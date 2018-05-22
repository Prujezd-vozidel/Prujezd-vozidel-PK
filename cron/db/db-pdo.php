<?php

require_once "db-exception.php";

class DB_PDO {
  
    protected $user;
    protected $pass;
    protected $dbhost;
    protected $dbname;
    protected $dbh;
    
    protected $logs;
  
    public function __construct($user, $pass, $dbhost, $dbname) {
        $this->user = $user;
        $this->pass = $pass;
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
    }
  
    protected function connect() {
        $this->dbh = new PDO("mysql:host=".$this->dbhost.";dbname=".$this->dbname.";charset=utf8", $this->user, $this->pass);
        if (!$this->dbh) {
            throw new DB_Exception($this->logs);
        }
    }
  
    public function executeQuery($query) {
        if (!$this->dbh) {
            $this->connect();
        }
        $ret = $this->dbh->query($query);
        if (!$ret) {
            throw new DB_Exception($this->logs);
        } else {
            $stmt = new DB_PDOStatement($this->dbh, $query, $this->logs);
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
    
    private $logs;
  
    public function __construct($dbh, $query, $logs) {
        $this->query = $query;
        $this->dbh = $dbh;
        $this->logs = $logs;
        if (!$dbh) {
            throw new DB_Exception($this->logs, "Spojení s databází se nezdařilo!");
        }
    }
  
    public function fetchAssoc() {
        if (!$this->result) {
            throw new DB_Exception($this->logs, "Dotaz nebyl vykonán!");
        }
        return $this->result->fetch(PDO::FETCH_ASSOC);
    }
    
}

?>
