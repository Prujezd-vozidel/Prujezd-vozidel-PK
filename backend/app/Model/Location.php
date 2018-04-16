<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 16.4.2018
 * Time: 13:22
 */

namespace App\Model;


class Location
{
    public $id;
    public $name;
    public $town;
    public $street;
    public $device;
    public $area;

    public function __construct() {
    }

    /**
     * Vytvori novou instance lokace a naplni ji daty. Id je nastaveno na 0.
     *
     * @param $data Pole cislovane od nuly obsahujici data, kterymi bude naplnena nova instance.
     *              Format: [0] = name, [1] = town, [2] = street, [3] = device, [4] = area
     * @return Location
     */
    public static function withData($data) {
        $instance = new self();
        $instance ->id = 0;
        $instance ->name = $data[0];
        $instance ->town = $data[1];
        $instance ->street = $data[2];
        $instance ->device = $data[3];
        $instance ->area = $data[4];

        return $instance;
    }

    public function toString() {
        return "<td>".$this->name."</td>"."<td>".$this->town."</td>"."<td>".$this->street."</td>".
            "<td>".$this->device."</td>"."<td>".$this->area."</td>";
    }
}