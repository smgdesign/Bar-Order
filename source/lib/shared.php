<?php

/**
 * Dettol / Lysol - 2013
 */
/** Autoload any classes that are required **/
 
function __autoload($className) {
    if (file_exists(ROOT . DS . 'lib' . DS . strtolower($className) . '.class.php')) {
        require_once(ROOT . DS . 'lib' . DS . strtolower($className) . '.class.php');
    } else if (file_exists(ROOT . DS . 'app' . DS . 'controllers' . DS . strtolower($className) . '.php')) {
        require_once(ROOT . DS . 'app' . DS . 'controllers' . DS . strtolower($className) . '.php');
    } else if (file_exists(ROOT . DS . 'app' . DS . 'models' . DS . strtolower($className) . '.php')) {
        require_once(ROOT . DS . 'app' . DS . 'models' . DS . strtolower($className) . '.php');
    } else {
        /* Error Generation Code Here */
    }
}
spl_autoload_register('__autoload');
$common->setReporting();
$common->removeMagicQuotes();
$common->unregisterGlobals();
/*header("Cache-Control: no-cache, no-store, must-revalidate, private");
header("Expires: 0");
header("Pragma: no-cache");*/
$common->callHook();