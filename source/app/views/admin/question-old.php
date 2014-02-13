<?php
foreach ($resp as $item) {
    echo $item.'<br />';
}
if (isset($moduleID)) {
?>
<form action="/admin/question/edit/<?php echo $moduleID; ?>/<?php echo $id; ?>" method="post">
    <a href="/admin/question/edit/<?php echo $moduleID; ?>">Create New</a><br />
    <label for="code">Question code</label>
    <input type="text" name="code" id="code" value="<?php echo (array_key_exists('code', $data)) ? $data['code'] : ''; ?>" />
    <br />
    <label for="question">Question</label>
    <textarea name="question" id="question"><?php echo (array_key_exists('question', $data)) ? $data['question'] : ''; ?></textarea>
    <br />
    <label for="type">Question type</label>
    <select name="type_id" id="type">
        <?php
        foreach ($this->Admin->getQuestionTypes() as $type) {
            echo '<option value="'.$type['id'].'" '.((array_key_exists('type_id', $data) && $type['id'] == $data['type_id']) ? 'selected="selected"' : '').'>'.$type['title'].'</option>';
        }
        ?>
    </select>
    <div class="clearme"></div>
    <div id="answerHolder"></div>
    <div class="clearme"></div>
    <label for="feedback">Feedback</label>
    <textarea name="feedback" id="feedback"><?php echo (array_key_exists('feedback', $data)) ? $data['feedback'] : ''; ?></textarea>
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="hidden" name="module_id" value="<?php echo $moduleID; ?>" />
    <input type="submit" name="editQuestion" value="<?php echo $mode.' question'; ?>" />
</form>
<script type="text/javascript">
$(function() {
    $("select[name='type_id']").each(selType)
    $("select[name='type_id']").change(selType);
    function selType() {
        $.ajax({
            'url': '/admin/question_types/<?php echo $id; ?>/'+$(this).val()+'/<?php echo ((array_key_exists('sub_type_id', $data) ? $data['sub_type_id'] : '')); ?>',
            'success': function(html) {
                $("div#answerHolder").html(html);
            }
        });
    }
});  
</script>
<?php
}
?>