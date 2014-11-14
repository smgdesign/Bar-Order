<div id="form_box">
    <?php
    if (isset($errors)) {
        foreach ($errors as $err) {
            ?>
    <p class="error"><?php echo $err; ?></p>
    <?php
        }
    }
    if (isset($data)) {
        if ($data['success']) {
            if ($action == 'edit') {
            ?>
    <p class="success">The item was successfully updated</p>
    <?php
            } else {
            ?>
    <p class="success">The item was successfully added</p>
    <?php
            }
        } else {
            ?>
    <p class="error">There was an error adding the item</p>
    <?php
        }
    }
    ?>
    <form action="/web/edit/menu" method="post" enctype="multipart/form-data">
        <h2><?php echo (($action == 'add') ? 'Add' : 'Edit'); ?> menu item</h2>
        <div class="col">
            <input type="text" name="title" id="title" placeholder="Title" <?php echo (isset($info['title'])) ? 'value="'.$info['title'].'"' : ''; ?> />
            <input type="text" name="price" placeholder="Price" size="6" maxlength="6" style="width: 75px;"  <?php echo (isset($info['price'])) ? 'value="'.$info['price'].'"' : ''; ?> />
        </div>
        <div class="col">
            <textarea name="desc" placeholder="Description"><?php echo (isset($info['desc'])) ? $info['desc'] : ''; ?></textarea>
            <?php
            if (isset($info['icon'])) {
                echo '<img src="'.$info['icon'].'" width="400" />';
            }
            ?>
            <input type="file" name="icon" />
        </div>
        <div class="col">
            <h2>Ingredients</h2>
            <ul class="ingredient_list">
            <?php
            if (isset($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    ?>
                <li class="ingredient">
                    <input type="checkbox" name="ingredient[<?php echo $ingredient['id']; ?>]" id="ingredient_<?php echo $ingredient['id']; ?>" value="1" <?php echo (isset($info['ingredients']) && array_key_exists($ingredient['id'], $info['ingredients']) !== false) ? 'checked="checked"' : ''; ?> />
                    <label for="ingredient_<?php echo $ingredient['id']; ?>" title="<?php echo $ingredient['desc']; ?>"><?php echo $ingredient['title']; ?></label>
                    <input type="checkbox" name="ingredient_req[<?php echo $ingredient['id']; ?>]" id="ingredient_req_<?php echo $ingredient['id']; ?>" value="1" <?php echo (isset($info['ingredients']) && array_key_exists($ingredient['id'], $info['ingredients']) !== false && $info['ingredients'][$ingredient['id']] == 1) ? 'checked="checked"' : ''; ?> />
                    <label for="ingredient_req_<?php echo $ingredient['id']; ?>" title="Required"><small>Required?</small></label>
                </li>
                <?php
                }
            }
            ?>
                <li class="add_new">Add new ingredient</li>
            </ul>
        </div>
        <div class="col">
            <h2>Categories</h2>
            <ul class="category_list">
            <?php
            if (isset($categories)) {
                foreach ($categories as $category) {
                    ?>
                <li class="category">
                    <input type="checkbox" name="category[<?php echo $category['id']; ?>]" id="category_<?php echo $category['id']; ?>" value="1" <?php echo (isset($info['categories']) && array_key_exists($category['id'], $info['categories']) !== false) ? 'checked="checked"' : ''; ?> />
                    <label for="category_<?php echo $category['id']; ?>" title="<?php echo $category['desc']; ?>"><?php echo $category['title']; ?></label>
                    <input type="radio" name="category_pri[<?php echo $category['id']; ?>]" id="category_pri_<?php echo $category['id']; ?>" value="1" <?php echo (isset($info['categories']) && array_key_exists($category['id'], $info['categories']) !== false && $info['categories'][$category['id']] == 1) ? 'checked="checked"' : ''; ?> />
                    <label for="category_pri_<?php echo $category['id']; ?>" title="Required"><small>Primary</small></label>
                </li>
                <?php
                }
            }
            ?>
                <li class="add_new">Add new category</li>
            </ul>
            <input type="hidden" name="submitted" value="TRUE" />
            <input type="hidden" name="menu_id" value="<?php echo (isset($id) && $id != 0) ? $id : 0; ?>" />
        </div>
        <div class="col">
            <label for="location_id">Location:</label>
            <select name="location_id">
                <?php
                $venues = $this->Web->location('list');
                
                foreach ($venues as $venue) {
                    echo '<optgroup label="'.$venue['venue_title'].'">';
                    foreach ($venue['locations'] as $key=>$location) {
                        echo '<option value="'.$key.'" '.(($key == $locationID) ? 'selected="selected"' : '').'>'.$location.'</option>';
                    }
                    echo '</optgroup>';
                }
                ?>
            </select>
        </div>
        <input type="submit" name="add" value="<?php echo ($action == 'add') ? 'Create' : 'Update'; ?>" id="form_btn" />
        <?php if (isset($id) && $id != 0) {
            echo '<input type="button" name="delete" value="Delete" class="form_btn" />';
        }
        ?>
    </form>
</div>
<script type="text/javascript">
    $(function() {
        var inc = 0;
        $(".add_new", ".ingredient_list").click(function() {
            $(this).before('<li class="ingredient"><input type="checkbox" name="ingredient[-1]['+inc+']" value="1" checked="checked" /><input type="text" name="ingredient_name['+inc+']" placeholder="Ingredient" /><br /><textarea name="ingredient_desc['+inc+']" placeholder="Description"></textarea><input type="checkbox" name="ingredient_req['+inc+']" id="ingredient_req_'+inc+'" value="1" /><label for="ingredient_req_'+inc+'" title="Required"><small>Required?</small></label></li>');
            inc++;
        });
        var incCat = 0;
        $(".add_new", ".category_list").click(function() {
            $(this).before('<li class="category"><input type="checkbox" name="category[-1]['+incCat+']" value="1" checked="checked" /><input type="text" name="category_name['+incCat+']" placeholder="Category" /><br /><textarea name="category_desc['+incCat+']" placeholder="Description"></textarea><input type="radio" name="categpry_pri['+incCat+']" id="categpry_pri_'+incCat+'" value="1" /><label for="categpry_pri_'+incCat+'" title="Required"><small>Primary</small></label></li>');
            incCat++;
        });
        $("input[name='delete']").click(function() {
            var c = confirm('Are you sure you wish to delete this item?');
            if (c) {
                $.ajax({
                    'url': '/web/delete/menu/'+<?php echo (isset($id)) ? $id : 0; ?>,
                    'success': function() {
                        window.location.href = "/";
                    }
                });
            }
        });
    });
</script>