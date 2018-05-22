<?php

require_once "/../logging.php";

class DB_Exception extends Exception {
    
    public function __construct($logs = NULL, $message = false, $code = false) {
        if (!$message) {
            $this->message = mysql_error();
        } 
        if (!$code) {
            $this->code = mysql_errno();
        }
        if ($logs != NULL) {
            $logs->log(Logging::ERROR, $this->__toString());
        }
    }
    
}

?>
