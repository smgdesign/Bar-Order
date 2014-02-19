<?php
/**
 * SMG Design MVC Template 2014
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $title; ?></title>
        <link href="/css/styles.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="/js/jquery-2.1.0.min.js"></script>
        <?php
        foreach ($this->headIncludes as $include) {
            echo $include;
        }
        ?>
        <script type="text/javascript">
        $(document).ready(function() {
            $(".sub_menu > a").click(function(ev) {
                ev.preventDefault();
                $(this).parent().find('ul').show();
            });
            $("a.list_menu").click(function(ev) {
                ev.preventDefault();
                var $this = $(this);
                if ($this.next('select').length > 0) {
                    $this.next('select').empty().remove();
                } else {
                    $.ajax({
                        'url': '/web/menu/list',
                        'dataType': 'json',
                        'success': function(i) {
                            if (i.status.code === 3) {
                                var dataL = i.data.length;
                                if (dataL > 0) {
                                    var tmpObj = $('<select name="menu_list" style="position: absolute;"></select>');
                                    tmpObj.append('<option value="">Please select one...</option>')
                                    for (var x = 0; x < dataL; x++) {
                                        tmpObj.append('<option value="'+i.data[x].id+'">'+i.data[x].title+'</option>');
                                    }
                                    $this.after(tmpObj);
                                    tmpObj.change(function() {
                                        if ($(this).val() !== '') {
                                            window.location = '/web/edit/menu/'+$(this).val();
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            });
        });
        </script>
    </head>
    <body>
        <div id="head">
            <h1>Bar Order Portal</h1>
            <ul>
                <?php 
                if (!$auth->loggedIn) {
                ?>
                <li class="first"><a href="/auth/login">Login</a></li>
                <?php
                } else {
                ?>
                <li class="first">Logged in as <?php echo $session->getVar('username'); ?></li>
                <li><a href="/">View orders</a></li>
                <li class="sub_menu">
                    <a href="/web/items/menu">Menu items</a>
                    <ul>
                        <li class="first"><a href="/web/edit/menu" class="list_menu">Edit menu item</a></li>
                        <li><a href="/web/add/menu">Add menu item</a></li>
                    </ul>
                </li>
                <li><a href="/web/items/locations">Locations</a></li>
                <?php
                    if ($auth->level > 1) {
                        ?>
                <li><a href="/web/add/venue">Venues</a></li>
                <?php
                    }
                    ?>
                <li><a href="/auth/logout">Logout</a></li>
                <?php
                }
                ?>
            </ul>
        </div>

         
    