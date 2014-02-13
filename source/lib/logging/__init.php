<?php

/*
 * Dettol / Lysol - 2013
 */

/**
 * Description of logging
 *
 * @author Richard Wilson <richard.wilson@smgdesign.org>
 */
class logging {
    protected $errHead = "<div class=\"php_error\">";
    protected $errFoot = "</div>";
    public function developHandler($errno, $errstr, $errfile, $errline) {
        $resp = '';
        $exit = false;
        $errMsg = "$errno - $errstr\r\n
                   On line $errline in file $errfile";
        error_log($errMsg, 1, "richard.wilson@smgdesign.org");
        if (!(error_reporting() && $errno)) {
            return;
        }
        switch ($errno) {
            case E_USER_ERROR:
                $resp .= "<strong>FATAL ERROR: [$errno] $errstr</strong><br />\r\n";
                $resp .= "On line $errline in file $errfile";
                $resp .= ", PHP ".PHP_VERSION." (".PHP_OS.")<br />\r\n";
                $resp .= "Exiting...";
                $exit = true;
                break;
            case E_USER_WARNING:
                $resp .= "<strong>WARNING: [$errno] $errstr</strong><br />\r\n";
                $resp .= "On line $errline in file $errfile";
                break;
            case E_USER_NOTICE:
                $resp .= "<strong>NOTICE: [$errno] $errstr</strong><br />\r\n";
                $resp .= "On line $errline in file $errfile";
                break;
            default:
                $resp .= "<strong>[$errno] $errstr</strong><br />\r\n";
                $resp .= "On line $errline in file $errfile";
                break;
        }
        echo $this->errHead;
        echo $resp;
        echo $this->errFoot;
        if ($exit) {
            exit(1);
        }
        return true;
    }
    public function recordUsage($action='login', $data=array()) {
        global $db, $session;
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $user = $session->getVar('id');
        return $db->dbQuery("INSERT INTO tbl_access_log (user_id, action, data) VALUES ($user, '$action', '$data')");
    }
}

?>
