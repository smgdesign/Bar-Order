<?php


?>

<h1>Administration</h1>
<p>From here you can add, remove and modify modules and content on the training portal.</p>

<h2>Navigation</h2>
<div class="clearer">
    <div class="tip">
        <div class="tip_inner">
            <h4>Tip</h4>
            <p>Click Add child item to add a sub item</p>
        </div>
    </div>
    <div class="tip">
        <div class="tip_inner">
            <h4>Tip</h4>
            <p>Enter a link and a title for an item</p>
        </div>
    </div>
</div>
<ul id="adminMenu">
<?php
$nav = $this->Admin->getNav();
if (!empty($nav)) {
    echo $this->Admin->displayNav($nav);
}
?>
</ul>
<span><a class="add_button saveNav" href="#">Save Navigation</a></span>
<div class="clearme"></div>
<h2>Modules</h2>
<div class="clearer">
    <div class="tip">
        <div class="tip_inner">
            <h4>Tip</h4>
            <p>Click and drag modules to re-order them.</p>
        </div>
    </div>
    <div class="tip">
        <div class="tip_inner">
            <h4>Tip</h4>
            <p>Click on a module name to expand it's details.</p>
        </div>
    </div>
</div>
<span><a class="add_button" href="/admin/module/edit">Add new Module</a></span>
<ol class="admin_module_list">
    <?php
        foreach ($this->Admin->getModules() as $module) {
            //print_r($module);
    ?>
    <li>
        <table class="admin_module hide_body">
            <thead>
                <tr class="admin_module_title">
                    <td class="tbl_reorder"><img title="Reorder" src="/img/reorder.png" /></td>
                    <td class="module_name table_expander" colspan="3"><?php echo $module['title']; ?></td>
                    <td class="edit"><a href="/admin/module/edit/<?php echo $module['id'] ?>"><img title="Edit" src="/img/edit.png" /></a></td>
                    <td class="delete"><img title="Remove" src="/img/delete.png" /></td>
                </tr>
            </thead>
            <tbody>
                <tr class="admin_module_heading">
                    <td class="tbl_reorder" title="Page order">&nbsp;</td>
                    <td colspan="3">
                        Page name
                    </td>
                    <td colspan="2">
                        Modify
                    </td>
                </tr>
                <tr class="module_page">
                    <td class="tbl_reorder">...</td>
                    <td class="page_name" colspan="3">First Module Page</td>
                    <td class="edit">Edit</td>
                    <td class="delete">Delete</td>
                </tr>
                <tr class="admin_module_heading">
                    <td class="tbl_reorder" title="Question order">&nbsp;</td>
                    <td>
                        Question Code
                    </td>
                    <td>
                        Question Title
                    </td>
                    <td>
                        Question Type
                    </td>
                    <td>
                        Modify
                    </td>
                </tr>
                <tr class="module_questions">
                    <td class="tbl_reorder">...</td>
                    <td class="test_code">TEST1</td>
                    <td class="test_question">Test Question</td>
                    <td class="test_type">Multiple choice</td>
                    <td class="edit">Edit</td>
                    <td class="delete">Delete</td>
                </tr>
            </tbody>
        </table>
    </li>
    <?php } ?>
</ol>

<h2>Pages</h2>
<div class="clearer">
    <div class="tip">
        <div class="tip_inner">
            <h4>Tip</h4>
            <p>Click and drag pages to re-order them.</p>
        </div>
    </div>
    <div class="tip">
        <div class="tip_inner">
            <h4>Tip</h4>
            <p>Click on a page name to expand it's details.</p>
        </div>
    </div>
</div>
<span><a class="add_button" href="/admin/content/edit">Add new Page</a></span>
<ol class="admin_module_list">
    <?php
        foreach ($this->Admin->getContentPages() as $page) {
    ?>
    <li>
        <table class="admin_module hide_body">
            <thead>
                <tr class="admin_module_title">
                    <td class="tbl_reorder"><img title="Reorder" src="/img/reorder.png" /></td>
                    <td class="page_name table_expander" colspan="3"><?php echo $page['title']; ?></td>
                    <td class="edit"><a href="/admin/content/edit/<?php echo $page['id'] ?>"><img title="Edit" src="/img/edit.png" /></a></td>
                    <td class="delete"><img title="Remove" src="/img/delete.png" /></td>
                </tr>
            </thead>
            <tbody>
                <tr class="admin_module_heading">
                    <td colspan="5">
                        <?php echo $page['html'] ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </li>
    <?php } ?>
