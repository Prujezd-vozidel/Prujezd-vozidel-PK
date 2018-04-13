<?php

require_once "../model/db/db-web.php";

class DAO {
  
    protected $dbh;
  
    public function setDB($dbh) {
        $this->dbh = $dbh;
    }

    public function delete($id) {
        $query = "DELETE FROM pom WHERE id='$id'";
        $stmt = $this->dbh->executeQuery($query);
    }
    
}

?>
