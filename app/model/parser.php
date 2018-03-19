<?php

require_once "traffic.php";
require_once "location.php";

class Parser {

    private $name;
    private $path;
    
    private $traffic;
    private $locations;
    
    public function __construct() {
        $this->name = "DOPR_D_";
        $this->path = "http://doprava.plzensky-kraj.cz/opendata/doprava/den/".$this->name;
        $this->traffic = NULL;
        $this->locations = NULL;
    }
    
    public function doWork($date) {
        $zipUrl = $this->path.$date.".zip";
        $dir = "../../download/$date/";
        $downloaded = $dir."downloaded.zip";
        
        $result = $this->download($date, $zipUrl, $dir, $downloaded);
        if ($result == -1 || $result == 0 || $result == 1) {
            
            $ok = -1;
            if ($result == 0 && $this->extract($dir, $downloaded) == 0) {
                unlink($downloaded);
                $ok = 0;
            }
            
            if ($ok == 0 || $result == 1) {
                $this->traffic = $this->parse($dir.$this->name.$date.".csv", TRUE);
                $this->locations = $this->parse($dir."Locations.csv", FALSE);
                return;
            }
            
            $this->deleteDir($dir);
            
        }
    }
    
    private function parse($fileName, $traffic) {
        $counter = 0; // TODO
        
        $array = array();
        if (($file = fopen($fileName, "r"))) {
            while (($row = fgetcsv($file, 1000, "|")) && $counter++ < 10) {
                if ($traffic) {
                    $array[] = new Traffic($row);
                } else {
                    $array[] = new Location($row);
                }
            }
            fclose($file);
        }
        return $array;
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