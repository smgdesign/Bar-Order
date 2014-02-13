<?php

/*
 * FS2
 * crossLINQ Version 0.0.4.1
 */

/**
 * Description of cron
 *
 * @author Richard Wilson <richard.wilson@smgdesign.org>
 */
class pop {
    public function connect() {
        global $mailConf;
        /* Set connection options */
        $pop3 = new pop3_class;
        $pop3->hostname = $mailConf['host'];
        $pop3->port = 110;
        $user = $mailConf['username'];
        $password = $mailConf['password'];
        $apop = 0;

        /* Connect to the server */
        if (($error = $pop3->Open())=="") {

            /* Authenticate */
            if (($error = $pop3->Login($user, $password, $apop))=="") {

                /* Setup a file name of a message to be retrieved
                * on an already opened POP3 connection */
                $pop3->GetConnectionName($connection_name);
                $message=1;
                $message_file='pop3://'.$connection_name.'/'.$message;

                /* Do your message processing here */
                $message = file_get_contents($message_file);

                /* If all goes well, delete the processed message */
                $pop3->DeleteMessage($message);
            }

            /* Close the connection before you exit */
            $pop3->Close();
        }
    }
}
?>
