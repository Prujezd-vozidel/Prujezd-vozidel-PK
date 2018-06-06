<?php

require_once "traffic.php";
require_once "location.php";
require_once "logging.php";

class Parser {
    
    // Pro data o doprave - na kolik intervalu se ma rozdelit den (96 = 4 * 24 => intervaly po ctvrt hodine).
    public $HOW_MANY_INTERVALS = 96;
    
    // Pro data o doprave - jak dlouho v milisekundach trva jeden interval.
    public $intervalMilli;
    
    // Pole informaci o lokacich.
    private $locations;
    
    // Pole informaci o doprave - peti-rozmerne pole s nasledujici strukturou:
    // Zaklad tvori pole zarizeni. Pro kazde zarizeni je pak pole pro casove intervaly.
    // Kazdy casovy interval ma pole o dvou prvcich (dva smery). Pro kazdy smer je vytvoreno pole o jedenacti
    // prvcich (jedenact typu vozidel). Pro kazde vozidlo je vytvoreno pole o dvou prvcich (pocet vozidel, suma rychlosti).
    private $traffic;
    
    // Skoro to same jako $traffic, akorat bez rozmeru pro casove intervaly.
    private $trafficOneDay;
    
    // Objekt pro logovani do souboru cron.txt ve slozce log.
    private $logs;
    
    public function __construct($logs) {
        // Pro lokace se data predzpracovavat nemusi.
        $this->locations = array();
        
        // Naopak u zaznamu je prilis zbytecnych informaci - k predzpracovani dojit musi.
        $this->intervalMilli = (int) (24 * 3600000 / $this->HOW_MANY_INTERVALS);
        $this->traffic = array();
        $this->trafficOneDay = array();
        
        $this->logs = $logs;
    }
    
    public function doWork($date) {
        // Stazeni archivu pri stestovani neresime.
        // Pouze zapis do logu a zavolani zpracovani obou souboru.
        $this->logs->log(Logging::INFO, "ZACATEK PROCEDURY pro den ".DateTime::createFromFormat("Ymd", $date)->format("d.m.Y").".");
        $this->logs->log(Logging::INFO, "Zpracovavani zaznamu o doprave.");
        $this->parse("data/test_".$date."_doprava.csv", TRUE);
        $this->logs->log(Logging::INFO, "Zpracovavani zaznamu o lokacich.");
        $this->parse("data/test_".$date."_lokace.csv", FALSE);
        $this->logs->log(Logging::INFO, "KONEC PROCEDURY.");
    }
    
    private function parse($fileName, $traffic) {
        if (($file = fopen($fileName, "r"))) {
            while (($row = fgetcsv($file, 1000, "|"))) {
                if ($traffic) {
                    $this->saveVehicleInfo(new Traffic($row));
                } else {
                    $this->locations[] = new Location($row);
                }
            }
            fclose($file);
        }
    }
    
    private function saveVehicleInfo($t) {
        // Kontrola, jestli je pro dane zarizeni vytvorene pole casu.
        if (!isSet($this->traffic[$t->device])) {
            // Vytvorit prvni dva rozmery pole pro dopravni data s rozdelenim na casove intervaly.
            $this->traffic[$t->device] = array();
            for ($i = 0; $i < $this->HOW_MANY_INTERVALS; $i++) {
                $this->traffic[$t->device][$i] = NULL;
            }
            
            // U pole s prumery za cely den rovnou vytvorit vsechny rozmery.
            $this->trafficOneDay[$t->device] = array();
            for ($i = 0; $i < 2; $i++) {
                $this->trafficOneDay[$t->device][$i] = array();
                for ($j = 0; $j < 11; $j++) {
                    $this->trafficOneDay[$t->device][$i][$j] = array(0, 0, 0); // Pocet danych vozidel, suma jejich rychlosti a pocet vozidel u kterych nesla stanovit rychlost.
                }
            }
            
        }
        
        // Zjisteni, do jakeho casoveho intervalu patri zaznam.
        list($date, $time) = explode(" ", $t->dateTime->format("Y-m-d H:i:s.u"), 2);
        list($hours, $minutes, $seconds) = explode(":", $time, 3);
        $interval = (int) (($hours * 3600000 + $minutes * 60000 + $seconds * 1000) / $this->intervalMilli);
        
        // Kontrola, jestli je pro dany casovy interval vytvorene pole pro smery.
        if ($this->traffic[$t->device][$interval] == NULL) {
            $this->traffic[$t->device][$interval] = array();
            for ($i = 0; $i < 2; $i++) {
                $this->traffic[$t->device][$interval][$i] = array();
                for ($j = 0; $j < 11; $j++) {
                    $this->traffic[$t->device][$interval][$i][$j] = array(0, 0, 0); // Pocet danych vozidel, suma jejich rychlosti a pocet vozidel u kterych nesla stanovit rychlost.
                }
            }
        }
        
        // Ulozeni dulezitych informaci o danem zaznamu do pole s casovymi intervaly a i do pole se zaznamy za cely den.
        if ($t->speed < 1) {
            $this->traffic[$t->device][$interval][$t->direction][$t->type10][2]++;
            $this->trafficOneDay[$t->device][$t->direction][$t->type10][2]++;
        } else {
            $this->traffic[$t->device][$interval][$t->direction][$t->type10][0]++;
            $this->traffic[$t->device][$interval][$t->direction][$t->type10][1] += $t->speed;
            $this->trafficOneDay[$t->device][$t->direction][$t->type10][0]++;
            $this->trafficOneDay[$t->device][$t->direction][$t->type10][1] += $t->speed;
        }
    }
    
    public function getTraffic() {
        return $this->traffic;
    }
    
    public function getTrafficOneDay() {
        return $this->trafficOneDay;
    }
    
    public function getLocations() {
        return $this->locations;
    }
    
}

?>