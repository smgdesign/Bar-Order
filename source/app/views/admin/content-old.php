<?php
foreach ($resp as $item) {
    echo $item.'<br />';
}

?>
<form action="/admin/content/edit" method="post">
    <label for="title">Page Content Title</label>
    <input type="text" name="title" id="title" value="<?php echo (array_key_exists('title', $data)) ? $data['title'] : ''; ?>" />
    <br />
    <label for="html">Page HTML</label>
    <textarea name="html" id="html"><?php echo (array_key_exists('html', $data)) ? $data['html'] : ''; ?></textarea>
    <div class="clearme"></div>
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="submit" name="editContent" value="<?php echo $mode.' page content'; ?>" />
</form>