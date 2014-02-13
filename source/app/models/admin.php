<?php
/**
 * Dettol / Lysol - 2013
 */
class Admin extends Model {
    public function getModule($id=0) {
        global $db;
        $data = array();
        if ($id != 0) {
            $data = $db->dbResult($db->dbQuery("SELECT * FROM tbl_modules WHERE id=$id"));
            if ($data[1] > 0) {
                return $data[0][0];
            }
        }
        return $data;
    }
    public function getModules() {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT * FROM tbl_modules ORDER BY ordering ASC"));
        if ($sel[1] > 0) {
            return $sel[0];
        }
        return array();
    }
    public function getLastModule() {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT id, title, ordering FROM tbl_modules ORDER BY ordering DESC LIMIT 1"));
        if ($sel[1] > 0) {
            return $sel[0][0];
        }
        return array();
    }
    public function getPage($id=0) {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT * FROM tbl_module_pages WHERE id=$id"));
        if ($sel[1] > 0) {
            return $sel[0][0];
        }
        return array();
    }
    public function getLastPage($id=0) {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT id, title, ordering FROM tbl_module_pages WHERE module_id=$id ORDER BY ordering DESC LIMIT 1"));
        if ($sel[1] > 0) {
            return $sel[0][0];
        }
        return array();
    }
    public function getPages($id=0) {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT id, title, html FROM tbl_module_pages WHERE module_id=$id ORDER BY ordering ASC"));
        if ($sel[1] > 0) {
            return $sel[0];
        }
        return array();
    }
    public function getContent($id=0) {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT * FROM tbl_content WHERE id=$id"));
        if ($sel[1] > 0) {
            return $sel[0][0];
        }
        return array();
    }
    public function getContentPages() {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT id, title, html FROM tbl_content"));
        if ($sel[1] > 0) {
            return $sel[0];
        }
        return array();
    }
    public function getQuestions($id=0) {
        global $db;
        if ($id != 0) {
            $que = $db->dbResult($db->dbQuery("SELECT q.id, q.code, q.question, qt.title FROM tbl_module_questions AS q
                                               LEFT JOIN tbl_module_question_types AS qt ON qt.id=q.type_id
                                               WHERE q.module_id=$id"));
            if ($que[1] > 0) {
                return $que[0];
            }
        }
        return array();
    }
    public function getQuestion($id=0) {
        global $db;
        if ($id != 0) {
            $que = $db->dbResult($db->dbQuery("SELECT * FROM tbl_module_questions WHERE id=$id"));
            if ($que[1] > 0) {
                return $que[0][0];
            }
        }
        return array();
    }
    public function getQuestionTypes($id=0) {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT id, title FROM tbl_module_question_types ORDER BY title ASC"));
        if ($sel[1] > 0) {
            return $sel[0];
        }
        return array();
    }
    public function getQuestionTypeDesc($id=0) {
        global $db;
        $sel = $db->dbResult($db->dbQuery("SELECT description FROM tbl_module_question_types WHERE id=$id"));
        
        if ($sel[1] > 0) {
            return $sel[0][0]['description'];
        }
        return '';
    }
    public function getQuestionSubTypes($id=0) {
        global $db;
        if ($id != 0) {
            $types = $db->dbResult($db->dbQuery("SELECT id, sub_type, description FROM tbl_module_question_types_sub WHERE type_id=$id"));
            if ($types[1] > 0) {
                return $types[0];
            }
        }
        return array();
    }
    public function getAnswers($id=0) {
        global $db;
        if ($id != 0) {
            $sel = $db->dbResult($db->dbQuery("SELECT a.id, a.code, a.answer, a.correct, q.type_id FROM tbl_module_answers AS a
                                               LEFT JOIN tbl_module_questions AS q ON q.id=a.question_id
                                               WHERE a.question_id=$id"));
            if ($sel[1] > 0) {
                return $sel[0];
            }
        }
        return array();
    }
    public function getResources() {
        global $db;
        $res = $db->dbResult($db->dbQuery("SELECT r.*, rc.title AS resource_title FROM tbl_resources AS r
                                           LEFT JOIN tbl_resource_categories AS rc ON rc.id=r.category_id"));
        if ($res[1] > 0) {
            return $res[0];
        }
        return array();
    }
    public function getResource($id=0) {
        global $db;
        if ($id != 0) {
            $res = $db->dbResult($db->dbQuery("SELECT r.*, rc.id FROM tbl_resources AS r
                                               LEFT JOIN tbl_resource_categories AS rc ON rc.id=r.category_id
                                               WHERE r.id=$id"));
            if ($res[1] > 0) {
                return $res[0][0];
            }
        }
        return array();
    }
    public function getResourceCategories() {
        global $db;
        $cats = $db->dbResult($db->dbQuery("SELECT * FROM tbl_resource_categories"));
        if ($cats[1] > 0) {
            return $cats[0];
        }
        return array();
    }
    public function getResourceCategory($id=0) {
        global $db;
        $cats = $db->dbResult($db->dbQuery("SELECT * FROM tbl_resource_categories WHERE id=$id"));
        if ($cats[1] > 0) {
            return $cats[0][0];
        }
        return array();
    }
    public function displayNav($nav) {
        $output = '';
        foreach ($nav as $item) {
            $output .= $this->navOutput($item).'<input type="button" value="Add child item" name="add[]" class="addNav" /><input type="button" name="remove[]" value="X" class="removeNav" /></span>';
            if (isset($item->sub)) {
                $output .= '<ul>'.$this->displayNav($item->sub).'</ul>';
            }
            $output .= '</li>';
        }
        return $output;
    }
    public function navOutput($item) {
        if (isset($item->title)) {
            return '<li><span><input type="text" name="nav_link" '.((isset($item->link)) ? 'value="'.$item->link.'"' : '').' placeholder="Link" /><input type="text" name="nav_title" value="'.$item->title.'" placeholder="Title" />';
        }
    }
}
?>
