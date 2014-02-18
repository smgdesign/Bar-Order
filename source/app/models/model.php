<?php

/**
 * Dettol / Lysol - 2013
 */
class Model {
    protected $_model;
    var $_controller;
    public function __construct() {
        $this->_model = get_class($this);
    }
    public function orders($mode='list', $id=0) {
        switch ($mode) {
            case "list":
                global $session;
                if (!is_null($session->getVar('location_id'))) {
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
                        't'=>array(
                            'join'=>'AND',
                            array(
                                'col'=>'location_id',
                                'operand'=>'=',
                                'value'=>$session->getVar('location_id')
                            )
                        )
                    );
                    $order = array('ORDER BY time_ordered DESC');
                    $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond, $order);
                    if ($data[1] > 0) {
                        $orders = array();
                        foreach ($data[0] as $item) {
                            if (!isset($orders[$item['id']])) {
                                $orders[$item['id']] = array('status'=>$item['status'], 'instruction'=>$item['instructions'], 'time_ordered'=>$item['time_ordered'], 'time_completed'=>$item['time_completed'], 'table'=>$item['name'], 'items'=>array(), 'total'=>0);
                            }
                            if (!empty($item['menu_id'])) {
                                $orders[$item['id']]['items'][] = array('id'=>$item['menu_id'], 'title'=>$item['title'], 'price'=>$item['price'], 'status'=>$item['item_status']);
                                $orders[$item['id']]['total'] = $orders[$item['id']]['total']+$item['price'];
                            }
                        }
                        return $orders;
                    } else {
                        return array();
                    }
                }
                break;
            case "get":
                if ($id !== 0) {
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
                                'col'=>'id',
                                'operand'=>'=',
                                'value'=>$id
                            )
                        )
                    );
                    $order = array('ORDER BY time_ordered DESC');
                    $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond, $order);
                    if ($data[1] > 0) {
                        $orders = array();
                        foreach ($data[0] as $item) {
                            if (empty($orders)) {
                                $orders = array('id'=>$item['id'], 'status'=>$item['status'], 'instruction'=>$item['instructions'], 'time_ordered'=>$item['time_ordered'], 'time_completed'=>$item['time_completed'], 'table'=>$item['name'], 'items'=>array(), 'total'=>0);
                            }
                            if (!empty($item['menu_id'])) {
                                if (isset($orders['items'][$item['menu_id']])) {
                                    $orders['items'][$item['menu_id']]['qty'] = $orders['items'][$item['menu_id']]['qty']+1;
                                } else {
                                    $orders['items'][$item['menu_id']] = array('id'=>$item['menu_id'], 'title'=>$item['title'], 'price'=>$item['price'], 'status'=>$item['item_status'], 'qty'=>1);
                                }
                                
                                $orders['total'] = $orders['total']+$item['price'];
                            }
                        }
                        return $orders;
                    } else {
                        return array();
                    }
                }
                break;
        }
    }
    public function ingredients($mode='list') {
        switch ($mode) {
            case "list":
                $tbl = array(
                    'i'=>'tbl_ingredient'
                );
                $cols = array(
                    'i'=>array('*')
                );
                $data = \data\collection::buildQuery("SELECT", $tbl, array(), $cols);
                return $data[0];
                break;
        }
    }
    public function categories($mode='list') {
        switch ($mode) {
            case "list":
                $tbl = array(
                    'c'=>'tbl_category'
                );
                $cols = array(
                    'c'=>array('*')
                );
                $data = \data\collection::buildQuery("SELECT", $tbl, array(), $cols);
                return $data[0];
                break;
        }
    }
}
