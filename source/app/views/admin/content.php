<script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">

    tinymce.init({
        plugins: "code",
        toolbar: "undo redo | bold italic | code",
        menubar: false,
        statusbar: false,
        force_p_newlines : false,
        forced_root_block : "",
        height: 30,
        selector: '#admin_page_name',
        valid_elements: "em,strong,span[*]",
        invalid_elements: "p"
    });
    tinymce.init({
        plugins: "image template responsivefilemanager code link",
        toolbar: "undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | template responsivefilemanager | code",
        image_advtab: true,
        convert_urls: false,
        selector: "#admin_page_content",
        height:300,
        content_css : "/css/admin.edit.css?" + new Date().getTime(),
        external_filemanager_path:"/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "/filemanager/plugin.min.js"},
        /* TEMPLATES */
        templates: [
            {
                title: 'Green Dotted Line',
                url: '/templates/green_hr.html',
                description: 'Add a horizontal green dotted line.'
            },
            {
                title: 'Modal window on-click',
                url: '/templates/modal.html',
                description: 'Code for adding a modal window on a click event.'
            },
            {
                title: 'Tooltip on-hover message',
                url: '/templates/tooltips.html',
                description: 'Code for adding a small popup message to be shown on hover.'
            },
            {
                title: 'Video Modal',
                url: '/templates/videos.html',
                description: 'Code for adding a popup video on a click event.'
            },
            {
                title: 'Module: standalone Key Point',
                url: '/templates/keypoint.html',
                description: 'A standalone Key Point snippet.'
            },
            {
                title: 'Module: 2 columns of equal width',
                url: '/templates/module_two_columns.html',
                description: 'A module content layout with 2 columns of equal width.'
            },
            {
                title: 'Module: 2 columns, larger left',
                url: '/templates/module_two_columns_weighted_left.html',
                description: 'A module content layout with 2 columns, where the left hand column is wider than the right.'
            },
            {
                title: 'Module: 2 columns, larger left with key point',
                url: '/templates/module_two_columns_weighted_left_keypoint.html',
                description: 'A module content layout with 2 columns, where the left hand column is wider than the right. In the left hand column is a Key Point section.'
            },
            {
                title: 'Module: 2 columns, larger right',
                url: '/templates/module_two_columns_weighted_right.html',
                description: 'A module content layout with 2 columns, where the right hand column is wider than the left.'
            },
            {
                title: 'Module: 2 columns, larger right with key point',
                url: '/templates/module_two_columns_weighted_right_keypoint.html',
                description: 'A module content layout with 2 columns, where the right hand column is wider than the left. In the right hand column is a Key Point section.'
            }
        ]
    });
     
</script>


<p class="floatright"><a style="margin:0 0 0 10px;" class="add_button" href="/admin/index">Back to main administration</a></p>
<h1>Edit Page</h1>
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

<form action="/admin/content/edit" method="post">
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />

    <table class="admin_table">
        <tr>
            <td><label for="admin_page_content">Page Title</label></td>
            <td class="td_labelled_input"><textarea name="title" id="admin_page_name"><?php echo (array_key_exists('title', $data)) ? $data['title'] : ''; ?></textarea><!--input type="text" name="title" id="admin_page_name" value="<?php echo (array_key_exists('title', $data)) ? $data['title'] : ''; ?>" /--></td>
        </tr>
        <tr>
            <td colspan="2"><label for="admin_page_content">Page Content</label></td>
        </tr>
        <tr>
            <td colspan="2"><textarea name="html" id="admin_page_content"><?php echo (array_key_exists('html', $data)) ? $data['html'] : ''; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2" class="td_form_submit"><input type="submit" name="editPage" class="add_button" value="<?php echo ucwords($mode).' Page'; ?>" /></td>
        </tr>
    </table>
    
</form>
    