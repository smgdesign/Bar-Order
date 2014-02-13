<p class="floatright"><a style="margin:0 0 0 10px;" class="add_button" href="/admin/index">Back to main administration</a></p>
<h1>Edit Module</h1>
<form action="/admin/module/edit/<?php echo $id; ?>" method="post">
    <table class="admin_table">
        <tr>
            <td><label for="admin_page_content">Module Title</label></td>
            <td class="td_labelled_input"><input type="text" name="title" id="title" value="<?php echo (array_key_exists('title', $data)) ? $data['title'] : ''; ?>" /></td>
        </tr>
        <tr>
            <td colspan="2" class="td_form_submit"><input type="submit" name="editModule" class="add_button"  value="<?php echo ucwords($mode).' Module title'; ?>" /></td>
        </tr>
    </table>
    <input type="hidden" name="order" id="order" value="<?php echo (array_key_exists('ordering', $data)) ? $data['ordering'] : ''; ?>" />
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
</form>
        
<?php


foreach ($resp as $item) {
    echo $item.'<br />';
}

?>
<?php
    if ($id != 0) {
?>
    <h2>Module Pages</h2>

    <span><a class="add_button" href="/admin/page/edit/<?php echo $id; ?>">Add new Page</a></span>
    <ol class="admin_module_list">
        <?php
            foreach ($this->Admin->getPages($id) as $page) {
        ?>
        <li>
            <table class="admin_module hide_body">
                <thead>
                    <tr class="admin_module_title">
                        <td class="tbl_reorder"><img title="Reorder" src="/img/reorder.png" /></td>
                        <td class="page_name table_expander" colspan="3"><?php echo $page['title']; ?></td>
                        <td class="edit"><a href="/admin/page/edit/<?php echo $id.'/'.$page['id'] ?>"><img title="Edit" src="/img/edit.png" /></a></td>
                        <td class="delete"><img title="Remove" src="/img/delete.png" /></td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="admin_module_heading">
                        <td class="tbl_reorder" title="Page order">&nbsp;</td>
                        <td colspan="5">
                            Content
                        </td>
                    </tr>
                    <tr class="admin_module_heading">
                        <td class="tbl_reorder" title="Page order">&nbsp;</td>
                        <td colspan="5">
                            <pre>
                                <p>NOT FOUND</p>
                            </pre>
                        </td>
                    </tr>
                </tbody>
            </table>
        </li>
        <?php } ?>
    </ol>

    <span><a class="add_button" href="/admin/question/edit/<?php echo $id; ?>">Add new Question</a></span>
    <ol class="admin_module_list">
    <?php
        foreach ($this->Admin->getQuestions($id) as $question) {
    ?>
    <li>
        <table class="admin_module hide_body">
            <thead>
                <tr class="admin_module_title">
                    <td class="tbl_reorder"><img title="Reorder" src="/img/reorder.png" /></td>
                    <td class="module_name table_expander"><?php echo $question['code']; ?></td>
                    <td class="module_name table_expander" colspan="2"><?php echo $common->shorten($question['question'], 75); ?></td>
                    <td class="edit"><a href="/admin/question/edit/<?php echo $id.'/'.$question['id']; ?>"><img title="Edit" src="/img/edit.png" /></a></td>
                    <td class="delete"><img title="Remove" src="/img/delete.png" /></td>
                </tr>
            </thead>
            <tbody>
                <tr class="admin_module_heading">
                    <td class="tbl_reorder" title="Question order">&nbsp;</td>
                    <td>
                        Question Code
                    </td>
                    <td colspan="3">
                        Question Title
                    </td>
                    <td>
                        Question Type
                    </td>
                </tr>
                <tr class="module_questions">
                    <td class="tbl_reorder">...</td>
                    <td class="test_code"><?php echo $question['code']; ?></td>
                    <td colspan="3" class="test_question"><?php echo $question['question']; ?></td>
                    <td class="test_type"><?php echo $question['title']; ?></td>
                </tr>
            </tbody>
        </table>
    </li>
    <?php } ?>
</ol>
    
<?php } ?>



<!--form action="/admin/module/edit" method="post">
    <label for="title">Module Name</label>
    <input type="text" name="title" id="title" value="<?php echo (array_key_exists('title', $data)) ? $data['title'] : ''; ?>" />
    <br />
    <label for="order">Module Order</label>
    <input type="text" size="3" name="order" id="order" value="<?php echo (array_key_exists('ordering', $data)) ? $data['ordering'] : ''; ?>" />
    <br />
    <?php
    if ($id != 0) {
    ?>
    <h4>Module pages</h4>
    <ul>
        <?php
        foreach ($this->Admin->getPages($id) as $page) {
            echo '<li><a href="/admin/page/edit/'.$id.'/'.$page['id'].'">'.$page['title'].'</a></li>';
        }
        ?>
        <li><a href="/admin/page/edit/<?php echo $id; ?>">Create new</a></li>
    </ul>
    <h4>Module questions</h4>
    <ul>
        <?php
        foreach ($this->Admin->getQuestions($id) as $question) {
            echo '<li><a href="/admin/question/edit/'.$id.'/'.$question['id'].'">'.$question['code'].'</a></li>';
        }
        ?>
        <li><a href="/admin/question/edit/<?php echo $id; ?>">Create new</a></li>
    </ul>
    <?php
    }
    ?>
    <div class="clearme"></div>
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="submit" name="editModule" value="<?php echo $mode.' module'; ?>" />
</form-->