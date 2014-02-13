<?php

/**
 * Dettol / Lysol - 2013
 */
class ApiController extends Controller {
    public function __init() {
        global $auth, $common, $db;
        $device = $auth->checkDevice();
        if (!$this->checkContinue()) return;
        if (is_array($device)) {
            if (($device['status'] == 'new' || ($device['status'] == 'exists' && $device['sync'])) || $common->getParam('override') === true) {
                if ((!is_null($common->getParam('sync')) && $common->getParam('sync') !== false) || $common->getParam('override') === true) {
                    $data = array();
                    $data['venues'] = $this->venue('list');
                    $data['locations'] = $this->location('list');
                    $data['tables'] = $this->table('list');
                    $data['menu'] = $this->menu('list');
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
                if ($id != 0) {
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
                $cond = array();
                $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                if ($data[1] > 0) {
                    return array('status'=>  \errors\codes::$__FOUND, 'data'=>$data[0]);
                } else {
                    return array('status'=>  \errors\codes::$__EMPTY);
                }
                break;
            case "set":
                if ($id != 0) {
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
                $cond = array();
                $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                if ($data[1] > 0) {
                    return array('status'=>  \errors\codes::$__FOUND, 'data'=>$data[0]);
                } else {
                    return array('status'=>  \errors\codes::$__EMPTY);
                }
                break;
            case "set":
                if ($id != 0) {
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
                $cond = array();
                $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
                $menu = array();
                if ($data[1] > 0) {
                    foreach ($data[0] as $item) {
                        if (!isset($menu[$item['id']])) {
                            $menu[$item['id']] = array('location_id'=>$item['location_id'], 'title'=>$item['title'], 'desc'=>$item['desc'], 'price'=>$item['price'], 'ingredients'=>array());
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
                        $menu[$cat['menu_id']]['categories'][] = $cat;
                    }
                }
                $locations = array();
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
                }
                break;
            case "get":
                
                break;
            case "set":
                
                break;
        }
    }
    public function order($action='list') {
        global $auth, $db, $common;
        if (!$this->checkContinue()) return;
        $deviceID = $auth->getDevice();
        switch ($action) {
            case "list":
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
                        'join'=>'AND',
                        array(
                            'col'=>'device_id',
                            'operand'=>'=',
                            'value'=>$deviceID
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
                        if (!empty($item['menu_id'])) {
                            $orders[$item['id']]['items'][] = array('id'=>$item['menu_id'], 'title'=>$item['title'], 'price'=>$item['price'], 'status'=>$item['item_status']);
                        }
                    }
                    if (!empty($orders)) {
                        $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'data'=>$orders);
                    } else {
                        $this->json = array('status'=>\errors\codes::$__EMPTY);
                    }
                } else {
                    $this->json = array('status'=>\errors\codes::$__EMPTY);
                }
                break;
            case "place":
                if (!is_null($common->getParam('submitted'))) {
                    $tableID = $common->getParam('table_id');
                    if (!is_null($tableID)) {
                        $instructions = $common->getParam('instructions');
                        $items = $common->getParam('items');
                        $ordered = new DateTime($common->getParam('time_ordered'));
                        if ($ordered !== false) {
                            if (!is_null($items) && is_array($items)) {
                                $orderItems = array();
                                $orderID = $db->dbQuery("INSERT INTO tbl_order (table_id, device_id, status, instructions, time_ordered) VALUES ($tableID, $deviceID, 0, '$instructions', '$ordered')", 'id');
                                if (is_int($orderID)) {
                                    foreach ($items as $id=>$item) {
                                        $i = 0;
                                        while ($i < $item['qty']) {
                                            $orderItems[] = array('item_id'=>$id, 'status'=>0, 'order_id'=>$orderID);
                                            $i++;
                                        }
                                    }
                                    if (!empty($orderItems)) {
                                        foreach ($orderItems as $orderItem) {
                                            $db->dbQuery("INSERT INTO tbl_order_item (item_id, status, order_id) VALUES (".implode(', ', $orderItem).")");
                                        }
                                        $this->json = array('status'=>  \errors\codes::$__SUCCESS, 'order_id'=>$orderID, 'status'=>0);
                                    } else {
                                        $this->json = array('status'=>  \errors\codes::$__EMPTY);
                                    }
                                } else {
                                    $this->json = array('status'=>  \errors\codes::$__ERROR);
                                }
                            } else {
                                $this->json = array('status'=>  \errors\codes::$__ERROR);
                            }
                        } else {
                            $this->json = array('status'=>  \errors\codes::$__ERROR);
                        }
                    } else {
                        $this->json = array('status'=>  \errors\codes::$__ERROR);
                    }
                } else {
                    $this->json = array('status'=>  \errors\codes::$__ERROR);
                }
                break;
        }
    }
}
