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
            ?>
    <p class="success">The item was successfully added</p>
    <?php
        } else {
            ?>
    <p class="error">There was an error adding the item</p>
    <?php
        }
    }
    ?>
    <form action="/web/add/menu" method="post" enctype="multipart/form-data">
        <h2>Add menu item</h2>
        <div class="col">
            <input type="text" name="title" id="title" placeholder="Title" />
            <input type="text" name="price" placeholder="Price" size="6" maxlength="6" style="width: 75px;"/>
        </div>
        <div class="col">
            <textarea name="desc" placeholder="Description"></textarea>
            <input type="file" name="icon" />
        </div>
        <div class="col">
            <h2>Ingredients</h2>
            <ul class="ingredient_list">
            <?php
            if (isset($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    ?>
                <li class="ingredient"><input type="checkbox" name="ingredient[<?php echo $ingredient['id']; ?>]" id="ingredient_<?php echo $ingredient['id']; ?>" value="1" /><label for="ingredient_<?php echo $ingredient['id']; ?>" title="<?php echo $ingredient['desc']; ?>"><?php echo $ingredient['title']; ?></label></li>
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
                <li class="category"><input type="checkbox" name="category[<?php echo $category['id']; ?>]" id="category_<?php echo $category['id']; ?>" value="1" /><label for="category_<?php echo $category['id']; ?>" title="<?php echo $category['desc']; ?>"><?php echo $category['title']; ?></label></li>
                <?php
                }
            }
            ?>
                <li class="add_new">Add new category</li>
            </ul>
            <input type="hidden" name="submitted" value="TRUE" />
            <input type="hidden" name="location_id" value="<?php echo $locationID; ?>" />
            <input type="submit" name="add" value="Create" id="form_btn" />
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function() {
        var inc = 0;
        $(".add_new", ".ingredient_list").click(function() {
            $(this).before('<li class="ingredient"><input type="checkbox" name="ingredient[-1]['+inc+']" value="1" checked="checked" /><input type="text" name="ingredient_name['+inc+']" placeholder="Ingredient" /><br /><textarea name="ingredient_desc['+inc+']" placeholder="Description"></textarea></li>');
            inc++;
        });
        var incCat = 0;
        $(".add_new", ".category_list").click(function() {
            $(this).before('<li class="category"><input type="checkbox" name="category[-1]['+incCat+']" value="1" checked="checked" /><input type="text" name="category_name['+incCat+']" placeholder="Category" /><br /><textarea name="category_desc['+incCat+']" placeholder="Description"></textarea></li>');
            incCat++;
        });
    });
</script>