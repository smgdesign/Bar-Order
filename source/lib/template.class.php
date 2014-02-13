<?php

/**
 * Bar App - 2014
 */
class Template {
     
    protected $variables = array();
    protected $_controller;
    protected $_action;
    var $headIncludes = array();
    var $xhr = true;
    function __construct($controller,$action) {
        $this->_controller = $controller;
        $this->_action = $action;
    }
 
    /** Set Variables **/
 
    function set($name,$value) {
        $this->variables[$name] = $value;
    }
 
    /** Display Template **/
     
    function render() {
        global $auth, $common, $session;
        extract($this->variables);
        header('Content-Type:text/html; charset=UTF-8');
        if (!$this->xhr) {
            if (file_exists(ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'header.php')) {
                include (ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'header.php');
            } else {
                include (ROOT . DS . 'app' . DS . 'views' . DS . 'header.php');
            }
        }
        include (ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php');       
        if (!$this->xhr) {
            if (file_exists(ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'footer.php')) {
                include (ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'footer.php');
            } else {
                include (ROOT . DS . 'app' . DS . 'views' . DS . 'footer.php');
            }
        }
    }
}