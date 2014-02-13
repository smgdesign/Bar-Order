<?php
/**
 * Dettol / Lysol - 2013
 */
class Auth extends Model {
    public function checkUsername() {
        global $common, $db, $session;
        if ($common->getParam('form-token') == $session->getVar('form-token')) {
            $username = $common->getParam('username');
            if (!is_null($username)) {
                $check = $db->dbResult($db->dbQuery("SELECT id FROM tbl_users WHERE username='$username'"));
                if ($check[1] > 0) {
                    return array('success'=>true, 'available'=>false);
                }
                return array('success'=>true, 'available'=>true);
            }
            return array('success'=>false, 'error'=>'empty username');
        }
        return array('success'=>false, 'error'=>'CSRF error');
    }
}
?>
