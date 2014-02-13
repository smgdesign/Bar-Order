<?php

/*
 * FS2
 * crossLINQ Version 0.0.1
 */

/**
 * Description of bcrypt
 *
 * @author Richard Wilson <richard.wilson@smgdesign.org>
 */
class Bcrypt {
    public function generateSalt($salter) {
        $salt = '$2a$13$';
        $salt .= md5(strtolower($salter));
        return $salt;
    }
    public function generateHash($salt, $pass) {
        $hash = crypt($pass, $salt);
        $hash = substr($hash, 29);
        return $hash;
    }
    public function rij_encrypt($encrypt, &$mc_key) {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $mc_key, trim($encrypt), MCRYPT_MODE_ECB, $iv));
        $encode = base64_encode($passcrypt);
        return $encode;
    }
    public function rij_decrypt($decrypt, $mc_key) {
        $decoded = base64_decode($decrypt);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $mc_key, trim($decoded), MCRYPT_MODE_ECB, $iv));
        return $decrypted;
    }
}

?>
