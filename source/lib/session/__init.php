<?php

/*
 * Copyright Invention Interactive Ltd. 2012
 * Project - TAP Biosystems - TAP Distributor Portal
 */

/**
 * the handler for sessions
 *
 * @author Richard
 */
class session {
    protected $self = array();
    public function __construct($sessionName) {
        if (!debug) {
            session_set_cookie_params(0, '/', 'hygiene-resource.com', true, true);
        } else {
            session_set_cookie_params(0);
        }
        session_name($sessionName);
        session_start();
    }
    public function __get($name = null) {
        return $this->self[$name];
    }
    public function __set($name, $value) {
        $this->self[$name] = $value;
    }
    public function addVar($name, $value) {
        $_SESSION[$name] = $value;
    }
    public function getVar($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        } else {
            return NULL;
        }
    }
    public function destroySession() {
        return session_destroy();
    }
}
?>
