<?php
/**
 * Bar App - 2014
 */
class Api extends Model {
    public function getCategories($menuID=array()) {
        if (is_array($menuID) && !empty($menuID)) {
            $tbl = array('c'=>'tbl_category');
            $joins = array(
                array('table'=>'tbl_category_hooks', 'as'=>'ch', 'on'=>array('ch.cat_id', '=', 'c.id'))
            );
            $cols = array(
                'c'=>array('id', 'title', 'desc'),
                'ch'=>array('menu_id', 'is_primary')
            );
            $cond = array('ch'=>array(
                    'join'=>'AND',
                    array(
                        'col'=>'menu_id',
                        'operand'=>'IN',
                        'value'=>$menuID
                    )
                )
            );
            $cats = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond);
            if ($cats[1] > 0) {
                return $cats[0];
            }
        }
        return array();
    }
}
?>
