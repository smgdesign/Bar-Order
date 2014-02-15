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
        $this->_template->headIncludes[] = '<script type="text/javascript" src="/js/modal.popup.js"></script>';
        $this->_template->headIncludes[] = '<script type="text/javascript" src="/js/order.list.functions.js"></script>';
        if (!$this->checkWebContinue()) return;
        $this->set('title', 'Web Portal');
        $this->set('orders', $this->Web->orders('list'));
    }
    public function test() {
        global $common;
        $this->set('title', 'Test Section');
        $data = $common->curlPOSTRequest(
            'http://bar.app/api/__init',
            array(
                'api_key'=>'912ad19545884e051c467e19e51e9e8e',
                'UUID'=>'1701F9A0-767F-441D-9E3C-637A4E07836B',
                'sync'=>true,
                'QR'=>'1:2:1'
            ),
            'json'
        );
        $this->set('data', $data);
        if (!is_null($common->getParam('submitted'))) {
            $place = $common->curlPOSTRequest(
                'http://bar.app/api/order/place',
                array_merge(
                    array(
                        'api_key'=>'912ad19545884e051c467e19e51e9e8e',
                        'UUID'=>'1701F9A0-767F-441D-9E3C-637A4E07836B'
                    ),
                    $common->getAllParam()
                ),
                'test'
            );
            $this->set('order', $place);
        }
    }
    public function view($id='') {
        global $common;
        $this->_template->xhr = true;
        if (!empty($id)) {
            $this->set('order', $this->Web->orders('get', $id));
        } else {
            $this->set('error', 'You must specify an order');
        }
    }
}
