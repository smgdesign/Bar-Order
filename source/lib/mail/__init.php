<?php

/*
 * Copyright Invention Interactive Ltd. 2012
 * Project - TAP Biosystems - TAP Distributor Portal
 */

/**
 * This class is used to prepare and send all mail messages
 *
 * @author Richard
 */
class mail {
    public $php_mail;
    public $config;
    public $imap;
    public function __construct() {
        require("class.phpmailer.php");
        $this->php_mail = new PHPMailer();
    }
    public function sendMail($to, $subject, $mailFile, $data='', $from='', $name='', $username='', $password='', $host='localhost') {
        global $php_mail;
        $mailFile = 'templates/'.$mailFile;
        $output = $this->prepMail($mailFile, $data);
        
        
        //Your SMTP servers details

        $this->php_mail->IsSMTP();               // set mailer to use SMTP
        $this->php_mail->Host = $host;  // specify main and backup server or localhost
        $this->php_mail->SMTPAuth = true;     // turn on SMTP authentication
        $this->php_mail->Username = $username;  // SMTP username
        $this->php_mail->Password = $password; // SMTP password
        //It should be same as that of the SMTP user

        $this->php_mail->From = $from;	//Default From email same as smtp user
        $this->php_mail->FromName = $name;

        if (is_array($to)) {
            foreach ($to as $email) {
                $this->php_mail->AddAddress($email);
            }
        } else {
            $this->php_mail->AddAddress($to); //Email address where you wish to receive/collect those emails.
        }

        $this->php_mail->WordWrap = 50;                                 // set word wrap to 50 characters
        $this->php_mail->IsHTML(true);                                  // set email format to HTML

        $this->php_mail->Subject = $subject;
        $this->php_mail->Body = $output;

        if(!$this->php_mail->Send()) {
           print_r("Message could not be sent. <p>");
           print_r("Mailer Error: " . $this->php_mail->ErrorInfo);
        }
        $this->php_mail->ClearAllRecipients();
    }
    public function prepMail($file, $data) {
        $tmpOutput = file_get_contents(ROOT.'/lib/mail/'.$file);
        
        foreach ($data as $key=>$val) {
            $tmpOutput = preg_replace('/{_'.$key.'_}/', $val, $tmpOutput);
        }
        return $tmpOutput;
    }
    public function get_mailConfig() {
        return $this->config;
    }
    public function set_mailConfig($config=array()) {
        foreach ($config as $item=>$val) {
            $this->config[$item] = $val;
        }
    }
    public function prepMailer() {
        $this->php_mail->IsSMTP();               // set mailer to use SMTP
        $this->php_mail->Host = $this->config['host'];  // specify main and backup server or localhost
        $this->php_mail->SMTPAuth = true;     // turn on SMTP authentication
        $this->php_mail->Username = $this->config['username'];  // SMTP username
        $this->php_mail->Password = $this->config['password']; // SMTP password
        $this->php_mail->From = $this->config['from'];
        $this->php_mail->FromName = $this->config['fromname'];
    }
    public function loadIMAP() {
        global $mailConf;
        require_once "class.imap.php";
        $this->imap = new imap($mailConf['host'],143,$mailConf['username'],$mailConf['password']);
        if ($this->imap->connection !== false) {
            return true;
        }
        return false;
    }
    public function loadMessages() {
        global $common;
        if ($this->imap->connection !== false) {
            $messages = $this->imap->mail_list();
            $items = array();
            if (count($messages) > 0) {
                foreach($messages as $msg) {
                    $info = $this->imap->mail_mime_to_array($msg['msgno'])['parsed'];
                    $msgText = $this->imap->get_part($msg['msgno'], "TEXT/PLAIN");
                    $msgHTML = $this->imap->get_part($msg['msgno'], "TEXT/HTML");
                    $msgAlt = $this->imap->get_part($msg['msgno'], "MULTIPART/ALTERNATIVE");
                    $msgContent = ((!empty($msgHTML)) ? $this->imap->transformHTML($msgHTML) : ((!empty($msgAlt)) ? $msgAlt : ((!empty($msgText)) ? $msgText : '')));
                    $subject = $info['Subject'];
                    $from = $info['From'];
                    $date = $info['Date'];
                    $subjectArr = $this->parseTgt($subject);
                    $items[$msg['msgno']] = array('subject'=>$subjectArr, 'from'=>$common->escape_data($from), 'date'=>$date, 'content'=>$common->escape_data($msgContent), 'attachments'=>$this->imap->create_attachments($msg['msgno']));
                }
            }
            return $items;
        }
    }
    public function parseTgt($subject='') {
        if ($subject != '') {
            $subjectMatch = array();
            preg_match('/XLN(REF|COM):([0-9]{6,14}):?([0-9]{0,10})?\]? ?(.+)/i', $subject, $subjectMatch);
            if (count($subjectMatch) > 1) {
                $subjectMatch = array_slice($subjectMatch, 1);
            }
            return $subjectMatch;
        }
        return array($subject);
    }
    public function moveMessage($msgno=0, $tgt='INBOX') {
        if ($this->imap->connection !== false) {
            $this->imap->mail_move($msgno, (($tgt !== 'INBOX') ? 'INBOX.' : '').$tgt);
        }
    }
}

?>
