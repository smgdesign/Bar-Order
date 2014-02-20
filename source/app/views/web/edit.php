<?php
if (isset($mode)) {
    if (file_exists(__DIR__.'/edit/'.$mode.'.php')) {
        include (__DIR__.'/edit/'.$mode.'.php');
    }
}
?>