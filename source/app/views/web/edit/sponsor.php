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
    <form action="/web/edit/sponsor" method="post" enctype="multipart/form-data">
        <h2><?php echo (($action == 'add') ? 'Add' : 'Edit'); ?> sponsor</h2>
        <input type="text" name="link" id="link" placeholder="URL Link" <?php echo (isset($info['link'])) ? 'value="'.$info['link'].'"' : ''; ?> />
        <div class="clear"></div>
        <?php
        if (isset($info['img'])) {
            echo '<img src="'.$info['img'].'" />';
        }
        ?>
        <input type="file" name="file" />
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="hidden" name="sponsor_id" value="<?php echo (isset($id) && $id != 0) ? $id : 0; ?>" />
        <?php if ($venue_id != 'select' || $id != 0) {
        ?>
        <input type="hidden" name="venue_id" value="<?php echo $venue_id; ?>" />
        <?php
        } else if ($venue_id == 'select') {
        ?>
        <div class="clear"></div>
        <select name="venue_id">
            <?php
            echo $this->selectList($this->Web->venue('list', true));
            ?>
        </select>
        <?php
        }
        ?>
        <div class="clear"></div>
        <input type="submit" name="add" value="<?php echo ($action == 'add') ? 'Create' : 'Update'; ?>" id="form_btn" />
        <?php if (isset($id) && $id != 0) {
            echo '<input type="button" name="delete" value="Delete" class="form_btn" />';
        }
        ?>
    </form>
</div>
<script type="text/javascript">
    $(function() {
        $("input[name='delete']").click(function() {
            var c = confirm('Are you sure you wish to delete this item?');
            if (c) {
                $.ajax({
                    'url': '/web/delete/sponsor/'+<?php echo (isset($id)) ? $id : 0; ?>,
                    'success': function() {
                        window.location.href = "/";
                    }
                });
            }
        });
    });
</script>