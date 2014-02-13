<?php
namespace errors;

class codes {
    static $__NOTFOUND = array('code'=>1, 'message'=>'The item you have requested could not be found');
    static $__EMPTY = array('code'=>2, 'message'=>'The resource you have called upon is empty');
    static $__FOUND = array('code'=>3, 'message'=>'The item you have requested was found');
    static $__SUCCESS = array('code'=>4, 'message'=>'The action was successful');
    static $__ERROR = array('code'=>5, 'message'=>'An error occurred processing the last action');
}
?>
