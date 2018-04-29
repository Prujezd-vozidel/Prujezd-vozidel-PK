<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 20.4.2018
 * Time: 20:25
 */

namespace App\Http\Controllers;


use App\Model\Vehicle;
use App\Model\Vozidlo;

class VehicleController extends Controller
{
    /**
     * Vrati vsechny typy vozidel.
     */
    public function getAll() {
        $vehicles = Vozidlo::all();

        if ($vehicles == null || count($vehicles) == 0) {
            return response('Not found.', 404);
        }

        return $vehicles;
    }
}