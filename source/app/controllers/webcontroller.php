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
    public function edit($mode='menu', $id=0) {
        global $common, $session;
        $this->level = 1;
        if (!$this->checkWebContinue()) return;
        $this->set('title', 'Edit '.$mode);
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
                    if (!is_null($common->getParam('menu_id')) && $common->getParam('menu_id') != 0) {
                        $id = $common->getParam('menu_id');
                        $data['tbl_menu']['mode'] = 'update';
                        $data['tbl_menu']['where'] = array('id'=>$id);
                        $data['response']['tbl_menu']['id'] = $id;
                        $delData = array('tbl_ingredient_hooks'=>array('menu_id'=>$id), 'tbl_category_hooks'=>array('menu_id'=>$id));
                        \data\collection::buildQuery("DELETE", $delData);
                        
                    }
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
                                $files->resizeImage(200, 200, 'square');
                                $files->saveImage(uploadDir.$file['name']);
                                $data['tbl_menu']['fields']['icon'] = 'http://bar.smgdev.co.uk/img/items/'.$file['name'];
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
                $this->set('action', 'add');
                if ($id != 0) {
                    $this->set('action', 'edit');
                    $tbl = array(
                        'm'=>'tbl_menu'
                    );
                    $joins = array(
                        array('table'=>'tbl_category_hooks', 'as'=>'ch', 'on'=>array('ch.menu_id', '=', 'm.id'))
                    );
                    $cols = array(
                        'm'=>array('*'),
                        'ch'=>array('cat_id')
                    );
                    $cond = array(
                        'm'=>array(
                            'join'=>'AND',
                            array(
                                'col'=>'id',
                                'operand'=>'=',
                                'value'=>$id
                            )
                        )
                    );
                    $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                    if ($data[1] > 0) {
                        $item = array();
                        $ingTbl = array(
                            'i'=>'tbl_ingredient_hooks'
                        );
                        $ingCols = array(
                            'i'=>array('ingredient_id')
                        );
                        $ingCond = array(
                            'i'=>array(
                                'join'=>'AND',
                                array(
                                    'col'=>'menu_id',
                                    'operand'=>'=',
                                    'value'=>$id
                                )
                            )
                        );
                        $ingredient = \data\collection::buildQuery("SELECT", $ingTbl, array(), $ingCols, $ingCond);
                        foreach ($data[0] as $i=>$cat) {
                            if ($i == 0) {
                                $item['title'] = $cat['title'];
                                $item['desc'] = $cat['desc'];
                                $item['price'] = $cat['price'];
                                $item['icon'] = $cat['icon'];
                                $item['categories'] = array();
                            }
                            $item['categories'][] = $cat['cat_id'];
                            
                        }
                        if ($ingredient[1] > 0) {
                            $item['ingredients'] = array();
                            foreach ($ingredient[0] as $ing) {
                                $item['ingredients'][] = $ing['ingredient_id'];
                            }
                        }
                        $this->set('info', $item);
                    }
                    $this->set('id', $id);
                }
                $this->set('ingredients', $this->Web->ingredients('list'));
                $this->set('categories', $this->Web->categories('list'));
                break;
        }
        if (!empty($err)) {
            $this->set('error', $err);
        }
    }
    public function delete($mode='menu', $id=0) {
        global $common;
        $this->level = 1;
        $this->_template->xhr = true;
        $common->isPage = false;
        
        if ($id!=0) {
            switch ($mode) {
                case "menu":
                    // get the image \\
                    $tbl = array('m'=>'tbl_menu');
                    $joins = array();
                    $cols = array(
                        'm'=>array('icon')
                    );
                    $cond = array(
                        'm'=>array(
                            'join'=>'AND',
                            array(
                                'col'=>'id',
                                'operand'=>'=',
                                'value'=>$id
                            )
                        )
                    );
                    $img = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                    if ($img[1] > 0) {
                        if (file_exists(uploadDir.basename($img[0][0]['icon']))) {
                            unlink(uploadDir.basename($img[0][0]['icon']));
                        }
                        $del = array(
                            'tbl_menu'=>array('id'=>$id),
                            'tbl_category_hooks'=>array('menu_id'=>$id),
                            'tbl_ingredient_hooks'=>array('menu_id'=>$id)
                        );
                        \data\collection::buildQuery("DELETE", $del);
                    }
                    break;
            }
            
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
    public function menu($action='list') {
        global $common;
        $this->isJSON = true;
        $this->_template->xhr = true;
        $common->isPage = false;
        switch ($action) {
            case "list":
                $this->json = array('status'=>  \errors\codes::$__FOUND, 'data'=>$this->Web->menu('list'));
                break;
        }
    }
}
