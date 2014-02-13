<?php

/*
 * Dettol / Lysol - 2013
 */

/**
 * The class for all common functions
 *
 * @author Richard
 */
class common {
    protected $self = array();
    var $isPage = true;
    public function __get($name = null) {
        return $this->self[$name];
    }
    public function __set($name, $value) {
        $this->self[$name] = $value;
    }
    public function curlRequest($url) {
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }
    
    public function curlPOSTRequest($url, $data) {
        global $session;
        set_time_limit(60);
        $output = array();
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_HEADER, 0);
        curl_setopt($curlSession, CURLOPT_POST, 1);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($curlSession, CURLOPT_TIMEOUT,30); 

        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

        $rawresponse = curl_exec($curlSession);
        //Store the raw response for later as it's useful to see for integration and understanding 
        $session->addVar('rawresponse', $rawresponse);
        //Split response into name=value pairs
        $response = split(chr(10), $rawresponse);
        // Check that a connection was made
        if (curl_error($curlSession)){
        // If it wasn't...
                $output['Status'] = "FAIL";
                $output['StatusDetail'] = curl_error($curlSession);
        }

        // Close the cURL session
        curl_close ($curlSession);

        // Tokenise the response
        for ($i=0; $i<count($response); $i++){
                // Find position of first "=" character
                $splitAt = strpos($response[$i], "=");
                // Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
                $output[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt+1)));
        } // END for ($i=0; $i<count($response); $i++)

        // Return the output
        return $output;
    }

    /**
    * function: escape_data
    * description: make all values safe before adding to database
    * values {
    *	data: string
    * }
    */

    public function escape_data ($data) {
        global $db;
        if (is_string($data)) {
            if (ini_get('magic_quotes_gpc')) {
                $data = stripslashes($data);
            }
            if (function_exists('mysqli_real_escape_string') && $db->dbc !== false) {
                $data = @mysqli_real_escape_string ($db->dbc, trim($data));
            } else {
                $data = @mysqli_escape_string ($db->dbc, trim($data));
            }
        }
        return $data;
    }

    /**
     * function: shorten
     * description: shorten a string (given) to the length provided
     * values {
     *       str: string
     *       len: int
     * }
     */
    public function shorten($string, $your_desired_width) {
        $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $your_desired_width) { break; }
        }

        return implode(array_slice($parts, 0, $last_part));
    }

    public function getAllParam($type='post') {
        switch (strtolower($type)) {
            case 'get':
                $type = $_GET;
            break;
            case 'cookie':
                $type = $_COOKIE;
            break;
            case 'server':
                $type = $_SERVER;
            break;
            case 'file':
                $type = $_FILES;
            break;
            case 'request':
                $type = $_REQUEST;
            break;
            default:
                $type = $_POST;
            break;
        }
        return $type;
    }

    public function getParam($name, $type='post', $default=NULL, $escape=true) {
        switch (strtolower($type)) {
            case 'get':
                $type = $_GET;
            break;
            case 'cookie':
                $type = $_COOKIE;
            break;
            case 'server':
                $type = $_SERVER;
            break;
            case 'file':
                $type = $_FILES;
            break;
            case 'request':
                $type = $_REQUEST;
            break;
            default:
                $type = $_POST;
            break;	
        }
        if (isset($type[$name])) {
            if ($escape) {
                return $this->escape_data($type[$name]);
            } else {
                return $type[$name];
            }
        } else {
            return $default;
        }
    }
    public function array_slice_assoc ($array, $key, $length, $preserve_keys = true) {
        $offset = array_search($key, array_keys($array));
        if (is_string($length)) {
            $length = array_search($length, array_keys($array)) - $offset;
        }
        return array_slice($array, $offset, $length, $preserve_keys);
    }
    public function getTblData($tbl, $order='') {
        global $db;
        if (!is_null($tbl)) {
            $data = $db->dbResult($db->dbQuery("SELECT * FROM $tbl $order"));
            if ($data[1] > 0) {
                return $data[0];
            }
        }
        return array();
    }
    public function reindex(array $source) {
        $i = 0;
        foreach ($source as $key => $val) {
            if ($key != $i) {
                unset($source[$key]);
                $source[$i] = $val;
            }
            $i++;
        }
        return $source;
    }
    public function bytesToSize($bytes, $precision = 2) {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            return $bytes . ' B';

        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
            return round($bytes / $kilobyte, $precision) . ' KB';

        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MB';

        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            return round($bytes / $gigabyte, $precision) . ' GB';

        } elseif ($bytes >= $terabyte) {
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }
    public function array_merge_recursive_new() {

        $arrays = func_get_args();
        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            reset($base); //important
            while (list($key, $value) = @each($array)) {
                if (is_array($value) && @is_array($base[$key])) {
                    $base[$key] = $this->array_merge_recursive_new($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }
    public function array_search_sub($array, $key, $value) {
        foreach ($array as $subArray) {
            if (isset($subArray[$key]) && $subArray[$key] == $value) {
                return TRUE;
            }
        }
        return FALSE;
    }
    public function setReporting() {
        global $logging;
        ini_set('log_errors', 'On');
        ini_set('error_log', ROOT.'/tmp/logs/error.log');
        if (debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
            set_error_handler(array(&$logging, debug.'Handler'));
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'Off');
        }
    }
    public function logData($file='', $data=array(), $mode='develop') {
        if (debug == $mode) {
            $fh = fopen(ROOT .'/tmp/logs/'.$file, 'w');
            if ($fh) {
                fwrite($fh, print_r($data, true));
                fclose($fh);
            }
        }
    }
    public function stripSlashesDeep($value) {
        $value = is_array($value) ? array_map(array('common', 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }
 
    public function removeMagicQuotes() {
        if (get_magic_quotes_gpc()) {
            $_GET = stripSlashesDeep($_GET);
            $_POST = stripSlashesDeep($_POST);
            $_COOKIE = stripSlashesDeep($_COOKIE);
        }
    }
    function unregisterGlobals() {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }
    public function numbers($num, $caps = false) {
        $nums = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty', 'twenty one', 'twenty two', 'twenty three', 'twenty four', 'twenty five', 'twenty six', 'twenty seven', 'twenty eight', 'twenty nine', 'thirty');
        if ($num) {
            return ($caps) ? ucfirst($nums[$num]) : $nums[$num];
        }
        return '';
    }
    public function arrayToInt(&$val) {
        $val = intval($val);
    }
    
    public function callHook() {
    	$url = $this->getParam('url', 'get');
        $url = ($url[0] == '/') ? substr($url, 1) : $url;
        if (is_null($url)) {
            $url = 'web';
        }
        $urlArray = array();
        $urlArray = explode("/",$url);
        
        $controller = $urlArray[0];
        array_shift($urlArray);
        if (array_key_exists(0, $urlArray) === false) {
            $urlArray = array('');
        }
        $action = $urlArray[0];
        array_shift($urlArray);
        $queryString = $urlArray;

        if (empty($action)) {
            $action = 'index';
        }
        $controllerName = $controller;
        $controller = ucwords($controller);
        $model = rtrim($controller, 's');
        if (!file_exists(ROOT . DS . 'app'. DS . 'controllers' . DS .strtolower($controller).'controller.php')) {
            $controller = 'Errors';
            $model = 'Error';
            $controllerName = 'errors';
            $action = 'not_found';
        }
        $controller .= 'Controller';
        if ((int)method_exists($controller, $action)) {
            $dispatch = new $controller($model,$controllerName,$action, $queryString);
            call_user_func_array(array($dispatch,$action),$queryString);
        } else {
            $controller = 'Errors';
            $model = 'Error';
            $controllerName = 'errors';
            $action = 'not_found';
            $controller .= 'Controller';
            $dispatch = new $controller($model,$controllerName,$action, $queryString);
            if ((int)method_exists($controller, $action)) {
                call_user_func_array(array($dispatch,$action),$queryString);
            }
        }
    }
    
    // controller loader \\
    public function loadController($controller=null, $action=null) {
        if (!is_null($controller)) {
            $model = rtrim($controller, 's');
            $controllerName = $controller;
            $controller = strtolower($controller);
            require_once (ROOT . DS . 'lib' . DS . 'app'. DS . 'controllers' . DS .$controller.'.php');
            $controllerName = $controller.'Controller';
            return new $controllerName($model,$controllerName, $action);
        }
    }
}

?>
