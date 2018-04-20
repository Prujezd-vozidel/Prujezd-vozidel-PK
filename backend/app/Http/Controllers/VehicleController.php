<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 20.4.2018
 * Time: 20:25
 */

namespace App\Http\Controllers;


use App\Model\Vehicle;

class VehicleController extends Controller
{
    /**
     * Vrati vsechny typy vozidel.
     */
    public function getAll() {
        $vehicles = [
            0 => Vehicle::create(0, 'neznámé'),
            1 => Vehicle::create(1, 'motocykl'),
            2 => Vehicle::create(2, 'osobní auto'),
            3 => Vehicle::create(4, 'dodávka')
        ];

        return $vehicles;
    }
}