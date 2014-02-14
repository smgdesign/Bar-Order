<?php

/**
 * This is a static class with a data namespace for easy data selection
 *
 * @author richard
 */
namespace data;
class collection {
    static function buildQuery(/*string*/ $mode='SELECT', /*array*/ $tbl=array(), /*array*/ $joins=array(), /*array*/ $cols=array(), /*array*/ $cond=array()) {
        $query = "";
        $err = array();
        switch ($mode) {
            case 'SELECT':
                if (!empty($tbl)) {
                    $colList = array();
                    if (!empty($cols)) {
                        foreach ($cols as $ref=>$col) {
                            $colList[] = $ref.'.'.implode(', '.$ref.'.', $col);
                        }
                    } else {
                        foreach ($tbl as $ref=>$table) {
                            $colList[] = $ref.'.*';
                        }
                    }
                    $colStr = implode(', ', $colList);
                    $table = reset($tbl);
                    $tableRef = array_keys($tbl);
                    $tblRef = reset($tableRef);
                    $query .= "SELECT $colStr FROM $table AS $tblRef ";
                    if (!empty($joins)) {
                        foreach ($joins as $joinTbl) {
                            $query .= "LEFT JOIN {$joinTbl['table']} AS {$joinTbl['as']} ON {$joinTbl['on'][0]}{$joinTbl['on'][1]}{$joinTbl['on'][2]} ";
                        }
                    }
                    $where = array();
                    if (!empty($cond)) {
                        foreach ($cond as $ref=>$blocks) {
                            if (is_array($blocks)) {
                                if (array_key_exists('join', $blocks)) {
                                    // this means this array needs linking together \\
                                    $joiner = $blocks['join'];
                                    unset($blocks['join']);
                                    $blockArr = array();
                                    foreach ($blocks as $condit) {
                                        if ($condit['operand'] != 'IN') {
                                            $blockArr[] = $ref.'.'.$condit['col'].' '.$condit['operand'].' '.$condit['value'];
                                        } else {
                                            $val = '';
                                            if (is_array($condit['value'])) {
                                                $val = "'".implode("','", $condit['value'])."'";
                                                $blockArr[] = $ref.'.'.$condit['col'].' '.$condit['operand'].' ('.$val.')';
                                            } else {
                                                $err[] = $ref.'.'.$condit['col'].' IN requires an array';
                                            }
                                        }
                                    }
                                    $where[] = "(".implode(' '.$joiner.' ', $blockArr).")";
                                } else {
                                    // this means there's deeper work to do \\
                                    $subWhere = array();
                                    foreach ($blocks as $item) {
                                        if (is_array($item)) {
                                            if (array_key_exists('join', $item) === false) {
                                                $item['join'] = 'AND';
                                            }
                                            // this means this array needs linking together \\
                                            $itemJoiner = $item['join'];
                                            unset($item['join']);
                                            $itemArr = array();
                                            foreach ($item as $condit) {
                                                if ($condit['operand'] != 'IN') {
                                                    $itemArr[] = $ref.'.'.$condit['col'].' '.$condit['operand'].' '.$condit['value'];
                                                } else {
                                                    $val = '';
                                                    if (is_array($condit['value'])) {
                                                        $val = "'".implode("','", $condit['value'])."'";
                                                        $itemArr[] = $ref.'.'.$condit['col'].' '.$condit['operand'].' ('.$val.')';
                                                    } else {
                                                        $err[] = $ref.'.'.$condit['col'].' IN requires an array';
                                                    }
                                                }
                                            }
                                            $subWhere[] = "(".implode(' '.$itemJoiner.' ', $itemArr).")";
                                        }
                                    }
                                    if (isset($itemJoiner)) {
                                        $where[] = "(".implode(' '.$itemJoiner.' ', $subWhere).")";
                                    }
                                }
                            }
                        }
                        if (!empty($where)) {
                            $query .= "WHERE ".implode(" AND ", $where);
                        }
                    }
                    if (func_num_args() > 5 && !is_bool(func_get_arg(5))) {
                        if (is_array(func_get_arg(5))) {
                            $query .= implode(' ', func_get_arg(5));
                        } else {
                            $query .= func_get_arg(5);
                        }
                    }
                }
                break;
        }
        if (!empty($query)) {
            return Collection::runQuery($query, $mode);
        }
    }
    static function runQuery($query='', $mode="SELECT") {
        global $db;
        $data = null;
        if (!empty($query)) {
            switch ($mode) {
                case 'SELECT':
                    $data = $db->dbResult($db->dbQuery($query));
                    break;
            }
        }
        return $data;
    }
}

?>
