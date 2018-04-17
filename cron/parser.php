<?php

require_once "traffic.php";
require_once "location.php";

class Parser {
    
    // Pro data o doprave - na kolik intervalu se ma rozdelit den (96 = 4 * 24 => intervaly po ctvrt hodine).
    private $HOW_MANY_INTERVALS = 96;
    
    // Pro data o doprave - jak dlouho v milisekundach trva jeden interval.
    private $intervalMilli;
    
    private $name;
    private $path;
    
    // Pole informaci o lokacich.
    private $locations;
    
    // Pole informaci o doprave - peti-rozmerne pole s nasledujici strukturou:
    // Zaklad tvori pole zarizeni. Pro kazde zarizeni je pak pole pro casove intervaly.
    // Kazdy casovy interval ma pole o dvou prvcich (dva smery). Pro kazdy smer je vytvoreno pole o jedenacti
    // prvcich (jedenact typu vozidel). Pro kazde vozidlo je vytvoreno pole o dvou prvcich (pocet vozidel, suma rychlosti).
    private $traffic;
    
    public function __construct() {
        $this->name = "DOPR_D_";
        $this->path = "http://doprava.plzensky-kraj.cz/opendata/doprava/den/".$this->name;
        
        // Pro lokace se data predzpracovavat nemusi.
        $this->locations = array();
        
        // Naopak u zaznamu je prilis zbytecnych informaci - k predzpracovani dojit musi.
        $this->intervalMilli = (int) (24 * 3600000 / $this->HOW_MANY_INTERVALS);
        $this->traffic = array();
    }
    
    public function doWork($date) {
        $zipUrl = $this->path.$date.".zip";
        $dir = "download/$date/";
        $downloaded = $dir."downloaded.zip";
        
        $result = $this->download($date, $zipUrl, $dir, $downloaded);
        if ($result == -1 || $result == 0 || $result == 1) {
            
            $ok = -1;
            if ($result == 0 && $this->extract($dir, $downloaded) == 0) {
                unlink($downloaded);
                $ok = 0;
            }
            
            if ($ok == 0 || $result == 1) {
                $this->parse($dir.$this->name.$date.".csv", TRUE);
                $this->parse($dir."Locations.csv", FALSE);
                // return; odkomentovat v pripade, ze extrahovana data nemaji byt odstranena.
            }
            
            $this->deleteDir($dir);
            
        }
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
            $this->traffic[$t->device] = array();
            for ($i = 0; $i < $this->HOW_MANY_INTERVALS; $i++) {
                $this->traffic[$t->device][$i] = NULL;
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
                    $this->traffic[$t->device][$interval][$i][$j] = array(0, 0); // Pocet danych vozidel, suma jejich rychlosti.
                }
            }
        }
        
        // Ulozeni dulezitych informaci o danem zaznamu.
        $this->traffic[$t->device][$interval][$t->direction][$t->type10][0]++;
        $this->traffic[$t->device][$interval][$t->direction][$t->type10][1] += $t->speed;
    }
    
    private function download($date, $zipUrl, $dir, $downloaded) {
        if (strpos(get_headers($zipUrl, 1)[0], "404") === FALSE) {
            if (!file_exists($dir)) {
                if (mkdir($dir)) {
                    if (copy($zipUrl, $downloaded)) {
                        // Stazeni probehlo v poradku.
                        return 0;
                    } else {
                        // Nepovedlo se stazeni zip souboru.
                        return -1;
                    }
                } else {
                    // Nepodarilo se vytvorit slozku pro data.
                    return -2;
                }
            } else {
                // Data k vybranemu dni jiz byla stazena.
                return 1;
            }
        } else {
            // Pro dany datum neexistuji data.
            return -3;
        }
    }
    
    private function extract($dir, $downloaded) {
        $zip = new ZipArchive();
        if ($zip->open($downloaded, ZIPARCHIVE::CREATE) === TRUE) {
            $zip->extractTo($dir);
            $zip->close();
            // Extrahovani v poradku dokonceno.
            return 0;
        } else {
            // Nepovedlo se extrahovani obsahu zipu.
            return -1;
        }
    }
    
    private function deleteDir($path) {
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $path_ = $path."/".$file;
                    if (filetype($path_) == "dir") {
                        $this->deleteDir($path_);
                    } else {
                        unlink($path_);
                    }
                }
            }
            reset($files);
            rmdir($path);
        }
    }
    
    public function getTraffic() {
        return $this->traffic;
    }
    
    public function getLocations() {
        return $this->locations;
    }
    
}

?>