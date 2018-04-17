<?php

require_once "db-pdo.php";

class DB_WEB extends DB_PDO {
  
    protected $user = "root";
    protected $pass = "";
    protected $dbhost = "localhost";
    protected $dbname = "prujezd_vozidel";
  
    public function __construct() {
    }
     
}

?>
