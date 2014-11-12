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
                $(".sub_menu ul").hide();
                $(this).parent().find('ul').show();
            });
            $("a.list").click(function(ev) {
                var tgt = $(this).prop('href').split('#')[1];
                ev.preventDefault();
                var $this = $(this);
                if ($this.next('select').length > 0) {
                    $this.next('select').empty().remove();
                } else {
                    $.ajax({
                        'url': '/web/'+tgt+'/list',
                        'dataType': 'json',
                        'success': function(i) {
                            if (i.status.code === 3) {
                                if (tgt === 'location') {
                                    var tmpObj = $('<select name="'+tgt+'_list" style="position: absolute;"></select>');
                                    tmpObj.append('<option value="">Please select one...</option>')
                                    var curVenue = 0;
                                    for (var x in i.data) {
                                        if (x !== curVenue) {
                                            curVenue = x;
                                            tmpObj.append('<optgroup label="'+i.data[x].venue_title+'"></optgroup>');
                                        }
                                        for (var y in i.data[x].locations) {
                                            $("optgroup:last", tmpObj).append('<option value="'+y+'">'+i.data[x].locations[y]+'</option>');
                                        }
                                    }
                                    $this.after(tmpObj);
                                    tmpObj.change(function() {
                                        if ($(this).val() !== '') {
                                            window.location = '/web/edit/'+tgt+'/'+$(this).val();
                                        }
                                    });
                                } else if (tgt === 'table') {
                                    var tmpObj = $('<select name="'+tgt+'_list" style="position: absolute;"></select>');
                                    tmpObj.append('<option value="">Please select one...</option>')
                                    for (var x in i.data) {
                                        var curLocation = 0;
                                        for (var y in i.data[x].locations) {
                                            if (y !== curLocation) {
                                                curLocation = y;
                                                $(tmpObj).append('<optgroup label="'+i.data[x].venue_title+' - '+i.data[x].locations[y].location_title+'"></optgroup>');
                                            }
                                            for (var z in i.data[x].locations[y].tables) {
                                                $("optgroup:last", tmpObj).append('<option value="'+z+'">'+i.data[x].locations[y].tables[z]+'</option>');
                                            }
                                            
                                        }
                                    }
                                    $this.after(tmpObj);
                                    tmpObj.change(function() {
                                        if ($(this).val() !== '') {
                                            window.location = '/web/edit/'+tgt+'/'+$(this).val();
                                        }
                                    });
                                } else if (tgt === 'sponsor') {
                                    var tmpObj = $('<select name="'+tgt+'_list" style="position: absolute;"></select>');
                                    tmpObj.append('<option value="">Please select one...</option>')
                                    var curVenue = 0;
                                    for (var x in i.data) {
                                        if (x !== curVenue) {
                                            curVenue = x;
                                            tmpObj.append('<optgroup label="'+i.data[x].venue_title+'"></optgroup>');
                                        }
                                        for (var y in i.data[x].sponsors) {
                                            $("optgroup:last", tmpObj).append('<option value="'+y+'">'+i.data[x].sponsors[y]+'</option>');
                                        }
                                    }
                                    $this.after(tmpObj);
                                    tmpObj.change(function() {
                                        if ($(this).val() !== '') {
                                            window.location = '/web/edit/'+tgt+'/'+$(this).val();
                                        }
                                    });
                                } else {
                                    var dataL = i.data.length;
                                    if (dataL > 0) {
                                        var tmpObj = $('<select name="'+tgt+'_list" style="position: absolute;"></select>');
                                        tmpObj.append('<option value="">Please select one...</option>')
                                        for (var x = 0; x < dataL; x++) {
                                            tmpObj.append('<option value="'+i.data[x].id+'">'+i.data[x].title+'</option>');
                                        }
                                        $this.after(tmpObj);
                                        tmpObj.change(function() {
                                            if ($(this).val() !== '') {
                                                window.location = '/web/edit/'+tgt+'/'+$(this).val();
                                            }
                                        });
                                    }
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
                        <li class="first"><a href="#menu" class="list">Edit menu item</a></li>
                        <li><a href="/web/edit/menu">Add menu item</a></li>
                    </ul>
                </li>
                <li class="sub_menu">
                    <a href="/web/items/venues">Venues</a>
                    <ul>
                        <li class="first"><a href="#venue" class="list">Edit venue</a></li>
                        <?php
                        if ($auth->level > 1) {
                        ?>
                        <li><a href="/web/edit/venue">Add venue</a></li>
                        <li><a href="#location" class="list">Edit location</a></li>
                        <?php
                        }
                        ?>
                        <li><a href="/web/edit/location">Add location</a></li>
                        <li><a href="#table" class="list">Edit table</a></li>
                        <li><a href="/web/edit/table">Add table</a></li>
                        <li><a href="#table" class="list">QR</a></li>
                    </ul>
                </li>
                <li class="sub_menu">
                    <a href="/web/items/sponsor">Sponsors</a>
                    <ul>
                        <li class="first"><a href="#sponsor" class="list">Edit sponsor</a></li>
                        <li><a href="/web/edit/sponsor">Add sponsor</a></li>
                    </ul>
                </li>
                <li class="sub_menu">
                    <a href="/web/items/sponsor">Sponsors</a>
                    <ul>
                        <li class="first"><a href="#sponsor" class="list">Edit sponsor</a></li>
                        <li><a href="/web/edit/sponsor">Add sponsor</a></li>
                    </ul>
                </li>
                <li><a href="/auth/logout">Logout</a></li>
                <?php
                }
                ?>
            </ul>
        </div>

         
    