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
        $this->_template->headIncludes[] = '<script type="text/javascript" src="/js/modal.popup.js"></script>';
        $this->_template->headIncludes[] = '<script type="text/javascript" src="/js/order.list.functions.js"></script>';
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
        $this->level = 1;
        if (!$this->checkWebContinue()) return;
        $this->_template->xhr = true;
        if (!empty($id)) {
            $this->set('order', $this->Web->orders('get', $id));
        } else {
            $this->set('error', 'You must specify an order');
        }
    }
    public function add($mode='menu') {
        global $common, $session;
        $this->level = 1;
        if (!$this->checkWebContinue()) return;
        $this->set('title', 'Add '.$mode);
        $this->set('mode', $mode);
        $this->set('locationID', $session->getVar('location_id'));
        $err = array();
        switch ($mode) {
            case "menu":
                if (!is_null($common->getParam('submitted'))) {
                    $data = array(
                        'response'=>array(
                            'tbl_menu'=>array('id'=>0),
                            'tbl_ingredient'=>array(),
                            'tbl_category'=>array(),
                            'tbl_ingredient_hooks'=>array(),
                            'tbl_category_hooks'=>array()
                        ),
                        'tbl_menu'=>array('fields'=>array()),
                        'tbl_ingredient'=>array('rows'=>array()),
                        'tbl_category'=>array('rows'=>array()),
                        'tbl_ingredient_hooks'=>array('rows'=>array()),
                        'tbl_category_hooks'=>array('rows'=>array())
                    );
                    if (!is_null($common->getParam('title'))) {
                        $data['tbl_menu']['fields']['title'] = $common->getParam('title');
                    } else {
                        $err[] = 'Title is required';
                    }
                    if (!is_null($common->getParam('desc'))) {
                        $data['tbl_menu']['fields']['desc'] = $common->getParam('desc');
                    }
                    if (!is_null($common->getParam('price'))) {
                        $data['tbl_menu']['fields']['price'] = number_format($common->getParam('price'), 2);
                    } else {
                        $err[] = 'Price is required';
                    }
                    if (empty($err)) {
                        $file = $common->getParam('icon', 'file');
                        if (!empty($file['name'])) {
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
                    if (!is_null($common->getParam('ingredient'))) {
                        $ingredients = $common->getParam('ingredient');
                        $newIngredients = array('name'=>$common->getParam('ingredient_name'), 'desc'=>$common->getParam('ingredient_desc'));
                        if (is_array($ingredients)) {
                            foreach ($ingredients as $id=>$ingredient) {
                                if ((int)$id==-1) {
                                    // this is a new item \\
                                    foreach ($ingredient as $i=>$newIng) {
                                        if (is_array($newIngredients['name']) && is_array($newIngredients['desc'])) {
                                            if (isset($newIngredients['name'][$i])) {
                                                if (isset($newIngredients['desc'][$i])) {
                                                    $data['tbl_ingredient']['rows'][$i] = array(
                                                        'fields'=>array(
                                                            'title'=>$newIngredients['name'][$i],
                                                            'desc'=>$newIngredients['desc'][$i]
                                                        )
                                                    );
                                                    $data['tbl_ingredient_hooks']['rows'][] = array(
                                                        'fields'=>array(
                                                            'ingredient_id'=>&$data['response']['tbl_ingredient'][$i]['id'],
                                                            'menu_id'=>&$data['response']['tbl_menu']['id']
                                                        )
                                                    );
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $data['tbl_ingredient_hooks']['rows'][] = array(
                                        'fields'=>array(
                                            'ingredient_id'=>$id,
                                            'menu_id'=>&$data['response']['tbl_menu']['id']
                                        )
                                    );
                                }
                            }
                        }
                    }
                    if (!is_null($common->getParam('category'))) {
                        $categories = $common->getParam('category');
                        $newCategories = array('name'=>$common->getParam('category_name'), 'desc'=>$common->getParam('category_desc'));
                        if (is_array($categories)) {
                            foreach ($categories as $id=>$category) {
                                if ((int)$id==-1) {
                                    // this is a new item \\
                                    foreach ($category as $i=>$newCat) {
                                        if (is_array($newCategories['name']) && is_array($newCategories['desc'])) {
                                            if (isset($newCategories['name'][$i])) {
                                                if (isset($newCategories['desc'][$i])) {
                                                    $data['tbl_category']['rows'][$i] = array(
                                                        'fields'=>array(
                                                            'title'=>$newCategories['name'][$i],
                                                            'desc'=>$newCat['desc'][$i]
                                                        )
                                                    );
                                                    $data['tbl_category_hooks']['rows'][] = array(
                                                        'fields'=>array(
                                                            'cat_id'=>&$data['response']['tbl_category'][$i]['id'],
                                                            'menu_id'=>&$data['response']['tbl_menu']['id']
                                                        )
                                                    );
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $data['tbl_category_hooks']['rows'][] = array(
                                        'fields'=>array(
                                            'cat_id'=>$id,
                                            'menu_id'=>&$data['response']['tbl_menu']['id']
                                        )
                                    );
                                }
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
                $this->set('ingredients', $this->Web->ingredients('list'));
                $this->set('categories', $this->Web->categories('list'));
                break;
        }
        if (!empty($err)) {
            $this->set('error', $err);
        }
    }
    public function orders($action='list') {
        global $common;
        $this->isJSON = true;
        $this->_template->xhr = true;
        $common->isPage = false;
        switch ($action) {
            case "list":
                $data = $this->Web->orders('list');
                $this->json = array('status'=>  \errors\codes::$__FOUND, 'data'=>$data['data'], 'lastSync'=>$data['lastSync']->format('Y-m-d H:i:s'));
                break;
        }
    }
}
