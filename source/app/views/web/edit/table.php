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
    <form action="/web/edit/table" method="post" enctype="multipart/form-data">
        <h2><?php echo (($action == 'add') ? 'Add' : 'Edit'); ?> table</h2>
        <input type="text" name="name" id="name" placeholder="Name" <?php echo (isset($info['name'])) ? 'value="'.$info['name'].'"' : ''; ?> />
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="hidden" name="table_id" value="<?php echo (isset($id) && $id != 0) ? $id : 0; ?>" />
        <?php if (isset($id)) {
        ?>
        <input type="hidden" name="location_id" value="<?php echo $location_id; ?>" />
        <?php
        } else if ($location_id == 'select') {
        ?>
        <select name="location_id">
            <?php
            $location = $this->Web->location('list');
            $curID = 0;
            foreach ($location as $venueID=>$loc) {
                if ($venueID !== $curID) {
                    $output .= '<optgroup label="'.$loc['venue_title'].'">';
                }
                foreach ($loc['locations'] as $locID=>$loca) {
                    $output .= '<option value="'.$locID.'">'.$loca.'</option>';
                }
                if ($locID !== $curID) {
                    $curID = $locID;
                    $output .= '</optgroup>';
                }
            }
            echo $output;
            ?>
        </select>
        <?php
        }
        ?>
        <div class="clear"></div>
        <input type="submit" name="add" value="<?php echo ($action == 'add') ? 'Create' : 'Update'; ?>" id="form_btn" />
        <?php if (isset($id) && $id != 0) {
            echo '<input type="button" name="delete" value="Delete" id="form_btn" />';
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
                    'url': '/web/delete/venue/'+<?php echo $id; ?>,
                    'success': function() {
                        window.location.href = "/";
                    }
                });
            }
        });
    });
</script>