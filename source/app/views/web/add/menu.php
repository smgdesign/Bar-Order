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
    <h1>Add menu item</h1>
    <form action="/web/add/menu" method="post" enctype="multipart/form-data">
        <input type="text" name="title" id="title" placeholder="Title" />
        <br />
        <textarea name="desc" placeholder="Description"></textarea>
        <br />
        <input type="text" name="price" placeholder="Price" size="6" maxlength="6" style="width: 75px;"/>
        <br />
        <input type="file" name="icon" />
        <br />
        
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="hidden" name="location_id" value="<?php echo $locationID; ?>" />
        <input type="submit" name="add" value="Create" id="form_btn" />
    </form>
</div>