</ol>
<h2>Resources</h2>
<div class="clearer">
    <div class="tip">
        <div class="tip_inner">
            <h4>Tip</h4>
            <p>Click on a resource to expand it's details.</p>
        </div>
    </div>
</div>
<span><a class="add_button" href="/admin/resource/edit">Add new Resource</a></span>

<ol class="admin_module_list">
    <?php
        foreach ($this->Admin->getResources() as $resource) {
    ?>
    <li>
        <table class="admin_module hide_body">
            <thead>
                <tr class="admin_module_title">
                    <td class="resource_name table_expander" colspan="3"><?php echo $resource['name']; ?></td>
                    <td class="edit"><a href="/admin/resource/edit/<?php echo $resource['id']; ?>"><img title="Edit" src="/img/edit.png" /></a></td>
                    <td class="delete"><img title="Remove" src="/img/delete.png" /></td>
                </tr>
            </thead>
            <tbody>
                <tr class="admin_module_heading">
                    <td colspan="4">
                        <img src="<?php echo $resource['thumb']; ?>" />
                        <p><?php echo $resource['resource_title']; ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </li>
    <?php } ?>
</ol>
<div class="clearme"></div>
<h2>Resource Categories</h2>
<div class="clearer">
    <div class="tip">
        <div class="tip_inner">
            <h4>Tip</h4>
            <p>Click on a resource category to expand it's description.</p>
        </div>
    </div>
</div>
<span><a class="add_button" href="/admin/resource/category">Add new Resource Category</a></span>

<ol class="admin_module_list">
    <?php
        foreach ($this->Admin->getResourceCategories() as $resource) {
    ?>
    <li>
        <table class="admin_module hide_body">
            <thead>
                <tr class="admin_module_title">
                    <td class="resource_name table_expander" colspan="3"><?php echo $resource['title']; ?></td>
                    <td class="edit"><a href="/admin/resource/category/<?php echo $resource['id']; ?>"><img title="Edit" src="/img/edit.png" /></a></td>
                    <td class="delete"><img title="Remove" src="/img/delete.png" /></td>
                </tr>
            </thead>
            <tbody>
                <tr class="admin_module_heading">
                    <td colspan="4">
                        <p><?php echo $resource['description']; ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </li>
    <?php } ?>
</ol>
<script>
$(document).ready(function(){
    $('.table_expander').on('click', function(){
        $(this).parents('table:first').find('tbody').toggle(250);
    });
    $("#adminMenu").on('click', '.addNav', function() {
        var newNav = $(this).parent().clone();
        newNav.find('input').each(function() {
            if ($(this).is("[type='text']")) {
                $(this).val('');
            }
        });
        if (!$(this).parent().next().is('ul')) {
            $(this).parent().after('<ul />');
        }
        $(this).parent().next('ul').append($("<li />").html(newNav));
    });
    $("#adminMenu").on('click', '.removeNav', function() {
        $(this).parent().parent().empty().remove();
    });
    $("a.saveNav").click(function(ev) {
        ev.preventDefault();
        var obj = {};
        $("#adminMenu").find("input[type='text']").each(function() {
            var c = $(this).parents('ul').length,
                arrStr = '';
            $(this).parents('ul').each(function() {
                if ($(this).parent('li').length > 0) {
                    arrStr += '['+$(this).parent().index()+']';
                }
            });
            arrStr += '['+$(this).parent().parent().index()+']['+$(this).attr('name')+']';
            obj[$(this).attr('name')+arrStr] = $(this).val();
        });
        obj['submitted'] = true;
        $.ajax({
            'url': '/admin/updateNav',
            'type': 'post',
            'data': obj,
            'dataType': 'json',
            'success': function(i) {

            }
        });
    });
});
</script>
