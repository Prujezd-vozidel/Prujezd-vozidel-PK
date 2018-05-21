<?php

class Location {

    // Pouzivane atributy.
    public $name;
    public $town;
    public $street;
    public $device;
    
    // Nepouzivane.
    // public $area;
    
    // --- ZEMEPISNA SIRKA A DELKA A UDAJE POTREBNE K JEJICH ZJISTENI. ---
    
    private $key;
    private $region;
    
    public $lat;
    public $lng;
    
    public function __construct($data) {
        $this->name = $data[0];
        $this->town = $data[1];
        $this->street = $data[2];
        $this->device = substr($data[3], 2);
        // $this->area = $data[4];
        
        $this->key = "AIzaSyCSx7hyAzQiG5uocJTeZgf1Z3lpDy4kpEk";
        $this->region = "cz";
        $this->lat = -1;
        $this->lng = -1;
    }
    
    // V pripade problemu, se ziskanim souboru protokolem HTTPS, v php.ini odkomentovat "extension=php_openssl.dll".
    public function setGeolocation() {
        $address = $this->town;
        if ($this->town != $this->street) {
            $address .= " ".$this->street;
        }
        $address = str_replace(" ", "+", $address); // Nemusi byt, jen pro jistotu.
        
        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=".$this->region."&key=".$this->key);
        $json = json_decode($json, TRUE);
        if ($json["status"] == "OK") {
            $this->lat = $json["results"]["0"]["geometry"]["location"]["lat"];
            $this->lng = $json["results"]["0"]["geometry"]["location"]["lng"];
        } else {
            $this->lat = -1;
            $this->lng = -1;
        }
    }
    
}

?>
