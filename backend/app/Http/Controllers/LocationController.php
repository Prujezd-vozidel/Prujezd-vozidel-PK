<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 16.4.2018
 * Time: 13:19
 */

namespace App\Http\Controllers;


use App\Model\Location;
use App\Model\Mesto;

class LocationController extends Controller
{
    /**
     * Vrati vsechna mesta.
     */
    public function getCities() {
        $towns = Mesto::with('zarizeni')->get();

        if ($towns == null || count($towns) == 0) {
            return response('Not fonud.', 404);
        }

        return $towns;
    }
}