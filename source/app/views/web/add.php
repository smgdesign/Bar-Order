<?php
if (isset($mode)) {
    if (file_exists(__DIR__.'/add/'.$mode.'.php')) {
        include (__DIR__.'/add/'.$mode.'.php');
    }
}
?>