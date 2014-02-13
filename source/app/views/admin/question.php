
<script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    
    tinymce.init({
        plugins: "image responsivefilemanager code link",
        toolbar: "undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | responsivefilemanager | code",
        image_advtab: true,
        relative_urls: false,
        selector: "#admin_question_question,#admin_question_feedback",
        height:200,
        content_css : "/css/admin.edit.css?" + new Date().getTime(),
        external_filemanager_path:"/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "/filemanager/plugin.min.js"},

        /*file_browser_callback: function(field_name, url, type, win) { 
            var cmsURL = '/files/browser/'+type;
            tinyMCE.activeEditor.windowManager.open({
                file : cmsURL,
                title : 'File Browser',
                width : 650,
                height : 500,
                resizable : "yes",
                inline : "yes",
                close_previous : "no"
            }, {
                window : win,
                input : field_name
            });
            return false;
        },*/
        /* TEMPLATES 
        templates: [
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
        */
    });
     
</script>



<p class="floatright"><a class="add_button" href="/admin/module/edit/<?php echo $moduleID; ?>">Back to module</a><a style="margin:0 0 0 10px;" class="add_button" href="/admin/index">Back to main administration</a></p>
<h1>Edit Question</h1>
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

<form action="/admin/question/edit/<?php echo $moduleID; ?>/<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="hidden" name="module_id" value="<?php echo $moduleID; ?>" />
    <table>
        <tr>
            <td>
                <label for="admin_question_code">Question Code</label>
            </td>
            <td class="td_labelled_input">
                <input type="text" name="code" value="<?php echo (array_key_exists('code', $data)) ? $data['code'] : ''; ?>" id="admin_question_code" />
            </td>
        </tr>
        <tr>
            <td valign="top"><label for="admin_question_question">Question</label></td>
            <td><textarea name="question" id="admin_question_question"><?php echo (array_key_exists('question', $data)) ? $data['question'] : ''; ?></textarea></td>
        </tr>
        <tr>
            <td>
                <label for="admin_question_type">Question type</label>
            </td>
            <td class="td_labelled_input">
                <select name="type_id" value="<?php echo (array_key_exists('code', $data)) ? $data['code'] : ''; ?>" id="admin_question_type">
                <?php
                foreach ($this->Admin->getQuestionTypes() as $type) { 
                ?>
                    <option value="<?php echo $type['id'] ?>" <?php echo ((array_key_exists('type_id', $data) && $type['id'] == $data['type_id']) ? 'selected="selected"' : '') ?>><?php echo $type['title'] ?></option>
                <?php 
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <label>Answers</label>
            </td>
            <td id="question_answers">
                
            </td>
        </tr>
        <tr>
            <td valign="top">
                <label for="admin_question_feedback">Feedback</label>
            </td>
            <td class="td_labelled_input">
                <textarea name="feedback" id="admin_question_feedback"><?php echo (array_key_exists('feedback', $data)) ? $data['feedback'] : ''; ?></textarea>
            </td>
        </tr>

        <!-- submit -->
        <tr>
            <td colspan="2" class="td_form_submit"><input type="submit" name="editPage" class="add_button" value="<?php echo ucwords($mode).' Question'; ?>" /></td>
        </tr>
    </table>
</form>

<script type="text/javascript">
$(function() {
    $("#admin_question_type").change(selType);
    $("select[name='type_id']").each(selType);
    function selType() {
        $.ajax({
            'url': '/admin/question_types/<?php echo $id; ?>/'+$(this).val()+'/<?php echo ((array_key_exists('sub_type_id', $data) ? $data['sub_type_id'] : '')); ?>',
            'success': function(html) {
                $("#question_answers").html(html);
            }
        });
    }
});  
</script>