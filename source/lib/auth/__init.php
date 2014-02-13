<?php

/**
 * Dettol / Lysol - 2013
 */

/**
 * Description of auth
 *
 * @author richard
 */
class authentication {
    var $loggedIn = false;
    var $level = 0;
    public function __construct() {
        global $session, $db, $common;
        require_once ROOT . DS . 'lib' . DS . '/enc/__init.php';
        $this->encrypt = new Bcrypt();
        if (!is_null($session->getVar('id')) && $session->getVar('user_agent') == md5($common->getParam('HTTP_USER_AGENT', 'server'))) {
            $cur = new DateTime();
            if ($session->getVar('last_action') < ($cur->getTimestamp() - 30*60)) {
                // log them out \\
                $this->logout('timeout');
            } else {
                $this->loggedIn = true;
                $this->level = $session->getVar('level');
                $session->addVar('last_action', $cur->getTimestamp());
            }
        }
    }
    public function login($username='', $pass='', $nextURL='') {
        global $common, $db, $session;
        if (empty($nextURL)) {
            $nextURL = '/';
        }
        if (!empty($username) && !empty($pass)) {
            $passSalt = $this->encrypt->generateSalt($username);
            $passEnc = $this->encrypt->generateHash($passSalt, $pass);
            $user = $db->dbResult($db->dbQuery("SELECT id, username, location_id, level FROM tbl_users WHERE username='$username' AND password='$passEnc'"));
            if ($user[1] > 0) {
                $session->addVar('id', $user[0][0]['id']);
                $session->addVar('username', $user[0][0]['username']);
                $session->addVar('location_id', $user[0][0]['location_id']);
                $session->addVar('level', $user[0][0]['level']);
                $cur = new DateTime();
                $session->addVar('last_action', $cur->getTimestamp());
                $session->addVar('user_agent', md5($common->getParam('HTTP_USER_AGENT', 'server')));
                header("Location: $nextURL");
                exit();
            } else {
                $err['system'][] = 'Unfortunately your details could not be found.';
            }
        } else {
            $common->isPage = true;
            $err = array('fields'=>array());
            if (empty($username)) {
                $err['fields'][] = 'Please enter a username.';
            }
            if (empty($pass)) {
                $err['fields'][] = 'Please enter a password.';
            }
        }
        return $err;
    }
    public function register() {
        global $common, $db;
        $err = array('fields'=>array());
        if (!$this->loggedIn) {
            $locationID = $common->getParam('location_id');
            if (!is_null($locationID)) {
                if (!is_null($common->getParam('submitted'))) {
                    $username = $common->getParam('username');
                    $pass = $common->getParam('password');
                    if (is_null($username)) {
                        $err['fields'][] = 'Please enter a username.';
                    } else {
                        $exists = $db->dbResult($db->dbQuery("SELECT id FROM tbl_users WHERE username='$username'"));
                        if ($exists[1] > 0) {
                            $err['fields'][] = 'Your username already exists.';
                        }
                    }
                    if (is_null($pass)) {
                        $err['fields'][] = 'Please enter a password.';
                    }
                    if ($pass != $common->getParam('conf-password')) {
                        $err['fields'][] = 'Your passwords must match.';
                    }
                    if (empty($err['fields'])) {
                        $passSalt = $this->encrypt->generateSalt($username);
                        $passEnc = $this->encrypt->generateHash($passSalt, $pass);
                        $ins = $db->dbQuery("INSERT INTO tbl_users (username, password, location_id, level) VALUES ('$username', '$passEnc', $locationID, 1)", 'id');
                        if (is_int($ins)) {
                            $this->login($username, $pass);
                        }
                    }
                }
            } else {
                $err['system'] = 'You appear to have followed an incorrect link.';
            }
        } else {
            $err['system'] = 'already logged in';
        }
        $common->isPage = true;
        return $err;
    }
    
