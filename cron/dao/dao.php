<?php

require_once "db/db-web.php";
require_once "location.php";

class DAO {
  
    protected $dbh;
  
    public function setDB($dbh) {
        $this->dbh = $dbh;
    }
    
    public function insertVehicles() {
        $query = "SELECT COUNT(*) AS total FROM vozidla";
        $stmt = $this->dbh->executeQuery($query);
        
        if ($stmt->fetchAssoc()["total"] < 1) {
            // Pokud v tabulce s vozidlama neni zadny zaznam, pridat vsechny moznosti.
            $query = "INSERT INTO vozidla (id, nazev) VALUES ";
            $query .= "('0', 'Neznámé vozidlo'), ";
            $query .= "('1', 'Motocykl'), ";
            $query .= "('2', 'Auto'), ";
            $query .= "('3', 'Auto s přívěsem'), ";
            $query .= "('4', 'Dodávka'), ";
            $query .= "('5', 'Dodávka s přívěsem'), ";
            $query .= "('6', 'Lehký nákladní automobil'), ";
            $query .= "('7', 'Lehký nákladní automobil s přívěsem'), ";
            $query .= "('8', 'Nákladní automobil'), ";
            $query .= "('9', 'Nákladní automobil s přívěsem'), ";
            $query .= "('10', 'Autobus');";
            $stmt = $this->dbh->executeQuery($query);
        }
    }
    
    public function controlTrafficData($dateStr) {
        $dateTo = new DateTime($dateStr);
        $dateTo->modify("+1 day");
        $query = "SELECT COUNT(*) AS total FROM zaznam_cas WHERE datetime_od >= '$dateStr' AND datetime_od < '".$dateTo->format('Y-m-d')."';";
        $stmt = $this->dbh->executeQuery($query);
        return $stmt->fetchAssoc()["total"] < 1;
    }
    
    public function insertTrafficData($insertRTT, $insertRT) {
        for ($i = 0; $i < 2; $i++) {
            $query = "";
            $values = "";
            $counter = 0;
            $array = NULL;
            
            if ($i == 0) {
                $query = "INSERT INTO zaznam_cas VALUES ";
                $array = &$insertRTT;
            } else {
                $query = "INSERT INTO zaznam VALUES ";
                $array = &$insertRT;
            }
            
            for ($j = 0; $j < count($array); $j++) {
                $values .= $array[$j].", ";
                $counter++;
                if ($counter == 500 || $j == (count($array) - 1)) {
                    $query_ = $query.substr($values, 0, strlen($values) - 2).";";
                    $stmt = $this->dbh->executeQuery($query_);
                    
                    $values = "";
                    $counter = 0;
                }
            }
        }
    }
    
    public function insertLocationData($location) {
        
        // --- Kontrola, zda je mesto v tabulce, pripadne jeho pridani. ---
        
        $townId = $this->conditionalInsertion($location, "mesto", -1);
        
        // --- Kontrola, zda je ulice v tabulce, pripadne jeji pridani. ---
        
        $streetId = $this->conditionalInsertion($location, "ulice", $townId);
        
        // --- Kontrola, zda je zarizeni v tabulce, pripadne jeho pridani. ---
        
        $query = "SELECT * FROM zarizeni WHERE id='".$location->device."';";
        $stmt = $this->dbh->executeQuery($query);
        
        if (!($stmt->fetchAssoc())) {
            // Zarizeni se v tabulce jeste nenachazi.
            $query = "INSERT INTO zarizeni VALUES ('".$location->device."', '".$location->name."', '0', '$streetId');";
            $stmt = $this->dbh->executeQuery($query);
        }
        
    }
    
    // Pro mesta a ulice.
    private function conditionalInsertion($location, $table, $townId) {
        $query = "SELECT id FROM $table WHERE nazev=";
        if ($townId < 0) {
            // Hledame mesto.
            $query .= "'".$location->town."';";
        } else {
            // Hledame ulici (s odkazem na konkretni mesto - ruzna mesta mohou mit shodny nazev nejake ulice).
            $query .= "'".$location->street."' AND mesto_id='$townId';";
        }
        $stmt = $this->dbh->executeQuery($query);
        
        $id = -1;
        if ($row = $stmt->fetchAssoc()) {
            // Zaznam uz je v tabulce.
            $id = $row["id"];
        } else {
            // Zaznam se v tabulce jeste nenachazi - nalezeni id pro novy zaznam.
            $id = $this->findFirstId($table);
            
            $query = "INSERT INTO $table VALUES ";
            if ($townId < 0) {
                $query .= "('$id', '".$location->town."');";
            } else {
                // Jestlize ulice v DB jeste neexistuje, nastavit geolokaci (nema smysl zem. sirku a delku zjistovat driv - nejedna se o dulezite informace pro CRON).
                $location->setGeolocation();
                $query .= "('$id', '".$location->street."', '$townId', '".$location->lat."', '".$location->lng."');";
            }
            
            // Vlozeni zaznamu do tabulky.
            $stmt = $this->dbh->executeQuery($query);
        }
        
        return $id;
    }
    
    public function findFirstId($table) {
        $query = "SELECT id FROM $table ORDER BY id DESC LIMIT 1;";
        $stmt = $this->dbh->executeQuery($query);
        
        $id = -1;
        if ($row = $stmt->fetchAssoc()) {
            // V tabulce je vice zaznamu.
            $id = $row["id"] + 1;
        } else {
            // Tabulka je prazdna.
            $id = 1;
        }
        
        return $id;
    }
    
}

?>
