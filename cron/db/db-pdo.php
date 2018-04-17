<?php

require_once "db-exception.php";

class DB_PDO {
  
    protected $user;
    protected $pass;
    protected $dbhost;
    protected $dbname;
    protected $dbh;
  
    public function __construct($user, $pass, $dbhost, $dbname) {
        $this->user = $user;
        $this->pass = $pass;
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
    }
  
    protected function connect() {
        $this->dbh = new PDO("mysql:host=".$this->dbhost.";dbname=".$this->dbname.";charset=utf8", $this->user, $this->pass);
        if (!$this->dbh) {
            throw new DB_Exception;
        }
    }
  
    public function executeQuery($query) {
        if (!$this->dbh) {
            $this->connect();
        }
        $ret = $this->dbh->query($query);
        if (!$ret) {
            throw new DB_Exception;
        } else {
            $stmt = new DB_PDOStatement($this->dbh, $query);
            $stmt->result = $ret;
            $stmt->number = $ret->rowCount();
            return $stmt;
        }
    }
      
}

class DB_PDOStatement {
    
    public $result;
    public $query;
    public $number;
    protected $dbh;
  
    public function __construct($dbh, $query) {
        $this->query = $query;
        $this->dbh = $dbh;
        if (!$dbh) {
            throw new DB_Exception("Spojení s databází se nezdařilo!");
        }
    }
  
    public function fetchAssoc() {
        if (!$this->result) {
            throw new DB_Exception("Dotaz nebyl vykonán!");
        }
        return $this->result->fetch(PDO::FETCH_ASSOC);
    }
    
}

?>
