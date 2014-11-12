<?php

/**
 * Bar App - 2014
 */
require_once ROOT . DS . 'lib' . DS . 'db' . DS . '__init.php';
require_once ROOT . DS . 'lib' . DS . 'db' . DS . 'collection.php';
require_once ROOT . DS . 'lib' . DS . 'common' . DS . '__init.php';
require_once ROOT . DS . 'lib' . DS . 'auth'. DS . '__init.php';
require_once ROOT . DS . 'lib' . DS . 'session' . DS . '__init.php';
require_once ROOT . DS . 'lib' . DS . 'logging' . DS . '__init.php';

require_once ROOT . DS . 'lib' . DS . 'codes.php';

define('debug', 'develop');
define('uploadDir', $_SERVER['DOCUMENT_ROOT'].'/public/img/');
define('verbose', false);
$confArray = array();
$confArray['db'] = array();
$confArray['db']['user'] = 'smgdev_bar';
$confArray['db']['password'] = ';SPdEN%Tub&2';
$confArray['db']['host'] = '92.48.105.206';
$confArray['db']['name'] = 'smgdev_bar';

$db = new db($confArray['db']);
$common = new common();
$session = new session('BarApp');
$logging = new logging();
$auth = new authentication();
?>
