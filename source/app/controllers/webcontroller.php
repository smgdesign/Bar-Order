<?php

/**
 * Dettol / Lysol - 2013
 */
class WebController extends Controller {
    public function __construct($model, $controller, $action) {
        global $common;
        parent::__construct($model, $controller, $action);
        $this->isJSON = false;
        $this->_template->xhr = false;
        $common->isPage = true;
    }
    public function index() {
        $this->level = 1;
        if (!$this->checkWebContinue()) return;
        $this->set('title', 'Web Portal');
        $this->set('orders', $this->Web->orders('list'));
    }
}
