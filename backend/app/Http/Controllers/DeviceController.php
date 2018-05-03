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
        $address = null;
        $showDirection=0;
        if ($request->has('address')) {
            $address = $request->input('address');
        }

        if ($request->has('showDirection')) {
            $showDirection = ($request->input('showDirection') === 1);
        }

        $device = Zarizeni::findByAddressJoinAddress($address);

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
            $device->traffic = Zaznam::findByDevice($id, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction);
            return json_encode($device);
        } else {
            return response('Not found.', 404);
        }
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
