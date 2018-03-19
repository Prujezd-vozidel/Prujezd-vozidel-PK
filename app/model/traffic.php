<?php

class Traffic {

    public $detector;
    public $dateTime;
    public $intensity;
    public $intensityN;
    public $occupancy;
    public $speed;
    public $state;
    public $type;
    public $duration;
    public $history;
    public $type10;
    
    public function __construct($data) {
        $this->detector = $data[0];
        $this->dateTime = $data[1];
        $this->intensity = $data[2];
        $this->intensityN = $data[3];
        $this->occupancy = $data[4];
        $this->speed = $data[5];
        $this->state = $data[6];
        $this->type = $data[7];
        $this->duration = $data[8];
        $this->history = $data[9];
        $this->type10 = $data[10];
    }
    
    public function toString() {
        return "<td>".$this->detector."</td>"."<td>".$this->dateTime."</td>"."<td>".$this->intensity."</td>".
               "<td>".$this->intensityN."</td>"."<td>".$this->occupancy."</td>"."<td>".$this->speed."</td>".
               "<td>".$this->state."</td>"."<td>".$this->type."</td>"."<td>".$this->duration."</td>".
               "<td>".$this->history."</td>"."<td>".$this->type10."</td>";
    }
}

?>
