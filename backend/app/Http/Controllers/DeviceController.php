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
    // nazvy moznych url parametru
    const ADDRESS_PARAM = 'address';
    const SHOW_DIRECTION_PARAM = 'showDirection';
    const DATE_FROM_PARAM = 'dateFrom';
    const DATE_TO_PARAM = 'dateTo';
    const TIME_FROM_PARAM = 'timeFrom';
    const TIME_FROM_TO = 'timeTo';
    const DIRECTION_PARAM = 'direction';


    public function getDevice(Request $request) {
        $address = null;
        $showDirection=0;
        if ($request->has(ADDRESS_PARAM)) {
            $address = $request->input(ADDRESS_PARAM);
        }

        if ($request->has(SHOW_DIRECTION_PARAM) && $request->input(SHOW_DIRECTION_PARAM) == '1') {
            $showDirection = 1;
        }

        $device = Zarizeni::findByAddressJoinAddress($address, $showDirection);
        if ($device == null || count($device) == 0) {
            return response('Not found.', 404);
        }

        return $device;
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
        if ($request->has(DATE_FROM_PARAM)) {
            $dateFrom = $request->input(DATE_FROM_PARAM);
        }
        if ($request->has(DATE_TO_PARAM)) {
            $dateTo = $request->input(DATE_TO_PARAM);
        }
        if ($request->has(TIME_FROM_PARAM)) {
            $timeFrom = $request->input(TIME_FROM_PARAM);
        }
        if ($request->has(TIME_TO_PARAM)) {
            $timeTo = $request->input(TIME_TO_PARAM);
        }
        if ($request->has(DIRECTION_PARAM)) {
            $direction = $request->input(DIRECTION_PARAM);
        }

        $device = Zarizeni::findByIdJoinAddress($id);
        if ($device != null) {
            $device[0]->traffic = Zaznam::findByDevice($id, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction);
        } else if ($device == null || count($device) == 0) {
            return response('Not found.', 404);
        }

        return $device;
    }

    public function getAll() {
        return Zarizeni::getAllJoinAddress();
    }

    public function lastDay() {
        return Zaznam::lastInsertedDate();
    }

    public function headerTest(Request $request) {
        $authHeader = $request->header("jwt");

        if($authHeader != null) {
            return $authHeader;
        } else {
            return $request->header("jwt");
        }
    }
}