    public function checkDevice() {
        global $common, $db, $session;
        $ipAddr = ip2long($common->getParam('REMOTE_ADDR', 'server'));
        $machineName = gethostbyaddr($common->getParam('REMOTE_ADDR', 'server'));
        if (is_null($session->getVar('UUID'))) {
            if (!is_null($common->getParam('UUID'))) {
                $session->addVar('UUID', $common->getParam('UUID'));
                $tbl = array('d'=>'tbl_device');
                $joins = array();
                $cols = array(
                    'd'=>array('id', 'name', 'last_sync')
                );
                $cond = array(
                    'd'=>array(
                        'join'=>'AND',
                        array(
                            'col'=>'UUID',
                            'operand'=>'=',
                            'value'=>"'{$common->getParam('UUID')}'"
                        )
                    )
                );
                $device = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                if ($device[1] > 0) {
                    $sync = new DateTime($device[0][0]['last_sync']);
                    if ($sync !== false) {
                        if (!is_null($common->getParam('sync'))) {
                            $tbl = array('c'=>'tbl_config');
                            $joins = array();
                            $cols = array(
                                'c'=>array('value')
                            );
                            $cond = array(
                                'c'=>array(
                                    'join'=>'AND',
                                    array(
                                        'col'=>'name',
                                        'operand'=>'=',
                                        'value'=>"'last_updated'"
                                    )
                                )
                            );
                            $dbsync = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                            if ($dbsync[1] > 0) {
                                $last = new DateTime($dbsync[0][0]['value']);
                                if ($last > $sync) {
                                    // means we need to resync \\
                                    return array('status'=>'exists', 'sync'=>true, 'device_id'=>$device[0][0]['id']);
                                } else {
                                    return array('status'=>'exists', 'sync'=>false, 'device_id'=>$device[0][0]['id']);
                                }
                            } else {
                                // now insert this in the db \\
                                $devID = $db->dbQuery("INSERT INTO tbl_device (name, ip_addr, UUID) VALUES ('$machineName', $ipAddr, '{$common->getParam('UUID')}')", 'id');
                                return array('status'=>'new', 'sync'=>true, 'device_id'=>$devID);
                            }
                        }
                    } else {
                        // now insert this in the db \\
                        $devID = $db->dbQuery("INSERT INTO tbl_device (name, ip_addr, UUID) VALUES ('$machineName', $ipAddr, '{$common->getParam('UUID')}')", 'id');
                        return array('status'=>'new', 'sync'=>true, 'device_id'=>$devID);
                    }
                } else {
                    // now insert this in the db \\
                    $devID = $db->dbQuery("INSERT INTO tbl_device (name, ip_addr, UUID) VALUES ('$machineName', $ipAddr, '{$common->getParam('UUID')}')", 'id');
                    return array('status'=>'new', 'sync'=>true, 'device_id'=>$devID);
                }
            } else {
                return array('status'=>'unknown', 'sync'=>true);
            }
        } else {
            // means already exists \\
            return true;
        }
    }
    
    public function getDevice() {
        global $common, $db, $session;
        if (!is_null($session->getVar('UUID'))) {
            $UUID = $session->getVar('UUID');
        }
        if (!is_null($common->getParam('UUID'))) {
            $UUID = $common->getParam('UUID');
        }
        if (isset($UUID)) {
            $id = $db->dbResult($db->dbQuery("SELECT id FROM tbl_device WHERE UUID='$UUID'"));
            if ($id[1] > 0) {
                return $id[0][0]['id'];
            }
        }
        return false;
    }
    
    public function logout($msg='') {
        global $session;
        $session->destroySession();
        if (!empty($msg)) {
            header ("Location: /auth/login/$msg");
            exit();
        }
        header("Location: /auth/login");
        exit();
    }
    private function generatePassword($level=5,$length=10) {
	$chars[1] = "1234567890";
	$chars[2] = "abcdefghijklmnopqrstuvwxyz";
	$chars[3] = "0123456789abcdefghijkmnopqrstuvwxyz";
	$chars[4] = "0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ";
	$chars[5] = "0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ!@#$%^&*?=+-_";
	$i = 0;
	$str = "";
	while ($i<=$length) {
            $str .= $chars[$level][mt_rand(0,strlen($chars[$level])-1)];
            $i++;
	}
	return $str;
}
}

?>
