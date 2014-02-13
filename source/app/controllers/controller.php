<?php
/**
 * Bar App - 2014
 */

class Controller {
    
    protected $_model;
    protected $_controller;
    protected $_action;
    protected $_template;
    protected $level = 0;
    var $isJSON = true;
    var $json = array();
    
    function __construct($model, $controller, $action) {
        global $common;
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_model = $model;
 
        $this->$model = new $model;
        $this->_template = new Template($controller,$action);
        $this->_template->$model = $this->$model;
        $common->isPage = false;
    }
 
    function set($name,$value) {
        $this->_template->set($name,$value);
    }
    
    function checkContinue() {
        global $session;
        if ($this->level > 0 && is_null($session->getVar('id')) && (is_null($session->getVar('level')) || $session->getVar('level') < $this->level)) {
            return false;
        }
        return true;
    }
    
    function checkWebContinue() {
        global $auth;
        if ($this->level <= $auth->level) return true;
        return false;
    }

    public function commands() {
        $methods = get_class_methods($this);
        $list = array();
        $hiddenMethods = array('commands', '__construct', 'set', 'checkContinue', 'checkWebContinue', '__destruct');
        foreach ($methods as $method) {
            if (array_search($method, $hiddenMethods) === false) {
                $r = new ReflectionMethod($this, $method);
                $params = $r->getParameters();
                $list[$method] = array();
                foreach ($params as $param) {
                    $list[$method][] = array(
                        $param->getName()=>array(
                            (($param->isOptional()) ? 'OPTIONAL' : 'REQUIRED'),
                            (($param->isOptional()) ? $param->getDefaultValue() : NULL)
                        )
                    );
                }
            }
        }
        $this->json = $list;
        
    }
    
    function __destruct() {
        global $auth, $common;
        $this->_template->{$this->_model}->_controller = $this;
        if ($this->level > $auth->level) {
            $curURL = '|'.str_replace('/', '|', $common->getParam('url', 'get'));
            header("Location: /auth/login/$curURL/permission");
            return false;
        }
        if ($this->isJSON) {
            header("Content-Type: text/javascript");
            if (!empty($this->json)) {
                echo json_encode($this->json);
            }
        }
        if ($common->isPage) {
            $this->_template->render();
        }
    }
         
}