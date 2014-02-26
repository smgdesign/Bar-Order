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
    <form action="/web/edit/location" method="post" enctype="multipart/form-data">
        <h2><?php echo (($action == 'add') ? 'Add' : 'Edit'); ?> location</h2>
        <input type="text" name="title" id="title" placeholder="Title" <?php echo (isset($info['title'])) ? 'value="'.$info['title'].'"' : ''; ?> />
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="hidden" name="location_id" value="<?php echo (isset($id) && $id != 0) ? $id : 0; ?>" />
        <?php if (isset($id)) {
        ?>
        <input type="hidden" name="parent_id" value="<?php echo $venue_id; ?>" />
        <?php
        } else if ($venue_id == 'select') {
        ?>
        <select name="parent_id">
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
                    'url': '/web/delete/location/'+<?php echo $id; ?>,
                    'success': function() {
                        window.location.href = "/";
                    }
                });
            }
        });
    });
</script>