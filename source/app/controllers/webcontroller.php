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
            'http://bar.smgdev.co.uk/api/__init',
            array(
                'api_key'=>'912ad19545884e051c467e19e51e9e8e',
                'UUID'=>'1701F9A0-767F-441D-9E3C-FETSR',
                'sync'=>true,
                'QR'=>'1:2:1',
                'override'=>true
            ),
            'json'
        );
        $this->set('data', $data);
        if (!is_null($common->getParam('submitted'))) {
            $place = $common->curlPOSTRequest(
                'http://bar.smgdev.co.uk/api/order/place',
                array_merge(
                    array(
                        'api_key'=>'912ad19545884e051c467e19e51e9e8e',
                        'UUID'=>'1701F9A0-767F-441D-9E3C-FETSR'
                    ),
                    $common->getAllParam()
                ),
                'json'
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
    public function add($mode='menu') {
        global $common, $session;
        $this->set('title', 'Add '.$mode);
        $this->set('mode', $mode);
        $this->set('locationID', $session->getVar('location_id'));
        $err = array();
        switch ($mode) {
            case "menu":
                if (!is_null($common->getParam('submitted'))) {
                    $data = array(
                        'response'=>array('tbl_menu'=>array('id'=>0)),
                        'tbl_menu'=>array('fields'=>array())
                    );
                    if (!is_null($common->getParam('title'))) {
                        $data['tbl_menu']['fields']['title'] = $common->getParam('title');
                    } else {
                        $err[] = 'Title is required';
                    }
                    if (!is_null($common->getParam('desc'))) {
                        $data['tbl_menu']['fields']['desc'] = $common->getParam('title');
                    }
                    if (!is_null($common->getParam('price'))) {
                        $data['tbl_menu']['fields']['price'] = number_format($common->getParam('price'), 2);
                    } else {
                        $err[] = 'Price is required';
                    }
                    if (empty($err)) {
                        $file = $common->getParam('icon', 'file');
                        if (!is_null($file['tmp_name'])) {
                            require_once ROOT . DS . 'lib' . DS . 'files' . DS . '__init.php';
                            $files = new files();
                            if ($files->createImageResource($file['name'], $file['tmp_name'])) {
                                $files->resizeImage(200, 200);
                                $files->saveImage(uploadDir.$file['name']);
                                $data['tbl_menu']['fields']['icon'] = $file['name'];
                            } else {
                                $err[] = 'An error occurred uploading the file';
                            }
                        }
                    }
                    $data['tbl_menu']['fields']['location_id'] = $common->getParam('location_id');
                    if (empty($err)) {
                        $this->set('data', \data\collection::buildQuery("INSERT", $data));
                    } else {
                        $this->set('error', $err);
                    }
                }
                break;
        }
        if (!empty($err)) {
            $this->set('error', $err);
        }
    }
}
