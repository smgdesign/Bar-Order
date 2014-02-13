<?php

/**
 * Dettol / Lysol - 2013
 */
class ErrorsController extends Controller {
    public function not_found() {
        header("HTTP/1.0 404 Not Found");
        $this->set('title', 'Page Not Found');
    }
}
