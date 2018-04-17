<?php

require_once "db/db-web.php";

class DAO {
  
    protected $dbh;
    
    public function setDB($dbh) {
        $this->dbh = $dbh;
    }
    
}

?>
