
<p class="floatright"><a style="margin:0 0 0 10px;" class="add_button" href="/admin">Back to main administration</a></p>
<?php
if ($type == 'edit') {
?>
<h1>Edit Resource</h1>
<?php
if(!empty($resp)){
?>
<div class="admin_error">
<?php   
    foreach ($resp as $item) {
        echo '<p>'.$item.'</p>';
    }
?>
</div>
<?php    
}
?>

<form action="/admin/resource/edit/<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <table>
        <tr>
            <td valign="top">
                <label for="admin_resource_category">Category</label>
            </td>
            <td class="td_labelled_input">
                <select name="category_id" id="admin_resource_category">
                    <?php
                    foreach ($cats as $cat) {
                        echo '<option value="'.$cat['id'].'" '.((array_key_exists('category_id', $data) && $data['category_id'] == $cat['id']) ? 'selected="selected"' : '').'>'.$cat['title'].'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="admin_resource_file">File</label></td>
            <td><?php echo (array_key_exists('file', $data)) ? '<strong>Current file: </strong>'.$data['file'] : ''; ?><input type="file" name="file" id="admin_resource_file" /></td>
        </tr>
        <tr>
            <td>
                <label for="admin_resource_name">Name</label>
            </td>
            <td class="td_labelled_input">
                <input type="text" name="name" id="admin_resource_name" value="<?php echo (array_key_exists('name', $data)) ? $data['name'] : ''; ?>" />
            </td>
        </tr>
        <tr>
            <td><label for="admin_resource_thumb">Thumbnail</label></td>
            <td><?php echo (array_key_exists('thumb', $data)) ? '<strong>Current file: </strong>'.$data['thumb'] : ''; ?><input type="file" name="thumb" id="admin_resource_thumb" /></td>
        </tr>

        <!-- submit -->
        <tr>
            <td colspan="2" class="td_form_submit"><input type="submit" name="editResource" class="add_button" value="<?php echo ucwords($mode).' Resource'; ?>" /></td>
        </tr>
    </table>
</form>
<?php
} else if ($type == 'category') {
    ?>
<h1>Resource Category</h1>
<?php
if(!empty($resp)){
?>
<div class="admin_error">
<?php   
    foreach ($resp as $item) {
        echo '<p>'.$item.'</p>';
    }
?>
</div>
<?php    
}
?>
<form action="/admin/resource/category/<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <table>
        <tr>
            <td valign="top">
                <label for="admin_category_title">Title</label>
            </td>
            <td class="td_labelled_input"><input type="text" name="title" id="admin_category_title" value="<?php echo (array_key_exists('title', $data)) ? $data['title'] : ''; ?>" /></td>
        </tr>
        <tr>
            <td><label for="admin_category_description">Description</label></td>
            <td><textarea name="description" id="admin_category_description"><?php echo (array_key_exists('description', $data)) ? $data['description'] : ''; ?></textarea></td>
        </tr>

        <!-- submit -->
        <tr>
            <td colspan="2" class="td_form_submit"><input type="submit" name="editCategory" class="add_button" value="<?php echo ucwords($mode).' Resource Category'; ?>" /></td>
        </tr>
    </table>
</form>
<?php
}
?>