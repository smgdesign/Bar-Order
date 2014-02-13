<?php

/**
 * Dettol / Lysol - 2013
 */
class AuthController extends Controller {
    public function __construct($model, $controller, $action) {
        global $common;
        parent::__construct($model, $controller, $action);
        $this->isJSON = false;
        $this->_template->xhr = false;
        $common->isPage = true;
    }
    public function login($prevURL='', $msg='') {
        global $auth, $common;
        $this->set('title', 'Login');
        if (!empty($msg)) {
            $this->set('msg', $msg);
        }
        if (!empty($prevURL)) {
            $this->set('prevURL', $prevURL);
        }
        if (!is_null($common->getParam('submitted'))) {
            $this->set('prevURL', $common->getParam('prevURL'));
            //$common->isPage = false;
            $loginScript = $auth->login($common->getParam('username'), $common->getParam('password'), $common->getParam('prevURL'));
            $this->set('errors', $loginScript);
        }
    }
    public function register($locationID=0) {
        global $auth, $common;
        $this->set('title', 'Register');
        if ($locationID != 0) {
            $this->set('locationID', $locationID);
        }
        if (!is_null($common->getParam('submitted'))) {
            $this->set('errors', $auth->register());
        }
    }
    public function logout() {
        global $auth;
        $auth->logout();
    }
    public function checkEmail() {
        global $common;
        $this->level = 1;
        $common->isPage = false;
        $this->isJSON = true;
        $this->json = $this->Auth->checkEmail();
    }
    public function checkUsername() {
        global $common;
        $this->level = 1;
        $common->isPage = false;
        $this->isJSON = true;
        $this->json = $this->Auth->checkUsername();
    }
    public function pending() {
        $content = $this->Auth->getContentByID(13);
        if (count($content) > 0) {
            $this->set('title', $content['title']);
            $this->set('content', $content['html']);
        }
    }
    public function password($prevURL='/') {
        global $auth, $common, $session;
        $content = $this->Auth->getContentByID(14);
        if (count($content) > 0) {
            $this->set('title', $content['title']);
            $this->set('content', $content['html']);
            $this->set('prevURL', $prevURL);
        } else {
            $this->set('title', 'Change your password');
            $this->set('content', '');
            $this->set('prevURL', $prevURL);
        }
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                $this->set('errors', $auth->changePassword());
            } else {
                $this->set('errors', array('Form validation error'));
            }
            $this->set('prevURL', $common->getParam('nextURL'));
        }
        $session->addVar('form-token', uniqid(mt_rand(), true));
        $this->set('csrf', $session->getVar('form-token'));
    }
    public function verify($id=0) {
        global $auth, $common, $session;
        $this->level = 2;
        if (!$this->checkContinue()) return;
        $content = $this->Auth->getContentByID(15);
        if (count($content) > 0) {
            $this->set('title', $content['title']);
            $this->set('content', $content['html']);
        } else {
            $this->set('title', 'Verify user');
            $this->set('content', '');
        }
        if ($id != 0) {
            $user = $this->Auth->getUser($id);
            if (count($user) > 0) {
                $this->set('user', $user);
            }
        }
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                if (is_null($common->getParam('deny'))) {
                    $this->set('verified', $auth->verify($common->getParam('id')));
                } else {
                    $common->isPage = false;
                    $this->isJSON = true;
                    $auth->verifyDeny($common->getParam('id'));
                }
            } else {
                $this->set('errors', array('Form validation error'));
            }
        }
        $session->addVar('form-token', uniqid(mt_rand(), true));
        $this->set('csrf', $session->getVar('form-token'));
    }
    public function forgot($step=0) {
        global $auth, $common, $session;
        $this->level = 0;
        $step = (int)$step;
        if ($step == 0) {
            $this->set('title', 'Forgotten password');
            $session->addVar('form-token', uniqid(mt_rand(), true));
            $this->set('csrf', $session->getVar('form-token'));
        } else {
            $common->isPage = false;
            $this->isJSON = true;
            if (!is_null($common->getParam('submitted'))) {
                if ($step == 1) {
                    $email = $common->getParam('email');
                    if (!is_null($email)) {
                        $userID = $this->Auth->getUserIDByEmail($email);
                        if (!is_null($userID)) {
                            $question = $this->Auth->getUser($userID);
                            $this->json = array('success'=>true, 'question'=>$question['secret_question'], 'user_id'=>$userID, 'csrf'=>$session->getVar('form-token'));
                        } else {
                            $this->json = array('success'=>false, 'errors'=>'Unfortunately your email address could not be found.', 'csrf'=>$session->getVar('form-token'));
                        }
                    } else {
                        $this->json = array('success'=>false, 'errors'=>'You must enter your email address.');
                    }
                } else if ($step == 2) {
                    $userID = $common->getParam('user_id');
                    $answer = $common->getParam('answer');
                    if (!is_null($userID)) {
                        if (!is_null($answer)) {
                            $reset = $auth->resetPassword($userID, $answer);
                            $this->json = $reset;
                        } else {
                            $this->json = array('success'=>false, 'errors'=>'Please enter your secret answer.');
                        }
                    } else {
                        $this->json = array('success'=>false, 'errors'=>'You appear to have reached this page in error.');
                    }
                }
            }
            
        }
    }
    public function reset($attempt=false, $step=0) {
        global $auth, $common, $session;
        if ($attempt) {
            $this->set('msg', 'You have logged in with a temporary password. Please enter your password again below. You will then be prompted to enter a new password');
        }
        $this->level = 0;
        $step = (int)$step;
        if ($step == 0) {
            $this->set('title', 'Reset password');
            $session->addVar('form-token', uniqid(mt_rand(), true));
            $this->set('csrf', $session->getVar('form-token'));
        } else {
            $common->isPage = false;
            $this->isJSON = true;
            if (!is_null($common->getParam('submitted'))) {
                if ($step == 1) {
                    $username = $common->getParam('username');
                    $password = $common->getParam('password');
                    if (!is_null($username) && !is_null($password)) {
                        $userID = $auth->loginTemp($username, $password);
                        if (!is_null($userID)) {
                            $this->json = array('success'=>true, 'user_id'=>$userID, 'csrf'=>$session->getVar('form-token'));
                        } else {
                            $this->json = array('success'=>false, 'errors'=>'Unfortunately your details could not be found.', 'csrf'=>$session->getVar('form-token'));
                        }
                    } else {
                        $this->json = array('success'=>false, 'errors'=>'You must enter your username and temporary password.');
                    }
                } else if ($step == 2) {
                    $userID = $common->getParam('user_id');
                    $password = $common->getParam('password');
                    $confPass = $common->getParam('conf-password');
                    if (!is_null($password) && $password == $confPass) {
                        if (!is_null($userID)) {
                            $reset = $auth->updatePassword($userID, $password);
                            $this->json = $reset;
                        } else {
                            $this->json = array('success'=>false, 'errors'=>'You appear to have reached this page in error.');
                        }
                    } else {
                        $this->json = array('success'=>false, 'errors'=>'Your passwords must match.');
                    }
                }
            }
        }
    }
}
