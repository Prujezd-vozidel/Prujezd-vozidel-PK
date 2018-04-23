<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 20.4.2018
 * Time: 20:09
 */

namespace App\Http\Controllers;

use App\Model\Device;
use App\Model\Zarizeni;
use App\Model\Zaznam;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function getDevice(Request $request) {
        $town = null;
        $street = null;
        $showDirection=0;
        if ($request->has('address')) {
            $address = $request->input('address');
            // todo: what is a format of address ?
            $addressParts = explode(";", $address);
            if (count($addressParts) == 2) {
                $town = $addressParts[0];
                $street = $addressParts[1];
            }
        }

        if ($request->has('showDirection')) {
            $showDirection = ($request->input('showDirection') === 1);
        }

        return Zarizeni::findByAddressJoinAddress($street, $town);
    }

    /**
     * Vrati zarizeni podle id.
     * Url parametry:
     * dateFrom
     * dateTo
     * timeFrom
     * timeTo
     * direction
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDeviceById(Request $request, $id) {

        $dateFrom = null;
        $dateTo = null;
        $timeFrom = null;
        $timeTo = null;
        $direction = null;

        // nacti parametry
        if ($request->has('dateFrom')) {
            $dateFrom = $request->input('dateFrom');
        }
        if ($request->has('dateTo')) {
            $dateTo = $request->input('dateTo');
        }
        if ($request->has('timeFrom')) {
            $timeFrom = $request->input('timeFrom');
        }
        if ($request->has('timeTo')) {
            $timeTo = $request->input('timeTo');
        }
        if ($request->has('direction')) {
            $direction = $request->input('direction');
        }

        $device = Zarizeni::findByIdJoinAddress($id);
        if ($device != null) {
            $device[0]->traffic = Zaznam::findByDevice($id, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction);
        }

        return $device;
    }

    public function getAll() {
        return Zarizeni::getAllJoinAddress();
    }

    public function lastDay() {
        return Zaznam::lastInsertedDate();
    }
}
