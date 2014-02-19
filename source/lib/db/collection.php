<?php

/**
 * This is a static class with a data namespace for easy data selection
 *
 * @author richard
 */
namespace data;
class collection {
    static function buildQuery(/*string*/ $mode='SELECT', /*array*/ &$tbl=array(), /*array*/ $joins=array(), /*array*/ $cols=array(), /*array*/ $cond=array()) {
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
                                    $subWhere = array();
                                    foreach ($blocks as $subRef=>$condit) {
                                        if (array_key_exists('join', $condit) === false) {
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
                                        } else {
                                            $subJoiner = $condit['join'];
                                            unset($condit['join']);
                                            $subBlockArr = array();
                                            foreach ($condit as $subCondit) {
                                                if ($subCondit['operand'] != 'IN') {
                                                    $subBlockArr[] = $subRef.'.'.$subCondit['col'].' '.$subCondit['operand'].' '.$subCondit['value'];
                                                } else {
                                                    $subVal = '';
                                                    if (is_array($subCondit['value'])) {
                                                        $subVal = "'".implode("','", $subCondit['value'])."'";
                                                        $blockArr[] = $subRef.'.'.$subCondit['col'].' '.$subCondit['operand'].' ('.$subVal.')';
                                                    } else {
                                                        $err[] = $subRef.'.'.$subCondit['col'].' IN requires an array';
                                                    }
                                                }
                                            }
                                            if (!empty($subBlockArr)) {
                                                $subWhere[] = "(".implode(' '.$subJoiner.' ', $subBlockArr).")";
                                            }
                                        }
                                    }
                                    if (!empty($blockArr)) {
                                        $subWhere[] = "(".implode(' '.$joiner.' ', $blockArr).")";
                                    }
                                    if (isset($joiner) && !empty($subWhere)) {
                                        $where[] = "(".implode(' '.$joiner.' ', $subWhere).")";
                                    }
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
                                            if (!empty($itemArr)) {
                                                $subWhere[] = "(".implode(' '.$itemJoiner.' ', $itemArr).")";
                                            }
                                        }
                                    }
                                    if (isset($itemJoiner) && !empty($subWhere)) {
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
                if (!empty($query)) {
                    return \data\collection::runQuery($query, $mode);
                }
                break;
            case "INSERT":
                $data = $tbl;
                if (!empty($data)) {
                    foreach ($data as $tbl=>$info) {
                        if ($tbl != 'response' && $tbl != 'mode' && $tbl != 'insert' && $tbl != 'update') {
                            if (array_key_exists('mode', $info)) {
                                $mode = $info['mode'];
                            } else if (array_key_exists('mode', $data))  {
                                $mode = $data['mode'];
                            } else {
                                $mode = 'insert';
                            }
                            switch ($mode) {
                                case 'insert':
                                    $data['response'] = \data\collection::runInsert($tbl, $info, $data);
                                    break;
                                case 'update':
                                    $data['response'] = \data\collection::runUpdate($tbl, $info, $data);
                                    break;
                                case 'delete':

                                    break;
                            }
                        } else if ($tbl == 'insert') {
                            // this means we have multiple items being inserted \\
                            if (array_key_exists('response', $info)) {
                                foreach ($info as $insTbl=>$subInfo) {
                                    if ($insTbl != 'response' && $insTbl != 'mode') {
                                        $data['insert']['response'] = \data\collection::runInsert($insTbl, $subInfo, $data['insert']);
                                    }
                                }
                            }
                        } else if ($tbl == 'update') {
                            // we may have this created ready for some updates so the response may not have been set for it \\
                            if (array_key_exists('response', $info)) {
                                // this means we have multiple items being updated \\
                                foreach ($info as $insTbl=>$subInfo) {
                                    if ($insTbl != 'response' && $insTbl != 'mode') {
                                        $data['update']['response'] = \data\collection::runUpdate($insTbl, $subInfo, $data['update']);
                                    }
                                }
                            }
                        }
                    }
                    $return = array_merge((array_key_exists('response', $data)) ? $data['response'] : array(), (array_key_exists('insert', $data)) ? ((array_key_exists('update', $data)) ? array('insert'=>$data['insert']['response'], 'update'=>$data['update']['response']) : ((array_key_exists('response', $data['insert'])) ? $data['insert']['response'] : $data['response'])) : ((array_key_exists('update', $data)) ? $data['update']['response'] : array()));
                    return array('success'=>true, 'returned'=>$return);
                }
                break;
            case "QUICKINSERT":
                global $db;
                $data = $tbl;
                // this is a function to return an ID after inserting the data entered \\
                $resp = array();
                if (!empty($data)) {
                    foreach ($data as $tbl=>$fields) {
                        $flds = '';
                        $vals = '';
                        foreach ($fields as $fld=>$val) {
                            $flds .= "`$fld`, ";
                            $vals .= "'$val', ";
                        }
                        $flds = rtrim($flds, ', ');
                        $vals = rtrim($vals, ', ');
                        $insert = "INSERT INTO $tbl ($flds) VALUES ($vals)";
                        $resp[$tbl] = $db->dbQuery($insert, 'id');
                    }
                }
                return $resp;
                break;
            case "QUICKUPDATE":
                global $db;
                $data = $tbl;
                // this is a function to do a quick update \\
                $upd = array();
                foreach ($data as $tbl=>$info) {
                    $where = '';
                    $flds = array();
                    foreach ($info['fields'] as $fld=>$val) {
                        $flds[] = "`$fld`=".((is_int($val)) ? "$val" : "'$val'");
                    }
                    if (array_key_exists('where', $info)) {
                        $where .= "WHERE ";
                        $whereArr = array();
                        foreach ($info['where'] as $fld=>$val) {
                            $whereArr[] = "`$fld`=".((is_int($val)) ? "$val" : "'$val'");
                        }
                        $where .= implode(' AND ', $whereArr);
                    } else {
                        return "Specify a WHERE clause for $tbl!!";
                    }
                    $update = "UPDATE $tbl SET ".implode(', ', $flds)." $where";
                    $upd[$tbl] = $db->dbQuery($update);
                }
                return $upd;
                break;
            case "DELETE":
                global $db;
                $data = $tbl;
                $success = true;
                if (count($data) > 0) {
                    foreach ($data as $tbl=>$cond) {
                        if (array_key_exists(0, $cond)) {
                            $condition = $cond[0];
                        } else {
                            $condition = array();
                            foreach ($cond as $fld=>$val) {
                                $condition[] = "$fld=".((is_int($val)) ? $val : "'$val'");
                            }
                            $condition = implode('AND', $condition);
                        }
                        $del = $db->dbQuery("DELETE FROM $tbl WHERE $condition");
                        if (!$del) {
                            $success = false;
                        }
                    }
                }
                return $success;
                                    
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
    static function runInsert($tbl, $info, $data) {
        global $db;
        $fldsStr = '';
        $valsStr = '';
        if (array_key_exists('rows', $info)) {
            // do this to clear the default id key as we are in multi-row mode \\
            if (!is_array($data['response'])) {
                die($data['response']);
            }
            if (!array_key_exists($tbl, $data['response']) || !is_array($data['response'][$tbl])) {
                $data['response'][$tbl] = array();
            }
            $first = true;
            foreach ($info['rows'] as $ind=>$row) {
                $valsStr = '(';
                foreach ($row['fields'] as $fld=>$val) {
                    if ($first) {
                        $fldsStr .= "`$fld`, ";
                    }
                    // see if we have a keyword of NOW \\
                    $namedFn = false;
                    if ($val === '_NOW_') {
                        $val = 'NOW()';
                        $namedFn = true;
                    }
                    $valsStr .= ((is_int($val) || $namedFn) ? "$val" : "'$val'").", ";
                }
                $first = false;
                $fldsStr = rtrim($fldsStr, ', ');
                $valsStr = rtrim($valsStr, ', ');
                $valsStr .= ')';
                $data['response'][$tbl][$ind]['id'] = $db->dbQuery("INSERT INTO $tbl ($fldsStr) VALUES $valsStr", 'id');
                $data['response'][$tbl][$ind] = array_merge($data['response'][$tbl][$ind], $row['fields']);
            }
        } else {
            $valsStr .= '(';
            foreach ($info['fields'] as $fld=>$val) {
                $fldsStr .= "`$fld`, ";
                // see if we have a keyword of NOW \\
                $namedFn = false;
                if ($val === '_NOW_') {
                    $val = 'NOW()';
                    $namedFn = true;
                }
                $valsStr .= ((is_int($val) || $namedFn) ? "$val" : "'$val'").", ";
            }
            $fldsStr = rtrim($fldsStr, ', ');
            $valsStr = rtrim($valsStr, ', ');
            $valsStr .= ')';
            $data['response'][$tbl]['id'] = $db->dbQuery("INSERT INTO $tbl ($fldsStr) VALUES $valsStr", 'id');
            $data['response'][$tbl] = array_merge($data['response'][$tbl], $info['fields']);
        }
        
        return $data['response'];
    }
    static function runUpdate($tbl, $info, $data) {
        global $db;
        $fldsStr = '';
        $where = '';
        if (array_key_exists('rows', $info)) {
            foreach ($info['rows'] as $ind=>$row) {
                $where = '';
                $fldsStr = '';
                foreach ($row['fields'] as $fld=>$val) {
                    // see if we have a keyword of NOW \\
                    $namedFn = false;
                    if ($val === '_NOW_') {
                        $val = 'NOW()';
                        $namedFn = true;
                    }
                    $fldsStr .= "`$fld`=".((is_int($val) || $namedFn) ? "$val" : "'$val'").", ";
                }
                if (array_key_exists('where', $row)) {
                    $where .= "WHERE ";
                    $whereArr = array();
                    foreach ($row['where'] as $fld=>$val) {
                        // see if we have a keyword of NOW \\
                        $namedFn = false;
                        if ($val === '_NOW_') {
                            $val = 'NOW()';
                            $namedFn = true;
                        }
                        $whereArr[] = "`$fld`=".((is_int($val) || $namedFn) ? "$val" : "'$val'");
                    }
                    $where .= implode(' AND ', $whereArr);
                } else {
                    return "Specify a WHERE clause on table: $tbl";
                }
                $fldsStr = rtrim($fldsStr, ', ');
                $db->dbQuery("UPDATE $tbl SET $fldsStr $where");
                $data['response'][$tbl][] = array_merge($row['fields'], $row['where']);
            }
        } else {
            foreach ($info['fields'] as $fld=>$val) {
                // see if we have a keyword of NOW \\
                $namedFn = false;
                if ($val === '_NOW_') {
                    $val = 'NOW()';
                    $namedFn = true;
                }
                $fldsStr .= "`$fld`=".((is_int($val) || $namedFn) ? "$val" : "'$val'").", ";
            }
            if (array_key_exists('where', $info)) {
                $where .= "WHERE ";
                $whereArr = array();
                foreach ($info['where'] as $fld=>$val) {
                    $whereArr[] = "`$fld`=".((is_int($val)) ? "$val" : "'$val'");
                }
                $where .= implode(' AND ', $whereArr);
            } else {
                return "Specify a WHERE clause on table: $tbl";
            }
            $fldsStr = rtrim($fldsStr, ', ');
            $db->dbQuery("UPDATE $tbl SET $fldsStr $where");
            $data['response'][$tbl] = array_merge($info['fields'], $info['where']);
        }
        return $data['response'];
    }
}

?>
