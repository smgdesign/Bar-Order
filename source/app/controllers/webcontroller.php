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
    public function test() {
        global $common;
        $data = $common->curlPOSTRequest(
            'http://bar.app/api/__init',
            array(
                'UUID'=>'1701F9A0-767F-441D-9E3C-637A4E07836B',
                'sync'=>true,
                'QR'=>'1:2:1'
            ),
            'json'
        );
        print_r($data);
        $common->isPage = false;
    }
}
