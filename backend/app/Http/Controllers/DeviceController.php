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
    const TIME_TO_PARAM = 'timeTo';
    const DIRECTION_PARAM = 'direction';


    public function getDevice(Request $request) {
        $address = null;
        $showDirection=0;
        if ($request->has(self::ADDRESS_PARAM)) {
            $address = $request->input(self::ADDRESS_PARAM);
        }

        if ($request->has(self::SHOW_DIRECTION_PARAM) && $request->input(self::SHOW_DIRECTION_PARAM) == '1') {
            $showDirection = 1;
        }

        $device = Zarizeni::findByAddressJoinAddress($address, $showDirection);

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

        // nacti parametry
        $params = $this->loadDateTimeDirectionConstraints($request);
        $dateFrom = $params[self::DATE_FROM_PARAM];
        $dateTo = $params[self::DATE_TO_PARAM];
        $timeFrom = $params[self::TIME_FROM_PARAM];
        $timeTo = $params[self::TIME_TO_PARAM];
        $direction = $params[self::DIRECTION_PARAM];

        $device = Zarizeni::findByIdJoinAddress($id);
        if ($device != null) {
            $device->traffic = Zaznam::findByDevice($id, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction);
            return json_encode($device);
        } else {
            return response('Not found.', 404);
        }
    }

    public function getTrafficAverageByDevice(Request $request, $id) {
        // nacti parametry
        $params = $this->loadDateTimeDirectionConstraints($request);
        $dateFrom = $params[self::DATE_FROM_PARAM];
        $dateTo = $params[self::DATE_TO_PARAM];
        $timeFrom = $params[self::TIME_FROM_PARAM];
        $timeTo = $params[self::TIME_TO_PARAM];
        $direction = $params[self::DIRECTION_PARAM];

        $device = Zarizeni::findByIdJoinAddress($id);
        if ($device != null) {
            $device[0]->traffic = Zaznam::averageByDevice($id, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction);
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

    /**
     * Funkce nacte z requestu url parametry dateFrom, dateTo, timeFrom, timeTo, direciton a vrati je jako pole.
     * Defaultni hodnoty jsou null.
     *
     * @param Request $request Request ze ktere budou nacitany parametry.
     */
    private function loadDateTimeDirectionConstraints(Request $request) {
        $params = array();
        $params[self::DATE_FROM_PARAM] = null;
        $params[self::DATE_TO_PARAM] = null;
        $params[self::TIME_FROM_PARAM] = null;
        $params[self::TIME_TO_PARAM] = null;
        $params[self::DIRECTION_PARAM] = null;

        if ($request->has(self::DATE_FROM_PARAM)) {
            $params[self::DATE_FROM_PARAM] = $request->input(self::DATE_FROM_PARAM);
        }
        if ($request->has(self::DATE_TO_PARAM)) {
            $params[self::DATE_TO_PARAM] = $request->input(self::DATE_TO_PARAM);
        }
        if ($request->has(self::TIME_FROM_PARAM)) {
            $params[self::TIME_FROM_PARAM] = $request->input(self::TIME_FROM_PARAM);
        }
        if ($request->has(self::TIME_TO_PARAM)) {
            $params[self::TIME_TO_PARAM] = $request->input(self::TIME_TO_PARAM);
        }
        if ($request->has(self::DIRECTION_PARAM)) {
            $params[self::DIRECTION_PARAM] = $request->input(self::DIRECTION_PARAM);
        }

        return $params;
    }
}
