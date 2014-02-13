<?php
class imap {
    var $connection;
    var $connectionArr = array();
    function __construct($host,$port,$user,$pass,$folder="",$ssl=false) {
        $ssl=($ssl==false)?"/novalidate-cert":"";
        $this->connection = (@imap_open("{"."$host:$port$ssl"."}$folder",$user,$pass));
        if ($this->connection === false) {
            print_r(imap_errors());
            return false;
        }
        $this->connectionArr['host'] = $host;
        $this->connectionArr['port'] = $port;
        $this->connectionArr['user'] = $user;
        $this->connectionArr['pass'] = $pass;
        return true;
    }
    function mail_loginmbox($user,$pass,$folder="") {
        if ($this->connection !== false) {
            $this->connection = (@imap_open("$folder",$user,$pass));
            if ($this->connection === false) {
                print_r(imap_errors());
                return false;
            }
            return true;
        }
        return false;
    }
    function mail_stat() {
        $check = imap_mailboxmsginfo($this->connection);
        return ((array)$check);
    }
    function mail_folders($host, $port, $ssl=false, $ref='*') {
        return imap_list($this->connection, "{"."$host:$port$ssl"."}", $ref);
    }
    function mail_foldersmbox($host, $port, $ssl=false, $ref='*') {
        return imap_listmailbox($this->connection, "{"."$host:$port$ssl"."}", $ref);
    }
    function mail_status($host, $port, $folder="INBOX", $ssl=false) {
        return imap_status($this->connection, "{"."$host:$port$ssl"."}$folder", SA_ALL);
    }
    function mail_creatembox($folder, $ssl=false) {
        if (!$this->mail_status($this->connectionArr['host'], $this->connectionArr['port'], $folder)) {
            return imap_createmailbox($this->connection, "{"."{$this->connectionArr['host']}:{$this->connectionArr['port']}$ssl"."}$folder");
        } else {
            return 0;
        }
    }
    function mail_move($msgno, $dest="INBOX") {
        $this->mail_creatembox($dest);
        return imap_mail_move($this->connection, $msgno, $dest);
    }
    function mail_append($host, $port, $message, $folder="Sent Items", $ssl=false) {
        return imap_append($this->connection, "{"."$host:$port$ssl"."}$folder", $message);
    }
    function mail_list($message="") {
        if ($message) {
            $range=$message;
        } else {
            $MC = imap_check($this->connection);
            $range = "1:".$MC->Nmsgs;
        }
        $response = imap_fetch_overview($this->connection,$range);
        if (count($response) > 0) {
            foreach ($response as $msg) {
                $result[$msg->msgno]=(array)$msg;
            }
        } else {
            $result = array();
        }
        return $result;
    }
    function mail_retr($message) {
        return(imap_fetchheader($this->connection,$message,FT_PREFETCHTEXT));
    }
    function mail_dele($message) {
        return(imap_delete($this->connection,$message));
    }
    function mail_parse_headers($headers) {
        $headers=preg_replace('/\r\n\s+/m', '',$headers);
        preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
        foreach ($matches[1] as $key =>$value) $result[$value]=$matches[2][$key];
        return($result);
    }
    function mail_mime_to_array($mid,$parse_headers=true) {
        $mail = imap_fetchstructure($this->connection,$mid);
        $mail = $this->mail_get_parts($mid,$mail,0);
        if ($parse_headers) $mail["parsed"]=$this->mail_parse_headers($mail[0]["data"]);
        return($mail);
    }
    function mail_get_parts($mid,$part,$prefix) {   
        $attachments=array();
        $attachments[$prefix]=$this->mail_decode_part($mid,$part,$prefix);
        if (isset($part->parts)) { // multipart
            $prefix = ($prefix == "0")?"":"$prefix.";
            foreach ($part->parts as $number=>$subpart)
                $attachments=array_merge($attachments, $this->mail_get_parts($mid,$subpart,$prefix.($number+1)));
        }
        return $attachments;
    }
    function mail_decode_part($message_number,$part,$prefix) {
        $attachment = array();
        if($part->ifdparameters) {
            foreach($part->dparameters as $object) {
                $attachment[strtolower($object->attribute)]=$object->value;
                if(strtolower($object->attribute) == 'filename') {
                    $attachment['is_attachment'] = true;
                    $attachment['filename'] = $object->value;
                }
            }
        }
        if($part->ifparameters) {
            foreach($part->parameters as $object) {
                $attachment[strtolower($object->attribute)]=$object->value;
                if(strtolower($object->attribute) == 'name') {
                    $attachment['is_attachment'] = true;
                    $attachment['name'] = $object->value;
                }
            }
        }
        $attachment['data'] = imap_fetchbody($this->connection, $message_number, $prefix);
        if($part->encoding == 3) { // 3 = BASE64
            $attachment['data'] = base64_decode($attachment['data']);
        } else if($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
            $attachment['data'] = quoted_printable_decode($attachment['data']);
        }
        return($attachment);
    }
    function get_mime_type(&$structure) {
        $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
        if ($structure->subtype) {
            return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
        }
        return "TEXT/PLAIN";
    }
    function get_part($msg_number, $mime_type, $structure = false,$part_number = false) {
        if (!$structure) {
            $structure = imap_fetchstructure($this->connection, $msg_number);
        }
        if ($structure) {
            if (!$part_number) {
                $part_number = "1";
            }
            if ($mime_type == $this->get_mime_type($structure)) {
                $text = imap_fetchbody($this->connection, $msg_number, $part_number);
                if ($structure->encoding == 3) {
                    return imap_base64($text);
                } else if ($structure->encoding == 4) {
                    return imap_qprint($text);
                } else {
                    return $text;
                }
            }
            if ($structure->type == 1) /* multipart */ {
                while(list($index, $sub_structure) = each($structure->parts)) {
                    if ($part_number) {
                        $prefix = $part_number . '.';
                        $data = $this->get_part($msg_number, $mime_type, $sub_structure,$prefix . ($index + 1));
                        if ($data) {
                            return $data;
                        }
                    }
                }
            }
        }
        return false;
    }
    function transformHTML($str) {
        if ((strpos($str,"<HTML") < 0) || (strpos($str,"<html") < 0)) {
            $makeHeader = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
            if ((strpos($str,"<BODY") < 0) || (strpos($str,"<body") < 0)) {
                $makeBody = "\n\n";
                $str = $makeHeader . $makeBody . $str ."\n";
            } else {
                $str = $makeHeader . $str ."\n";
            }
        } else {
            $str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n". $str;
        }
        return $str;
    }
    
