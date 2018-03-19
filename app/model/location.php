<?php

class Location {

    public $name;
    public $town;
    public $street;
    public $device;
    public $area;
    
    public function __construct($data) {
        $this->name = $data[0];
        $this->town = $data[1];
        $this->street = $data[2];
        $this->device = $data[3];
        $this->area = $data[4];
    }
    
    public function toString() {
        return "<td>".$this->name."</td>"."<td>".$this->town."</td>"."<td>".$this->street."</td>".
               "<td>".$this->device."</td>"."<td>".$this->area."</td>";
    }
}

?>
