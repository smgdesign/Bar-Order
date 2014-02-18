<?php

/**
 * Bar App - 2014
 */
class Template {
    var $statuses = array('Order Placed', 'In Progress', 'Serving', 'Completed', 'Problem with Order');
    protected $variables = array();
    protected $_controller;
    protected $_action;
    protected $_model;
    var $headIncludes = array();
    var $xhr = true;
    function __construct($controller,$action, $model) {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_model = $model;
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
    
    public function orderStatus($status) {
        if (isset($this->statuses[$status])) {
            return $this->statuses[$status];
        }
        return $this->statuses[0];
    }
    public function orderStatuses() {
        return $this->statuses;
    }
    public function selectList($list=array(), $sel='') {
        $output = '';
        if (!empty($list)) {
            foreach ($list as $val=>$item) {
                $output .= '<option value="'.$val.'" '.(($val == $sel) ? 'selected="selected"' : '').'>'.$item.'</option>';
            }
        }
        return $output;
    }
    public function radioList($name='', $list=array(), $sel='') {
        $output = '';
        if (!empty($list)) {
            $UID = uniqid('item_');
            $i = 0;
            foreach ($list as $val=>$item) {
                $output .= '<label for="'.$UID.'_'.$i.'">'.$item.'</label><input name="'.$name.'" type="radio" id="'.$UID.'_'.$i.'" value="'.$val.'" '.(($val == $sel) ? 'checked="checked"' : '').' />';
                $i++;
            }
        }
        return $output;
    }
}