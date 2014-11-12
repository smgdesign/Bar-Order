<?php
/*
 * Dettol / Lysol - 2013
 */

/**
 * the main database class
 *
 * @author Richard
 */
class db {
    protected $self = array();
    public function __construct($dbArray) {
        $this->self['user'] = $dbArray['user'];
        $this->self['password'] = $dbArray['password'];
        $this->self['host'] = $dbArray['host'];
        $this->self['name'] = $dbArray['name'];
        $this->dbConnect();
    }
    public function __get($name) {
        return $this->self[$name];
    }
    private function dbConnect() {
        $this->self['dbc'] = mysqli_connect ($this->self['host'], $this->self['user'], $this->self['password']) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );
        mysqli_select_db ($this->self['dbc'], $this->self['name']) OR die ('Could not select the database: ' . mysqli_error($this->dbc) );
    }

    /**
    * function dbQuery
    * description: query database
    * values {
    *	fields: csv table fields
    *	tbl: table
    *	conditions: optional WHERE condition
    *	order {
    *		field: str
    *		dir: str
    *	}
    * }
    */

    public function dbQuery($statement, $state='select') {
        if (debug == 'develop') {
            $mysqlQuery = mysqli_query($this->dbc, $statement) or die("<div class=\"php_error\">Query of <strong>".$statement."</strong> failed with error: ".mysqli_error($this->dbc)."</div>");
        } else {
            $mysqlQuery = @mysqli_query($this->dbc, $statement);
        }
        if ($state != 'id') {
            return $mysqlQuery;
        } else {
            return mysqli_insert_id($this->dbc);
        }
    }

    public function dbResult($result, $type=MYSQL_ASSOC) {
        $returnArray = array();
        $check = (mysqli_num_rows($result) > 0) ? true : false;
        $rowTotal = mysqli_num_rows($result);
        if ($check) {
            while ($resultRow = mysqli_fetch_array($result, $type)) {
                $returnArray[0][] = $resultRow;
            }
        } else {
            $returnArray = false;
        }
        if (is_array($returnArray)) {
            $returnArray[] = mysqli_num_rows($result);
        }
        return $returnArray;
    }

    function __destruct() {
        mysqli_close($this->self['dbc']);
    }
}
?>