    function create_attachments($msgno) {
        $struct = imap_fetchstructure($this->connection, $msgno);
        $attachments = array();
        $ind = 2;
        $str = array();
        $this->attachment_exists($struct, $msgno, $attachments, $str, $ind);
        return $attachments;
    }
    
    function attachment_exists($part, $msgno, &$attachments, $str, $ind) {
        if (isset($part->parts)) {
            $str[] = $ind;
            foreach ($part->parts as $key=>$partofPart) {
                $this->attachment_exists($partofPart, $msgno, $attachments, $str, $key+1);
            }
        } else {
            if (isset($part->disposition)) {
                if ($part->disposition == 'attachment') {
                    $attachments[] = array('title'=>$part->dparameters[0]->value, 'file'=>$this->downloadFile($part->dparameters[0]->value, imap_fetchbody($this->connection,$msgno,implode('.', $str))));
                }
            }
        }
    }
    
    function downloadFile($strFileName,$fileContent) {
        $strFileType = strrev(substr(strrev($strFileName),0,4));
        $ContentType = "application/octet-stream";
        if ($strFileType == ".asf") 
            $ContentType = "video/x-ms-asf";
        if ($strFileType == ".avi")
            $ContentType = "video/avi";
        if ($strFileType == ".doc")
            $ContentType = "application/msword";
        if ($strFileType == ".zip")
            $ContentType = "application/zip";
        if ($strFileType == ".xls")
            $ContentType = "application/vnd.ms-excel";
        if ($strFileType == ".gif")
            $ContentType = "image/gif";
        if ($strFileType == ".jpg" || $strFileType == "jpeg")
            $ContentType = "image/jpeg";
        if ($strFileType == ".wav")
            $ContentType = "audio/wav";
        if ($strFileType == ".mp3")
            $ContentType = "audio/mpeg3";
        if ($strFileType == ".mpg" || $strFileType == "mpeg")
            $ContentType = "video/mpeg";
        if ($strFileType == ".rtf")
            $ContentType = "application/rtf";
        if ($strFileType == ".htm" || $strFileType == "html")
            $ContentType = "text/html";
        if ($strFileType == ".xml") 
            $ContentType = "text/xml";
        if ($strFileType == ".xsl") 
            $ContentType = "text/xsl";
        if ($strFileType == ".css") 
            $ContentType = "text/css";
        if ($strFileType == ".php") 
            $ContentType = "text/php";
        if ($strFileType == ".asp") 
            $ContentType = "text/asp";
        if ($strFileType == ".pdf")
            $ContentType = "application/pdf";
        if (substr($ContentType,0,4) == "text") {
            $fileContent = imap_qprint($fileContent);
        } else {
            $fileContent = imap_base64($fileContent);
        }
        return array ('content'=>$fileContent, 'type'=>$ContentType);
    }
    function check_email_address($email) {
        if (!preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9\._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9\._-] +)+$/" , $email)) {
            return false;
        }
        return true;
    }
    function __destruct() {
        // expunge any changes \\
        if ($this->connection !== false) {
            imap_expunge($this->connection);
        }
    }
}
?>