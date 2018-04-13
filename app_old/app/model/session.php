<?php

class Session {
    
    public function __construct() {
        ob_start();
        session_start();
    }
    
    public function create($name, $value) {
        $_SESSION[$name] = $value;
    }
    
    public function delete($name) {
        unSet($_SESSION[$name]);
    }
    
}

?>