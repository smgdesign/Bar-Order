<?php

/**
 * Dettol / Lysol - 2013
 */
class AdminController extends Controller {
    protected $img;
    protected $imageResized;
    protected $height;
    protected $width;
    public function index() {
        $this->level = 2;
        // index can't have parameters otherwise they'd become the view \\
        $this->set('title', 'Dettol &amp; Lysol Administration');
    }
    public function module($action='edit') {
        $this->level = 2;
        $args = func_get_args();
        $args = array_splice($args, 1);
        if (method_exists($this, 'module_'.$action)) {
            call_user_func_array(array($this, 'module_'.$action), $args);
        } else {
            $this->set('error', array('<span class="error">Module action not found.</span>'));
        }
    }
    
    public function module_edit($id=0) {
        global $db, $common, $session, $purifier;
        $this->set('title', 'Edit module - Dettol &amp; Lysol Administration');
        $this->set('mode', 'create');
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                $title = htmlentities($purifier->purify($common->getParam('title')));
                $lastPage = $this->Admin->getLastModule()['ordering'];
                $order = ((int)$lastPage+1);
                if ($common->getParam('id') != 0) {
                    if (!is_null($title)) {
                        $id = $common->getParam('id');
                        $upd = $db->dbQuery("UPDATE tbl_modules SET title='$title' WHERE id=$id");
                        $this->set('resp', array('<span class="success">updated successfully</span>'));
                    } else {
                        $this->set('resp', array('span class="error">please enter a title</span>'));
                    }
                } else {
                    if (!is_null($title)) {
                        $id = $db->dbQuery("INSERT INTO tbl_modules (title, ordering) VALUES ('$title', $order)", 'id');
                        $this->set('resp', array('<span class="success">added successfully</span>'));
                    } else {
                        $this->set('resp', array('span class="error">please enter a title</span>'));
                    }
                }
                
            } else {
                $this->set('resp', array('<span class="error">CSRF error</span>'));
            }
        }
        $session->addVar('form-token', uniqid(mt_rand(), true));
        $this->set('csrf', $session->getVar('form-token'));
        $this->set('data', $this->Admin->getModule($id));
        $this->set('id', $id);
        if ($id != 0) {
            $this->set('mode', 'update');
        }
    }
    public function page($action='edit') {
        $this->level = 2;
        $args = func_get_args();
        $args = array_splice($args, 1);
        if (method_exists($this, 'page_'.$action)) {
            call_user_func_array(array($this, 'page_'.$action), $args);
        } else {
            $this->set('resp', array('<span class="error">Page action not found.</span>'));
        }
    }
    public function page_edit($pID=0, $id=0) {
        global $db, $common, $session, $purifier;
        $this->set('title', 'Edit page - Dettol &amp; Lysol Administration');
        $this->set('mode', 'create');
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                if ($common->getParam('pID') != 0) {
                    $pID = $common->getParam('pID');
                    $title = htmlentities($purifier->purify($common->getParam('title', 'post', '', false)));
                    $lastPage = $this->Admin->getLastPage($pID)['ordering'];
                    //$order = $common->getParam('order', 'post', $lastPage+1);
                    $order = ((int)$lastPage+1);
                    $html = $common->escape_data($purifier->purify($common->getParam('html', 'post', '', false)));
                    if ($common->getParam('id') != 0) {
                        if (!is_null($title)) {
                            $id = $common->getParam('id');
                            $upd = $db->dbQuery("UPDATE tbl_module_pages SET title='$title', html='$html' WHERE id=$id AND module_id=$pID");
                            $this->set('resp', array('<span class="success">updated successfully</span>'));
                        } else {
                            $this->set('resp', array('span class="error">please enter a title</span>'));
                        }
                    } else {
                        if (!is_null($title)) {
                            $id = $db->dbQuery("INSERT INTO tbl_module_pages (module_id, title, html, ordering) VALUES ($pID, '$title', '$html', $order)", 'id');
                            $this->set('resp', array('<span class="success">added successfully</span>'));
                        } else {
                            $this->set('resp', array('span class="error">please enter a title</span>'));
                        }
                    }
                } else {
                    $this->set('resp', array('<span class="error">Module not set</span>'));
                }
            } else {
                $this->set('resp', array('<span class="error">CSRF error</span>'));
            }
        }
        if ($pID != 0) {
            $session->addVar('form-token', uniqid(mt_rand(), true));
            $this->set('csrf', $session->getVar('form-token'));
            $this->set('data', $this->Admin->getPage($id));
            $this->set('pID', $pID);
            $this->set('id', $id);
            if ($id != 0) {
                $this->set('mode', 'update');
            }
        } else {
            $this->set('resp', array('<span class="error">Module not specified</span>'));
        }
    }
    public function content($action='edit') {
        $this->level = 2;
        $args = func_get_args();
        $args = array_splice($args, 1);
        if (method_exists($this, 'content_'.$action)) {
            call_user_func_array(array($this, 'content_'.$action), $args);
        } else {
            $this->set('resp', array('<span class="error">Content action not found.</span>'));
        }
    }
    public function content_edit($id=0) {
        global $db, $common, $session, $purifier;
        $this->set('title', 'Edit page content - Dettol &amp; Lysol Administration');
        $this->set('mode', 'create');
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                $title = htmlentities($purifier->purify($common->getParam('title', 'post', '', false)));
                $html = $common->escape_data($purifier->purify($common->getParam('html', 'post', '', false)));
                if ($common->getParam('id') != 0) {
                    if (!is_null($title)) {
                        $id = $common->getParam('id');
                        $upd = $db->dbQuery("UPDATE tbl_content SET title='$title', html='$html'WHERE id=$id");
                        $this->set('resp', array('<span class="success">updated successfully</span>'));
                    } else {
                        $this->set('resp', array('span class="error">please enter a title</span>'));
                    }
                } else {
                    if (!is_null($title)) {
                        $id = $db->dbQuery("INSERT INTO tbl_content (title, html) VALUES ('$title', '$html')", 'id');
                        $this->set('resp', array('<span class="success">added successfully</span>'));
                    } else {
                        $this->set('resp', array('span class="error">please enter a title</span>'));
                    }
                }
            } else {
                $this->set('resp', array('<span class="error">CSRF error</span>'));
            }
        }
        $session->addVar('form-token', uniqid(mt_rand(), true));
        $this->set('csrf', $session->getVar('form-token'));
        $this->set('data', $this->Admin->getContent($id));
        $this->set('id', $id);
        if ($id != 0) {
            $this->set('mode', 'update');
        }
    }
    public function question($action='edit') {
        $this->level = 2;
        $args = func_get_args();
        $args = array_splice($args, 1);
        if (method_exists($this, 'question_'.$action)) {
            call_user_func_array(array($this, 'question_'.$action), $args);
        } else {
            $this->set('resp', array('<span class="error">Question action not found.</span>'));
        }
    }
    public function resource($action='edit') {
        $this->level = 2;
        $args = func_get_args();
        $args = array_splice($args, 1);
        if (method_exists($this, 'resource_'.$action)) {
            call_user_func_array(array($this, 'resource_'.$action), $args);
        } else {
            $this->set('resp', array('<span class="error">Resource action not found.</span>'));
        }
    }
    public function resource_edit($id=0) {
        global $db, $common, $session;
        $this->set('title', 'Edit resource - Dettol &amp; Lysol Administration');
        $this->set('mode', 'create');
        $this->set('type', 'edit');
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                $catID = $common->getParam('category_id');
                $file = $common->getParam('file', 'file');
                $name = (is_null($common->getParam('name'))) ? $file['name'] : $common->getParam('name');
                $thumb = $common->getParam('thumb', 'file');
                $location = uploadDir.'/resources/';
                
                
                if (!empty($file['name'])) {
                    $fileName = $file['name'];
                    if (!is_dir($location)) {
                        mkdir($location, 0777);
                        mkdir($location.'thumb/', 0777);
                    }
                    $this->moveFile($file['tmp_name'], $location.'/'.$fileName);
                    if (!empty($thumb['name'])) {
                        $thumbName = $thumb['name'];
                        $this->moveFile($thumb['tmp_name'], $location.'thumb/'.$thumbName);
                    } else {
                        $thumbName = $fileName;
                        $fileInfo = pathinfo($file['name']);
                        switch ($fileInfo['extension']) {
                            case 'jpg':
                            case 'jpeg':
                                $this->img = @imagecreatefromjpeg($location.'/'.$fileName);
                                break;
                            case 'gif':
                                $this->img = @imagecreatefromgif($location.'/'.$fileName);
                                break;
                            case 'png':
                                $this->img = @imagecreatefrompng($location.'/'.$fileName);
                                break;
                            default:
                                $this->img = false;
                                break;
                        }
                        if ($this->img) {
                            $this->width = imagesx($this->img);
                            $this->height = imagesy($this->img);
                            $this->resizeImage(100, 140, 'auto');
                            $this->saveImage($location.'thumb/'.$thumbName);
                        } else {
                            $thumbName = 'default.jpg';
                        }
                    }
                    if ($common->getParam('id') != 0) {
                        if (!is_null($name)) {
                            $id = $common->getParam('id');
                            $upd = $db->dbQuery("UPDATE tbl_resources SET category_id=$catID, file='$fileName', name='$name', location='$location', thumb='thumb/$thumbName' WHERE id=$id");
                            $this->set('resp', array('<span class="success">updated successfully</span>'));
                        } else {
                            $this->set('resp', array('<span class="error">please enter a title</span>'));
                        }
                    } else {
                        if (!is_null($name)) {
                            $id = $db->dbQuery("INSERT INTO tbl_resources (category_id, file, name, location, thumb) VALUES ($catID, '$fileName', '$name', '$location', 'thumb/$thumbName')", 'id');
                            $this->set('resp', array('<span class="success">added successfully</span>'));
                        } else {
                            $this->set('resp', array('<span class="error">please enter a title</span>'));
                        }
                    }
                } else {
                    $this->set('resp', array('<span class="error">please upload a file</span>'));
                }
                
                
                
            } else {
                $this->set('resp', array('<span class="error">CSRF error</span>'));
            }
        }
        $session->addVar('form-token', uniqid(mt_rand(), true));
        $this->set('csrf', $session->getVar('form-token'));
        $this->set('data', $this->Admin->getResource($id));
        $this->set('cats', $this->Admin->getResourceCategories());
        $this->set('id', $id);
        if ($id != 0) {
            $this->set('mode', 'update');
        }
    }
    public function resource_category($id=0) {
        global $db, $common, $session, $purifier;
        $this->set('title', 'Edit resource category - Dettol &amp; Lysol Administration');
        $this->set('mode', 'create');
        $this->set('type', 'category');
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                
                
                $title = htmlentities($purifier->purify($common->getParam('title', 'post', '', false)));
                $desc = $common->escape_data($purifier->purify($common->getParam('description', 'post', '', false)));
                if ($common->getParam('id') != 0) {
                    if (!is_null($title)) {
                        $id = $common->getParam('id');
                        $upd = $db->dbQuery("UPDATE tbl_resource_categories SET title='$title', description='$desc' WHERE id=$id");
                        $this->set('resp', array('<span class="success">updated successfully</span>'));
                    } else {
                        $this->set('resp', array('span class="error">please enter a title</span>'));
                    }
                } else {
                    if (!is_null($title)) {
                        $id = $db->dbQuery("INSERT INTO tbl_resource_categories (title, description) VALUES ('$title', '$desc')", 'id');
                        $this->set('resp', array('<span class="success">added successfully</span>'));
                    } else {
                        $this->set('resp', array('<span class="error">please enter a title</span>'));
                    }
                }
            } else {
                $this->set('resp', array('<span class="error">CSRF error</span>'));
            }
        }
        $session->addVar('form-token', uniqid(mt_rand(), true));
        $this->set('csrf', $session->getVar('form-token'));
        $this->set('data', $this->Admin->getResourceCategory($id));
        $this->set('id', $id);
        if ($id != 0) {
            $this->set('mode', 'update');
        }
    }
    public function question_edit($moduleID=0, $id=0) {
        global $db, $common, $session, $purifier;
        $this->set('title', 'Edit question - Dettol &amp; Lysol Administration');
        $this->set('mode', 'create');
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                $code = $common->getParam('code');
                $moduleID = $common->getParam('module_id');
                $question = $common->escape_data($purifier->purify($common->getParam('question', 'post', '', false)));
                $typeID = $common->getParam('type_id');
                $subType = $common->getParam('sub_type', 'post', 0);
                $feedback = $common->escape_data($purifier->purify($common->getParam('feedback', 'post', '', false)));
                if ($common->getParam('id') != 0) {
                    if (!is_null($moduleID) && !is_null($question)) {
                        $id = $common->getParam('id');
                        $upd = $db->dbQuery("UPDATE tbl_module_questions SET code='$code', question='$question', type_id=$typeID, sub_type_id=$subType, feedback='$feedback', updated_by={$session->getVar('id')} WHERE id=$id");
                        $this->set('resp', array('<span class="success">updated successfully</span>'));
                    } else {
                        $this->set('resp', array('<span class="error">Please enter a question</span>'));
                        return;
                    }
                } else {
                    if (!is_null($moduleID) && !is_null($question)) {
                        $id = $db->dbQuery("INSERT INTO tbl_module_questions (code, module_id, question, type_id, sub_type_id, feedback, updated_by) VALUES ('$code', '$moduleID', '$question', $typeID, $subType, '$feedback', {$session->getVar('id')})", 'id');
                        $this->set('resp', array('<span class="success">added successfully</span>'));
                    } else {
                        $this->set('resp', array('<span class="error">Please enter a question</span>'));
                        return;
                    }
                }
                switch ($typeID) {
                    // fact or fiction \\
                    case 1:
                        $correct = $common->getParam('correct', 'post', '');
                        // delete from the answers \\
                        $del = $db->dbQuery("DELETE FROM tbl_module_answers WHERE question_id=$id");
                        foreach ($correct as $i=>$ans) {
                            if (is_array($correct)) {
                                $itemCorrect = $correct[$i];
                            } else {
                                $itemCorrect = 0;
                            }
                            $db->dbQuery("INSERT INTO tbl_module_answers (module_id, question_id, correct) VALUES ($moduleID, $id, $itemCorrect)");
                        }
                        break;
                    // fill in the blank \\
                    case 3:
                    // multiple choice \\
                    case 4:
                    // letter shuffle \\
                    case 5:
                    // scenario based multiple choice \\
                    case 7:
                        $correct = $common->getParam('correct', 'post', '');
                        $ansCode = $common->getParam('ans_code');
                        $answer = $common->getParam('answer');
                        // delete from the answers \\
                        $del = $db->dbQuery("DELETE FROM tbl_module_answers WHERE question_id=$id");
                        foreach ($answer as $i=>$ans) {
                            if (!empty($ans)) {
                                if (is_array($correct) && !empty($correct[$i])) {
                                    $itemCorrect = $correct[$i];
                                } else {
                                    $itemCorrect = 0;
                                }
                                $db->dbQuery("INSERT INTO tbl_module_answers (module_id, code, question_id, answer, correct) VALUES ($moduleID, '{$ansCode[$i]}', $id, '{$common->escape_data($purifier->purify($ans))}', $itemCorrect)");
                            }
                        }
                        break;
                    // drag and drop \\
                    case 2:
                        switch ($subType) {
                            // order \\
                            case 1:
                                $ansCode = $common->getParam('ans_code');
                                $answer = $common->getParam('answer');
                                // delete any existing answers \\
                                $del = $db->dbQuery("DELETE FROM tbl_module_answers WHERE question_id=$id");
                                $jsonArr = array();
                                foreach ($answer as $i=>$ans) {
                                    if (!empty($ans) || !empty($ansCode[$i])) {
                                        $jsonArr[] = array('order'=>$i+1, 'code'=>$ansCode[$i], 'answer'=>$common->escape_data($purifier->purify($ans)));
                                    }
                                }
                                if (!empty($jsonArr)) {
                                    $json = json_encode($jsonArr);
                                    $db->dbQuery("INSERT INTO tbl_module_answers (module_id, question_id, answer) VALUES ($moduleID, $id, '$json')");
                                }
                                break;
                            // statement matching \\
                            case 2:
                                $ansCode = $common->getParam('ans_code');
                                $phrase = $common->getParam('phrase');
                                $statement = $common->getParam('statement');
                                // delete any existing answers \\
                                $del = $db->dbQuery("DELETE FROM tbl_module_answers WHERE question_id=$id");
                                $jsonArr = array();
                                foreach ($phrase as $i=>$ans) {
                                    if (!empty($ansCode[$i]) || !empty($ans) || !empty($statement[$i])) {
                                        $jsonArr[] = array('code'=>$purifier->purify($ansCode[$i]), 'phrase'=>$purifier->purify($ans), 'statement'=>$purifier->purify($statement[$i]));
                                    }
                                }
                                if (!empty($jsonArr)) {
                                    $json = json_encode($jsonArr);
                                    $db->dbQuery("INSERT INTO tbl_module_answers (module_id, question_id, answer) VALUES ($moduleID, $id, '$json')");
                                }
                                break;
                            case 3:
                                $col = $common->getParam('column');
                                $pre = $common->getParam('pre-filled');
                                $ass = $common->getParam('assigned');
                                $ans = $common->getParam('answer');
                                $cor = $common->getParam('cor');
                                
                                // delete existing answers \\
                                $del = $db->dbQuery("DELETE FROM tbl_module_answers WHERE question_id=$id");
                                $jsonArr = array('columns'=>array(), 'pre-filled'=>array(), 'answer'=>array());
                                foreach ($col as $i=>$column) {
                                    if (!empty($column)) {
                                        $jsonArr['columns'][$i] = $purifier->purify($column);
                                    }
                                }
                                foreach ($pre as $j=>$prefilled) {
                                    if (!empty($prefilled)) {
                                        $jsonArr['pre-filled'][$j] = array('text'=>$common->escape_data($purifier->purify($prefilled)), 'column'=>$ass[$j]);
                                    }
                                }
                                $offs = 0;
                                foreach ($ans as $k=>$answer) {
                                    if (!empty($answer)) {
                                        $jsonArr['answer'][$k-$offs] = array('text'=>$common->escape_data($purifier->purify($answer)), 'column'=>$cor[$k-$offs]);
                                    } else {
                                        $offs++;
                                    }
                                }
                                if (!empty($jsonArr)) {
                                    $json = json_encode($jsonArr);
                                    $db->dbQuery("INSERT INTO tbl_module_answers (module_id, question_id, answer) VALUES ($moduleID, $id, '$json')");
                                }
                                break;
                            case 4:
                                $ansID = $common->getParam('answer_id');
                                $correct = $common->getParam('correct', 'post', '');
                                $answer = $common->getParam('desc');
                                $image = $common->getParam('image', 'file');
                                $resp = array();
                                $json = array('answers'=>array());
                                if (!isset($upd) || is_null($ansID)) {
                                    if (!empty($image['name'])) {
                                        $file = $image;
                                        $fileInfo = pathinfo($file['name']);
                                        $targetPath = uploadDir.'/answers';
                                        $targetFile = rtrim($targetPath,'/') . '/' . time() . '-' . $file['name'];
                                        if (!file_exists($targetPath)) {
                                            if (!mkdir($targetPath, 0777, true)) {
                                                $resp[] = '<span class="error">The directory could not be created.</span>';
                                                return;
                                            }
                                        }
                                        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
                                        if (array_search(strtolower($fileInfo['extension']), $allowed) !== false) {
                                            if ($this->moveFile($file['tmp_name'], $targetFile)) {
                                                $json['image'] = $targetFile;
                                            } else {
                                                $resp[] = '<span class="error">File could not be uploaded.</span>';
                                            }
                                        } else {
                                            $resp[] = '<span class="error">Invalid file type</span>';
                                        }
                                    }
                                    
                                    if (!empty($resp)) {
                                        $this->set('resp', $resp);
                                    } else {
                                        foreach ($answer as $i=>$ans) {
                                            if (!empty($ans)) {
                                                if (is_array($correct)) {
                                                    $itemCorrect = intval($correct[$i]);
                                                } else {
                                                    $itemCorrect = 0;
                                                }
                                                $json['answers'][] = array('desc'=>$common->escape_data($purifier->purify($ans)), 'correct'=>$itemCorrect);
                                            }
                                        }
                                        if (!empty($json)) {
                                            $jsonArr = json_encode($json);
                                            $db->dbQuery("INSERT INTO tbl_module_answers (module_id, question_id, answer) VALUES ($moduleID, $id, '$jsonArr')");
                                        }
                                    }
                                } else {
                                    $curAns = $db->dbResult($db->dbQuery("SELECT answer FROM tbl_module_answers WHERE id=$ansID"));
                                    if ($curAns[1] > 0) {
                                        $json = json_decode($curAns[0][0]['answer'], true);
                                    }
                                    if (!empty($image['name'])) {
                                        $file = $image;
                                        $fileInfo = pathinfo($file['name']);
                                        $targetPath = uploadDir.'/answers';
                                        $targetFile = rtrim($targetPath,'/') . '/' . time() . '-' . $file['name'];

                                        if (!file_exists($targetPath)) {
                                            if (!mkdir($targetPath, 0777, true)) {
                                                $resp[] = '<span class="error">The directory could not be created.</span>';
                                                return;
                                            }
                                        }
                                        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
                                        if (array_search($fileInfo['extension'], $allowed) !== false) {
                                            if ($this->moveFile($file['tmp_name'], $targetFile)) {
                                                $json['image'] = $targetFile;
                                            } else {
                                                $resp[] = '<span class="error">File could not be uploaded.</span>';
                                            }
                                        } else {
                                            $resp[] = '<span class="error">Invalid file type</span>';
                                        }
                                    }
                                    
                                    if (!empty($resp)) {
                                        $this->set('resp', $resp);
                                    } else {
                                        $json['answers'] = array();
                                        foreach ($answer as $i=>$ans) {
                                            if (!empty($ans)) {
                                                if (is_array($correct)) {
                                                    $itemCorrect = intval($correct[$i]);
                                                } else {
                                                    $itemCorrect = 0;
                                                }
                                                $json['answers'][] = array('desc'=>$common->escape_data($purifier->purify($ans)), 'correct'=>$itemCorrect);
                                            }
                                        }
                                        if (!empty($json)) {
                                            $jsonArr = json_encode($json);
                                            $db->dbQuery("UPDATE tbl_module_answers SET answer='$jsonArr' WHERE id=$ansID");
                                        }
                                    }
                                }
                                break;
                        }
                        break;
                    // picture based multiple choice \\
                    case 6:
                        $ansID = $common->getParam('answer_id');
                        $correct = $common->getParam('correct', 'post', '');
                        $ansCode = $common->getParam('ans_code');
                        $answer = $common->getParam('answer');
                        $image = $common->getParam('image', 'file');
                        $resp = array();
                        if (!isset($upd)) {
                            foreach ($answer as $i=>$ans) {
                                if (!empty($ans)) {
                                    if (!empty($image['name'][$i])) {
                                        $file = $image;
                                        $fileInfo = pathinfo($file['name'][$i]);
                                        $targetPath = uploadDir.'/answers';
                                        $targetFile = rtrim($targetPath,'/') . '/' . time() . '-' . $file['name'][$i];

                                        if (!file_exists($targetPath)) {
                                            if (!mkdir($targetPath, 0777, true)) {
                                                $resp[] = '<span class="error">The directory could not be created.</span>';
                                                return;
                                            }
                                        }
                                        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
                                        if (array_search($fileInfo['extension'], $allowed) !== false) {
                                            if ($this->moveFile($file['tmp_name'][$i], $targetFile)) {
                                                $json = json_encode(array('answer'=>$common->escape_data($purifier->purify($ans)), 'file'=>$targetFile));
                                                if (is_array($correct)) {
                                                    $itemCorrect = $correct[$i];
                                                } else {
                                                    $itemCorrect = 0;
                                                }
                                                $db->dbQuery("INSERT INTO tbl_module_answers (module_id, code, question_id, answer, correct) VALUES ($moduleID, '{$ansCode[$i]}', $id, '$json', $itemCorrect)");
                                            } else {
                                                $resp[] = '<span class="error">File could not be uploaded.</span>';
                                            }
                                        } else {
                                            $resp[] = '<span class="error">Invalid file type</span>';
                                        }
                                    }
                                }
                            }
                            if (!empty($resp)) {
                                $this->set('resp', $resp);
                            }
                        } else {
                            foreach ($answer as $i=>$ans) {
                                if (!empty($ans)) {
                                    if (!empty($image['name'][$i])) {
                                        $file = $image;
                                        $fileInfo = pathinfo($file['name'][$i]);
                                        $targetPath = uploadDir.'/answers';
                                        $targetFile = rtrim($targetPath,'/') . '/' . time() . '-' . $file['name'][$i];

                                        if (!file_exists($targetPath)) {
                                            if (!mkdir($targetPath, 0777, true)) {
                                                $resp[] = '<span class="error">The directory could not be created.</span>';
                                                return;
                                            }
                                        }
                                        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
                                        if (array_search($fileInfo['extension'], $allowed) !== false) {
                                            if ($this->moveFile($file['tmp_name'][$i], $targetFile)) {
                                                $json = json_encode(array('answer'=>$common->escape_data($purifier->purify($ans)), 'file'=>$targetFile));
                                                if (is_array($correct)) {
                                                    $itemCorrect = $correct[$i];
                                                } else {
                                                    $itemCorrect = 0;
                                                }
                                                $db->dbQuery("UPDATE tbl_module_answers SET code='{$ansCode[$i]}', answer='$json', correct=$itemCorrect WHERE id={$ansID[$i]}");
                                            } else {
                                                $resp[] = '<span class="error">File could not be uploaded.</span>';
                                            }
                                        } else {
                                            $resp[] = '<span class="error">Invalid file type</span>';
                                        }
                                    } else {
                                        // means no new upload \\
                                        $cur = $db->dbQuery("SELECT answer FROM tbl_module_answers WHERE id={$ansID[$i]}");
                                        if ($cur[1] > 0) {
                                            $curAns = json_decode($cur[0][0]['answer'], true);
                                            if (!is_null($curAns)) {
                                                $curAns['answer'] = $common->escape_data($purifier->purify($ans));
                                            }
                                            $newAns = json_encode($curAns);
                                            $db->dbQuery("UPDATE tbl_module_answers SET code='{$ansCode[$i]}', answer='$newAns', correct=$itemCorrect WHERE id={$ansID[$i]}");
                                        }
                                    }
                                }
                            }
                            if (!empty($resp)) {
                                $this->set('resp', $resp);
                            }
                        }
                        break;
                }
            } else {
                $this->set('resp', array('<span class="error">CSRF error</span>'));
            }
        }
        if ($moduleID != 0) {
            $session->addVar('form-token', uniqid(mt_rand(), true));
            $this->set('csrf', $session->getVar('form-token'));
            $this->set('data', $this->Admin->getQuestion($id));
            $this->set('id', $id);
            $this->set('moduleID', $moduleID);
            if ($id != 0) {
                $this->set('mode', 'update');
            }
        } else {
            $this->set('resp', array('<span class="error">Module not specified</span>'));
        }
    }
    public function question_types($id=0, $typeID=0, $subTypeID=0) {
        $this->_template->xhr = true;
        $this->level = 2;
        $this->set('id', $id);
        $this->set('typeID', $typeID);
        $this->set('subTypeID', $subTypeID);
        $this->set('desc', $this->Admin->getQuestionTypeDesc($typeID));
    }
    public function ordering($item='') {
        global $db, $common, $session;
        $common->isPage = false;
        $this->isJSON = true;
        if (!is_null($common->getParam('submitted'))) {
            if ($common->getParam('form-token') == $session->getVar('form-token')) {
                $IDs = $common->getParam('IDs');
                if (is_array($IDs) && count($IDs) > 0) {
                    switch ($item) {
                        case 'module':
                            $tbl = 'tbl_modules';
                            break;
                        case 'page':
                            $tbl = 'tbl_module_pages';
                            break;
                        case 'content':
                            $tbl = 'tbl_content';
                            break;
                    }
                    if (isset($tbl)) {
                        foreach ($IDs as $key=>$id) {
                            $i = $key+1;
                            $db->dbQuery("UPDATE $tbl SET ordering=$i WHERE id=$id");
                        }
                        $this->json = array('status'=>'success');
                        return;
                    } else {
                        $this->json = array('status'=>'table', 'message'=>'Item type not defined');
                    }
                }
                $this->json = array('status'=>'IDs', 'message'=>'You must provide an array of IDs');
                return;
            } else {
                $this->json = array('status'=>'CSRF', 'message'=>'CSRF error');
                return;
            }
        } else {
            $this->json = array('status'=>'submitted', 'message'=>'Malformed form submission');
            return;
        }
    }
    public function updateNav() {
        global $common, $db;
        $common->isPage = false;
        $this->isJSON = true;
        $this->level = 2;
        if (!is_null($common->getParam('submitted'))) {
            $nav_link = $common->getParam('nav_link');
            $nav_title = $common->getParam('nav_title');
            if (is_array($nav_title)) {
                $navItems = $this->processNav($nav_title, $nav_link);
            }
        }
        if (!empty($navItems)) {
            $navItemsStr = $common->escape_data(json_encode($navItems));
            $db->dbQuery("DELETE FROM tbl_config WHERE title='navigation'");
            $db->dbQuery("INSERT INTO tbl_config (title, value) VALUES ('navigation', '$navItemsStr')");
        }
        $this->json = $navItems;
    }
    private function processNav($nav_title, $nav_link, $level=0) {
        if (!empty($nav_title)) {
            $item = array();
            foreach ($nav_title as $i=>$nav) {
                if (is_array($nav)) {
                    $item[$i] = array();
                    if ($level == 0) {
                        $item[$i]['sub'] = $this->processNav($nav, $nav_link[$i], ++$level);
                        $item[$i]['link'] = htmlentities($nav_link[$i]['nav_link']);
                        $item[$i]['title'] = htmlentities($nav_title[$i]['nav_title']);
                    } else {
                        $item[$i]['sub'] = $this->processNav($nav, $nav_link[$i], ++$level);
                        $item[$i]['link'] = htmlentities($nav_link[$i]['nav_link']);
                        $item[$i]['title'] = htmlentities($nav_title[$i]['nav_title']);
                    }
                }
            }
        }
        return $item;
    }
    private function moveFile($src='', $dest='') {
        if (!empty($src) && !empty($dest)) {
            if (!@move_uploaded_file($src, $dest)) {
                return false;
            }
            return true;
        }
        return false;
    }
    private function resizeImage($newWidth, $newHeight, $option="auto") {  
  
        // *** Get optimal width and height - based on $option  
        $optionArray = $this->getDimensions($newWidth, $newHeight, strtolower($option));  

        $optimalWidth  = $optionArray['optimalWidth'];  
        $optimalHeight = $optionArray['optimalHeight'];  
        // *** Resample - create image canvas of x, y size  
        $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);  
        imagecopyresampled($this->imageResized, $this->img, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);  

        // *** if option is 'crop', then crop too  
        if ($option == 'crop') {  
            $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);  
        }  
    }
    
    private function getDimensions($newWidth, $newHeight, $option) {  

       switch ($option)  
        {  
            case 'exact':  
                $optimalWidth = $newWidth;  
                $optimalHeight= $newHeight;  
                break;  
            case 'portrait':  
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);  
                $optimalHeight= $newHeight;  
                break;  
            case 'landscape':  
                $optimalWidth = $newWidth;  
                $optimalHeight= $this->getSizeByFixedWidth($newWidth);  
                break;  
            case 'auto':  
                $optionArray = $this->getSizeByAuto($newWidth, $newHeight);  
                $optimalWidth = $optionArray['optimalWidth'];  
                $optimalHeight = $optionArray['optimalHeight'];  
                break;  
            case 'crop':  
                $optionArray = $this->getOptimalCrop($newWidth, $newHeight);  
                $optimalWidth = $optionArray['optimalWidth'];  
                $optimalHeight = $optionArray['optimalHeight'];  
                break;  
        }  
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);  
    }
    private function getSizeByFixedHeight($newHeight)  
    {  
        $ratio = $this->width / $this->height;  
        $newWidth = $newHeight * $ratio;  
        return $newWidth;  
    }  

    private function getSizeByFixedWidth($newWidth)  
    {  
        $ratio = $this->height / $this->width;  
        $newHeight = $newWidth * $ratio;  
        return $newHeight;  
    }  

    private function getSizeByAuto($newWidth, $newHeight)  
    {  
        if ($this->height < $this->width) {  
            $optimalWidth = $newWidth;  
            $optimalHeight= $this->getSizeByFixedWidth($newWidth);  
        } elseif ($this->height > $this->width) {  
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);  
            $optimalHeight= $newHeight;  
        } else {  
            if ($newHeight < $newWidth) {  
                $optimalWidth = $newWidth;  
                $optimalHeight= $this->getSizeByFixedWidth($newWidth);  
            } else if ($newHeight > $newWidth) {  
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);  
                $optimalHeight= $newHeight;  
            } else {  
                // *** Sqaure being resized to a square  
                $optimalWidth = $newWidth;  
                $optimalHeight= $newHeight;  
            }  
        }  

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);  
    }  

    private function getOptimalCrop($newWidth, $newHeight) {  

        $heightRatio = $this->height / $newHeight;  
        $widthRatio  = $this->width /  $newWidth;  

        if ($heightRatio < $widthRatio) {  
            $optimalRatio = $heightRatio;  
        } else {  
            $optimalRatio = $widthRatio;  
        }  

        $optimalHeight = $this->height / $optimalRatio;  
        $optimalWidth  = $this->width  / $optimalRatio;  

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);  
    }
    
    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight) {  
        // *** Find center - this will be used for the crop  
        $cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );  
        $cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );  

        $crop = $this->imageResized;  
        //imagedestroy($this->imageResized);  

        // *** Now crop from center to exact requested size  
        $this->imageResized = imagecreatetruecolor($newWidth , $newHeight);  
        imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);  
    }
    public function saveImage($savePath, $imageQuality="100") {  
        // *** Get extension  
            $ext = pathinfo($savePath);  

        switch($ext['extension']) {  
            case 'jpg':  
            case 'jpeg':  
                if (imagetypes() & IMG_JPG) {  
                    imagejpeg($this->imageResized, $savePath, $imageQuality);  
                }  
                break;  

            case 'gif':  
                if (imagetypes() & IMG_GIF) {  
                    imagegif($this->imageResized, $savePath);  
                }  
                break;  

            case 'png':  
                // *** Scale quality from 0-100 to 0-9  
                $scaleQuality = round(($imageQuality/100) * 9);  

                // *** Invert quality setting as 0 is best, not 9  
                $invertScaleQuality = 9 - $scaleQuality;  

                if (imagetypes() & IMG_PNG) {  
                    imagepng($this->imageResized, $savePath, $invertScaleQuality);  
                }  
                break;  

            default:  
                // *** No extension - No save.  
                break;  
        }  

        imagedestroy($this->imageResized);  
    }
}
