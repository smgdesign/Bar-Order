<?php
/**
 * Dettol / Lysol - 2013
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        
        <!-- CSS -->
        <link href="/css/fonts.css" type="text/css" rel="stylesheet" />
        <link href="/css/main.css" type="text/css" rel="stylesheet" />
        <link href="/css/admin.main.css" type="text/css" rel="stylesheet" />
        
        <!-- JS -->
        <script src="/js/jquery-1.10.2.min.js"></script>
        <script src="/js/admin.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            $("#header li").hover(function() {
                $(this).siblings().find('ul').hide();
                $("ul", this).show();
            }, function() {
                $("ul", this).hide();
            });
        });
        </script>
        <?php
        foreach ($this->headIncludes as $include) {
            echo $include;
        }
        ?>
		</head>
    <body>
       <!-- Wrapper -->
        <div id="wrapper">
            
            
            <div id="ribbon-wrapper">
                <!-- min height -->
                <div class="min-height height-350"></div>
                
                <!-- Header -->
                <div id="header"></div>

                <!-- Main -->
                <div id="main">
                    <div class="min-height height-350"></div>
                    <div id="content">
            

         
    