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
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function getDevice(Request $request) {
        $address='';
        $showDirection=0;
        if ($request->has('address')) {
            $address = $request->input('address');
        }

        if ($request->has('showDirection')) {
            $showDirection = ($request->input('showDirection') === 1);
        }

        $device = new Device();
        $device->id = 1;
        $device->name = 'device';
        $device->street = $address;
        $device->town = $address;

//        return response()->json($device);
        return Zarizeni::findByAddressJoinAddress('Česká Kubice', 'Česká Kubice');
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

        return Zarizeni::findByIdJoinAddress($id);
    }

    public function getAll() {
        return Zarizeni::getAllJoinAddress();
    }
}
