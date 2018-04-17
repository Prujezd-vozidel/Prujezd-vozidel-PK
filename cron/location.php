<?php

class Location {

    // Pouzivane atributy.
    public $name;
    public $town;
    public $street;
    public $device;
    
    // Nepouzivane.
    // public $area;
    
    public function __construct($data) {
        $this->name = $data[0];
        $this->town = $data[1];
        $this->street = $data[2];
        $this->device = substr($data[3], 2);
        // $this->area = $data[4];
    }
    
}

?>
