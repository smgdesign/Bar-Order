<?php

/**
 * Dettol / Lysol - 2013
 */
class ApiController extends Controller {
    public function __init() {
        global $auth, $common, $db;
        $qr = $this->registerQR();
        if ($qr) {
            if ($this->checkContinue()) {
                $device = $auth->checkDevice();
                if (is_array($device)) {
                    if (($device['status'] == 'new' || ($device['status'] == 'exists' && $device['sync'])) || $common->getParam('override')) {
                        if ((!is_null($common->getParam('sync')) && $common->getParam('sync') !== false) || $common->getParam('override')) {
                            $data = array();
                            $data['venues'] = $this->venue('list');
                            $data['locations'] = $this->location('list');
                            $data['tables'] = $this->table('list');
                            $data['menu'] = $this->menu('list');
                            $data['sponsors'] = $this->sponsors('list');
                            $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'data'=>$data);
                            $db->dbQuery("UPDATE tbl_device SET last_sync=NOW() WHERE id={$device['device_id']}");
                        } else {
                            $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'sync'=>'required');
                        }
                    } else {
                        $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'sync'=>'not_required');
                    }
                } else {
                    $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'sync'=>'not_required');
                }
            } else {
                $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'API Key not matched');
            }
        } else {
            $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'The QR code data string is required in format venueID:locationID:tableID - for testing purposes, this should be 1:2:1');
        }
    }
    
    public function registerQR() {
        global $common;
        if (!$this->checkContinue()) return;
        if (!is_null($common->getParam('QR'))) {
            $qr = $common->getParam('QR');
            $info = explode(':', $qr);
            if (!is_null($info)) {
                // our QR code will contain venueID:locationID:tableID \\
                if (count($info) == 3) {
                    $venue = (int)$info[0];
                    $location = (int)$info[1];
                    $table = (int)$info[2];
                    $this->venue('set', $venue);
                    $this->location('set', $location);
                    $this->table('set', $table);
                    return true;
                }
            }
        }
        return false;
    }
    
    public function venue($action='list', $id=0) {
        global $session;
        if (!$this->checkContinue()) return;
        switch ($action) {
            case "list":
                $tbl = array(
                    'v'=>'tbl_venue'
                );
                $joins = array();
                $cols = array(
                    'v'=>array('*')
                );
                $cond = array('v'=>array(
                    'join'=>'AND',
                    array(
                        'col'=>'parent_id',
                        'operand'=>'=',
                        'value'=>0
                    ),
                    array(
                        'col'=>'id',
                        'operand'=>'=',
                        'value'=>$session->getVar('venue_id')
                    )
                ));
                $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                if ($data[1] > 0) {
                    return array('status'=>\errors\codes::$__FOUND, 'data'=>$data[0]);
                } else {
                    return array('status'=>\errors\codes::$__EMPTY);
                }
                break;
            case "set":
                if ($id != 0 && is_int($id)) {
                    $session->addVar('venue_id', $id);
                    $this->json = array('status'=>\errors\codes::$__SUCCESS, 'venue_id'=>$session->getVar('venue_id'));
                } else {
                    $this->json = array('status'=>\errors\codes::$__ERROR);
                }
                break;
        }
    }
    public function location($action='list', $id=0) {
        global $session;
        if (!$this->checkContinue()) return;
        switch ($action) {
            case "list":
                $tbl = array(
                    'v'=>'tbl_venue'
                );
                $joins = array();
                $cols = array(
                    'v'=>array('*')
                );
                $cond = array(
                    'v'=>array(
                        'join'=>'AND',
                        array(
                            'col'=>'parent_id',
                            'operand'=>'=',
                            'value'=>$session->getVar('venue_id')
                        )
                    )
                );
                $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                if ($data[1] > 0) {
                    return array('status'=>  \errors\codes::$__FOUND, 'data'=>$data[0]);
                } else {
                    return array('status'=>  \errors\codes::$__EMPTY);
                }
                break;
            case "set":
                if ($id != 0 && is_int($id)) {
                    $session->addVar('location_id', $id);
                    $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'location_id'=>$id);
                } else {
                    $this->json = array('status'=> \errors\codes::$__ERROR);
                }
                break;
        }
    }
    public function table($action='list', $id=0) {
        global $session;
        if (!$this->checkContinue()) return;
        switch ($action) {
            case "list":
                $tbl = array(
                    't'=>'tbl_table'
                );
                $joins = array();
                $cols = array(
                    't'=>array('*')
                );
                $cond = array(
                    't'=>array(
                        'join'=>'AND',
                        array(
                            'col'=>'location_id',
                            'operand'=>'=',
                            'value'=>$session->getVar('location_id')
                        )
                    )
                );
                $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                if ($data[1] > 0) {
                    return array('status'=>  \errors\codes::$__FOUND, 'data'=>$data[0]);
                } else {
                    return array('status'=>  \errors\codes::$__EMPTY);
                }
                break;
            case "set":
                if ($id != 0 && is_int($id)) {
                    $session->addVar('table_id', $id);
                    $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'table_id'=>$id);
                } else {
                    $this->json = array('status'=> \errors\codes::$__ERROR);
                }
                break;
        }
    }
    public function menu($action='get', $id=0) {
        global $common, $session;
        if (!$this->checkContinue()) return;
        switch ($action) {
            case "list":
                $tbl = array(
                    'm'=>'tbl_menu'
                );
                $joins = array(
                    array('table'=>'tbl_ingredient_hooks', 'as'=>'ih', 'on'=>array('ih.menu_id', '=', 'm.id')),
                    array('table'=>'tbl_ingredient', 'as'=>'i', 'on'=>array('i.id', '=', 'ih.ingredient_id'))
                );
                $cols = array(
                    'm'=>array('*'),
                    'i'=>array('id AS ingredient_id', 'title AS ingredient', 'desc AS ingredient_desc')
                );
                $cond = array(
                    'm'=>array(
                        'join'=>'OR',
                        array(
                            'col'=>'location_id',
                            'operand'=>'=',
                            'value'=>$session->getVar('venue_id')
                        ),
                        array(
                            'col'=>'location_id',
                            'operand'=>'=',
                            'value'=>$session->getVar('location_id')
                        )
                    )
                );
                $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                $menu = array();
                if ($data[1] > 0) {
                    foreach ($data[0] as $item) {
                        if (!isset($menu[$item['id']])) {
                            $menu[$item['id']] = array('location_id'=>$item['location_id'], 'title'=>$item['title'], 'icon'=>$item['icon'], 'desc'=>$item['desc'], 'price'=>$item['price'], 'ingredients'=>array(), 'categories'=>array());
                        }
                        if (!is_null($item['ingredient_id'])) {
                            $menu[$item['id']]['ingredients'][] = array('id'=>$item['ingredient_id'], 'title'=>$item['ingredient'], 'desc'=>$item['ingredient_desc']);
                        }
                    }
                }
                // now get the categories \\
                $cats = $this->Api->getCategories(array_keys($menu));
                if (count($cats) > 0) {
                    foreach ($cats as $cat) {
                        if (!isset($menu[$cat['menu_id']]['categories'])) {
                            $menu[$cat['menu_id']]['categories'] = array();
                        }
                        $menu[$cat['menu_id']]['categories'][] = array('id'=>$cat['id'], 'title'=>$cat['title'], 'desc'=>$cat['desc']);
                    }
                }
                /*$locations = array();
                foreach ($menu as $id=>$item) {
                    if (!isset($locations[$item['location_id']])) {
                        $locations[$item['location_id']] = array();
                    }
                    $locations[$item['location_id']][$id] = $item;
                }
                if (!empty($locations)) {
                    return array('status'=>  \errors\codes::$__FOUND, 'data'=>$locations, 'description'=>'The data object is grouped by the location ID that each menu item belongs to');
                } else {
                    return array('status'=>  \errors\codes::$__EMPTY);
                }*/
                if (!empty($menu)) {
                    return array('status'=>  \errors\codes::$__FOUND, 'data'=>$menu);
                } else {
                    return array('status'=>  \errors\codes::$__EMPTY);
                }
                break;
            case "get":
                
                break;
            case "set":
                
                break;
        }
    }
    public function sponsors($action='list') {
        global $session;
        if (!$this->checkContinue()) return;
        $table = array(
            'a'=>'tbl_advert'
        );
        $cols = array(
            'a'=>array('img AS image', 'link')
        );
        $cond = array(
            'a'=>array(
                'join'=>'AND',
                array(
                    'col'=>'venue_id',
                    'operand'=>'=',
                    'value'=>$session->getVar('venue_id')
                )
            )
        );
        $data = \data\collection::buildQuery("SELECT", $table, array(), $cols, $cond);
        if ($data[1] > 0) {
            return array('status'=>  \errors\codes::$__FOUND, 'data'=>$data[0]);
        } else {
            return array('status'=>  \errors\codes::$__EMPTY);
        }
    }
    public function order($action='list') {
        global $auth, $db, $common, $session;
        if (!$this->checkContinue()) return;
        $device = $auth->getDevice();
        if (is_array($device)) {
            $deviceID = $device['id'];
            switch ($action) {
                case "list":
                    $last = new DateTime();
                    $tbl = array(
                        'o'=>'tbl_order'
                    );
                    $joins = array(
                        array('table'=>'tbl_order_item', 'as'=>'oi', 'on'=>array('oi.order_id', '=', 'o.id')),
                        array('table'=>'tbl_menu', 'as'=>'m', 'on'=>array('m.id', '=', 'oi.menu_id')),
                        array('table'=>'tbl_table', 'as'=>'t', 'on'=>array('t.id', '=', 'o.table_id'))
                    );
                    $cols = array(
                        'o'=>array('*'),
                        'oi'=>array('status AS item_status'),
                        'm'=>array('id AS menu_id', 'title', 'price'),
                        't'=>array('name')
                    );
                    $cond = array(
                        'o'=>array(
                            'join'=>'OR',
                            array(
                                'col'=>'device_id',
                                'operand'=>'=',
                                'value'=>$deviceID
                            ),
                            'o'=>array(
                                'join'=>'AND',
                                array(
                                    'col'=>'table_id',
                                    'operand'=>'=',
                                    'value'=>$session->getVar('table_id')
                                ),
                                array(
                                    'col'=>'time_completed',
                                    'operand'=>'=',
                                    'value'=>"'0000-00-00 00:00:00'"
                                )
                            )
                        )
                    );
                    $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                    if ($data[1] > 0) {
                        $orders = array();
                        foreach ($data[0] as $item) {
                            if (!isset($orders[$item['id']])) {
                                $orders[$item['id']] = array('status'=>$item['status'], 'instruction'=>$item['instructions'], 'time_ordered'=>$item['time_ordered'], 'time_completed'=>$item['time_completed'], 'table'=>$item['name'], 'items'=>array());
                            }
                            
                            if (isset($orders[$item['id']]['items'][$item['menu_id']])) {
                                $orders[$item['id']]['items'][$item['menu_id']]['qty'] = $orders[$item['id']]['items'][$item['menu_id']]['qty']+1;
                            } else {
                                $orders[$item['id']]['items'][$item['menu_id']] = array('id'=>$item['menu_id'], 'title'=>$item['title'], 'price'=>$item['price'], 'status'=>$item['item_status'], 'qty'=>1);
                            }
                        }
                        if (!empty($orders)) {
                            $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'data'=>$orders, 'lastSync'=>$last->format('Y-m-d H:i:s'));
                        } else {
                            $this->json = array('status'=>\errors\codes::$__EMPTY, 'message'=>'Orders were found, but not matching criteria.', 'query'=>array($tbl, $joins, $cols, $cond));
                        }
                    } else {
                        $this->json = array('status'=>\errors\codes::$__EMPTY, 'message'=>'No orders were found.', 'query'=>array($tbl, $joins, $cols, $cond));
                    }
                    break;
                case "place":
                    if (!is_null($common->getParam('submitted'))) {
                        $tableID = $session->getVar('table_id');
                        if (!is_null($tableID)) {
                            $instructions = $common->getParam('instructions');
                            $items = $common->getParam('items');
                            $ordered = new DateTime();
                            if ($ordered !== false) {
                                if (!is_null($items) && is_array($items)) {
                                    $orderItems = array();
                                    $orderID = $db->dbQuery("INSERT INTO tbl_order (table_id, device_id, status, instructions, time_ordered) VALUES ($tableID, $deviceID, 0, '$instructions', '{$ordered->format('Y-m-d H:i:s')}')", 'id');
                                    if (is_int($orderID)) {
                                        foreach ($items as $id=>$item) {
                                            $i = 0;
                                            while ($i < $item) {
                                                $orderItems[] = array('menu_id'=>$id, 'status'=>0, 'order_id'=>$orderID);
                                                $i++;
                                            }
                                        }
                                        if (!empty($orderItems)) {
                                            foreach ($orderItems as $orderItem) {
                                                $db->dbQuery("INSERT INTO tbl_order_item (menu_id, status, order_id) VALUES (".implode(', ', $orderItem).")");
                                            }
                                            $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'order_id'=>$orderID, 'order_status'=>0);
                                        } else {
                                            $this->json = array('status'=>  \errors\codes::$__EMPTY);
                                        }
                                    } else {
                                        $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'A valid order was not created', 'item'=>$orderID);
                                    }
                                } else {
                                    $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'items is not a valid post array object', 'item'=>$items);
                                }
                            } else {
                                $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'date ordered is invalid', 'item'=>$ordered);
                            }
                        } else {
                            $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'the session has no table id');
                        }
                    } else {
                        $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'submitted is required');
                    }
                    break;
                case "status":
                    if (!is_null($common->getParam('id'))) {
                        $order = $this->Api->orders('get', $common->getParam('id'));
                        if (!empty($order)) {
                            $this->json = array('status'=>  \errors\codes::$__FOUND, 'data'=>array('status'=>$order['status'], 'items'=>$order['items']));
                        } else {
                            $this->json = array('status'=>  \errors\codes::$__ERROR);
                        }
                    } else {
                        $this->json = array('status'=>  \errors\codes::$__ERROR);
                    }
                    break;
            }
        } else {
            $this->json = array('status'=>  \errors\codes::$__ERROR);
        }
    }
    public function change_status($mode='order') {
        global $common;
        switch ($mode) {
            case "order":
                if (!is_null($common->getParam('id')) && !is_null($common->getParam('status'))) {
                    $data = array(
                        'tbl_order'=>array(
                            'fields'=>array(
                                'status'=>$common->getParam('status')
                            ),
                            'mode'=>'update',
                            'where'=>array('id'=>$common->getParam('id'))
                        ),
                        'tbl_order_item'=>array(
                            'fields'=>array(
                                'status'=>$common->getParam('status')
                            ),
                            'mode'=>'update',
                            'where'=>array('order_id'=>$common->getParam('id'))
                        ),
                        'response'=>array(
                            'tbl_order'=>array('id'=>$common->getParam('id')),
                            'tbl_order_item'=>array('order_id'=>$common->getParam('id'))
                        )
                    );
                    if ((int)$common->getParam('status') == 3) {
                        $data['tbl_order']['fields']['time_completed'] = '_NOW_';
                    }
                    $update = \data\collection::buildQuery("INSERT", $data);
                    if ($update['success']) {
                        $this->json = array('status'=>  \errors\codes::$__SUCCESS);
                    } else {
                        $this->json = array('status'=>  \errors\codes::$__ERROR);
                    }
                } else {
                    $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'id and status are required');
                }
                break;
            case "item":
                if (!is_null($common->getParam('id')) && !is_null($common->getParam('item_id')) && !is_null($common->getParam('status'))) {
                    $data = array(
                        'tbl_order_item'=>array(
                            'fields'=>array(
                                'status'=>$common->getParam('status')
                            ),
                            'mode'=>'update',
                            'where'=>array('order_id'=>$common->getParam('id'), 'menu_id'=>$common->getParam('item_id'))
                        ),
                        'response'=>array(
                            'tbl_order_item'=>array('order_id'=>$common->getParam('id'))
                        )
                    );
                    if ($common->getParam('status') == 3) {
                        $data['tbl_order_item']['fields']['time_prepared'] = 'NOW()';
                    }
                    $update = \data\collection::buildQuery("INSERT", $data);
                    if ($update['success']) {
                        $this->json = array('status'=>  \errors\codes::$__SUCCESS);
                    } else {
                        $this->json = array('status'=>  \errors\codes::$__ERROR);
                    }
                } else {
                    $this->json = array('status'=>  \errors\codes::$__ERROR, 'message'=>'id, item_id and status are required');
                }
                break;
        }
        
    }
}
