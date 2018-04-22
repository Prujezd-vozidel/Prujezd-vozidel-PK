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
     * Vrati likaci s danym id.
     *
     * @param $id
     * @return Lokace s id.
     */
    public function findById($id)
    {
        $location = Location::withData(array("Česká Kubice, směr od Německa", "Česká Kubice", "Česká Kubice", "KP055", "5"));
        $location->id = $id;
        return response()->json($location);
    }

    /**
     * Vrati instanci lokace jako json.
     */
    public function getLocation()
    {
        return response()->json(Location::withData(array("Česká Kubice, směr od Německa", "Česká Kubice", "Česká Kubice", "KP055", "5")));
    }

    /**
     * Vrati vsechna mesta.
     */
    public function getCities() {
        return Mesto::with('zarizeni')->get();
    }
}