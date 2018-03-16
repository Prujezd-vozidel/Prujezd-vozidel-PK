<?php

    class Session {
    
        public function __construct() {
            ob_start();
            session_start();
        }
        
        public function create($nazev, $vloz) {
            $_SESSION[$nazev] = $vloz;
        }
        
        public function delete($nazev) {
            unSet($_SESSION[$nazev]);
        }
        
    }

?>