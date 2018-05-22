<?php

class Traffic {
    
    // Pouzivane atributy.
    public $device;
    public $direction;
    public $dateTime;
    public $speed;
    public $type10;
    
    // TODO pouziti.
    public $state;
    
    // Nepouzivane.
    // public $intensity;
    // public $intensityN;
    // public $occupancy;
    // public $type;
    // public $duration;
    // public $history;
    
    public function __construct($data) {
        $this->device = substr($data[0], 2, 3);
        $this->direction = substr($data[0], 7) - 1; // Misto hodnot 1, 2 - hodnoty 0, 1 (kvuli poli).
        $this->dateTime = new DateTime($data[1]);
        // $this->intensity = $data[2];
        // $this->intensityN = $data[3];
        // $this->occupancy = $data[4];
        $this->speed = (double) $data[5];
        $this->state = (int) $data[6];
        // $this->type = $data[7];
        // $this->duration = $data[8];
        // $this->history = $data[9];
        $this->type10 = (int) $data[10];
    }
    
}

?>
