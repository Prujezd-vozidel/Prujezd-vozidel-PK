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
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;

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


    public function getDevice(Request $request)
    {
        $address = null;
        $showDirection = 0;
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
    public function getDeviceByIdWithTraffic(Request $request, $id)
    {
        $device = $this->getDeviceById($id);
        if ($device != null) {
            $device->traffics = $this->findTrafficByDevice($request, $id);
            return json_encode($device);
        } else {
            return response('Not found.', 404);
        }
    }

    /**
     * Vrati zarizeni podle id, nebo null, pokud neni nalezeno.
     *
     * @param $id
     * @return Zarizeni
     */
    public function getDeviceById($id)
    {
        return Zarizeni::findByIdJoinAddress($id);
    }

    /**
     * Vrati zaznamy o doprave pro dane zarizeni. Request obsahuje dodatecne url parametry.
     *
     * @param Request $request
     * @param $deviceId
     */
    public function findTrafficByDevice(Request $request, $deviceId)
    {
        // nacti parametry
        $params = $this->loadDateTimeDirectionConstraints($request);
        $dateFrom = $params[self::DATE_FROM_PARAM];
        $dateTo = $params[self::DATE_TO_PARAM];
        $timeFrom = $params[self::TIME_FROM_PARAM];
        $timeTo = $params[self::TIME_TO_PARAM];
        $direction = $params[self::DIRECTION_PARAM];

        return Zaznam::findByDevice($deviceId, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction);
    }

    public function findDayAverageTrafficByDevice(Request $request, $deviceId) {
        // nacti parametry
        $params = $this->loadDateTimeDirectionConstraints($request);
        $dateFrom = $params[self::DATE_FROM_PARAM];
        $dateTo = $params[self::DATE_TO_PARAM];
        $direction = $params[self::DIRECTION_PARAM];

        return Zaznam::averageByDay($deviceId, $dateFrom, $dateTo, $direction);
    }

    /**
     * Nacte zarizeni spolecne se vsemi jeho zaznamy (podle url parametru) a vrati je jako stahnutelny csv soubor.
     * Csv bude obsahovat udaje o zarizeni na prvni radce, udaje o doprave na nasledujicich.
     *
     * Pokud zarizeni nebylo nalezeno, je vracena 404.
     *
     * @param Request $request
     * @param $id
     * @return Mixed_
     */
    public function getDeviceByIdAsCsv(Request $request, $id)
    {
        return $this->createCsvFileForDeviceData(
            $this->findDeviceByIdSetDates($request, $id),
            $this->findTrafficByDevice($request, $id),
            'doprava-export-'
        );
    }

    public function getTrafficAverageByDevice(Request $request, $id)
    {
        $device = $this->findDeviceByIdSetDates($request, $id);
        if ($device != null) {

            $device->traffics = $this->findTrafficAverageByDevice($request, $id);
            return json_encode($device);
        } else {
            return response('Not found.', 404);
        }
    }

    /**
     * Vrati prumerna data o doprave pro dane zarizeni jako stahnutelny csv soubor.
     *
     * @param Request $request
     * @param integer $id Id zarizeni.
     * @return Mixed_
     */
    public function getTrafficAverageByDeviceCsv(Request $request, $id) {
        return $this->createCsvFileForDeviceData(
            $this->findDeviceByIdSetDates($request, $id),
            $this->findDayAverageTrafficByDevice($request, $id),
            'doprava-prumery-export-'
            );
    }

    /**
     * Vrati prumerna data o doprave (pouze data o doprave) pro dane zarizeni.
     *
     * @param Request $request Request s parametry.
     * @param $deviceId Id zarizeni.
     * @return Mixed_
     */
    public function findTrafficAverageByDevice(Request $request, $deviceId) {
        // nacti parametry
        $params = $this->loadDateTimeDirectionConstraints($request);
        $dateFrom = $params[self::DATE_FROM_PARAM];
        $dateTo = $params[self::DATE_TO_PARAM];
        $timeFrom = $params[self::TIME_FROM_PARAM];
        $timeTo = $params[self::TIME_TO_PARAM];
        $direction = $params[self::DIRECTION_PARAM];

        return Zaznam::averageByDevice($deviceId, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction);
    }

    /**
     * Vrati denni prumery pro jednotlive typy vozidel.
     *
     * @param Request $request Request s parametry.
     * @param $id Id zarizeni.
     * @return Mixed_
     */
    public function getTrafficDayAverage(Request $request, $id)
    {
        $device = $this->findDeviceByIdSetDates($request, $id);

        if ($device != null) {
            $device->traffics = $this->findDayAverageTrafficByDevice($request, $id);
            return json_encode($device);
        } else {
            return response('Not found.', 404);
        }
    }


    /**
     * Vrati denni prumery pro jendotlive typy vozidel jako stahnutelny csv soubor.
     *
     * @param Request $request Request.
     * @param $id Id zarizeni.
     * @return Mixed_
     */
    public function getTrafficDayAverageCsv(Request $request, $id)
    {
        return $this->createCsvFileForDeviceData(
            $this->findDeviceByIdSetDates($request, $id),
            $this->findDayAverageTrafficByDevice($request, $id),
            'doprava-denni-prumery-export-'
        );
    }

    public function getAll()
    {
        return Zarizeni::getAllJoinAddress();
    }

    public function lastDay()
    {
        return Zaznam::lastInsertedDate();
    }

    public function headerTest(Request $request)
    {
        $authHeader = $request->header("jwt");

        if ($authHeader != null) {
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
     * @return array Pole s nactenymi parametry.
     */
    private function loadDateTimeDirectionConstraints(Request $request)
    {
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

    /**
     * Konvertuje objekt typu stdClass na pole pomoci funkci json_decode(), json_encode().
     *
     * @param $object
     * @return Array_
     */
    private function stdClassToArray($object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * Prida hlavicku pole a data z pole do csv souboru.
     *
     * @param $df Ukazatel na soubor.
     * @param $array Pole.
     */
    private function addArrayToCsvFile($df, $array) {
        if ($array != null && count($array) > 0) {
            $row = $this->stdClassToArray($array[0]);
            fputcsv($df, array_keys($row));

            foreach ($array as $row) {
                fputcsv($df, $row);
            }
        }
    }

    /**
     * Funkce se pokusi nalezt zarizeni (s adresou) podle id a pokud najde, pokusi se z requestu dostat hodnoty parametru DATE_FROM,
     * DATE_TO a DIRECTION, ktere pak prida do nalezeneho zarizeni, ktere vrati. Pokud request uvedene parametry neobsahuje, budou
     * v zarizeni nastaveny na null.
     *
     * Pokud zarizeni nenajde, vrati null.
     *
     * @param Request $request Request s parametry.
     * @param integer $id id zarizeni.
     * @return Mixed_
     */
    private function findDeviceByIdSetDates(Request $request, $id) {
        // nacteni parametru
        $params = $this->loadDateTimeDirectionConstraints($request);
        $dateFrom = $params[self::DATE_FROM_PARAM];
        $dateTo = $params[self::DATE_TO_PARAM];
        $direction = $params[self::DIRECTION_PARAM];

        $device = Zarizeni::findByIdJoinAddress($id);
        if ($device != null) {
            $device->dateFrom = $dateFrom;
            $device->dateTo = $dateTo;

            if ($direction != null) {
                $device->direction = intval($direction);
            }
        }

        return $device;
    }

    /**
     * Vytvoří csv soubor pro zarizeni a souvisejici data o doprave a vynuti jeho stazeni.
     *
     * @param Mixed_ $device Metadata zarizeni (tj. zarizeni bez dat o doprave). Pokud null, je vracena response 404.
     * @param Mixed_ $traffic Data o doprave.
     * @param string $namePrefix Prefix jmena stahovaneho souboru.
     * @return Mixed_
     */
    private function createCsvFileForDeviceData($device, $traffic, $namePrefix) {

        if ($device != null) {
            $devArray = json_decode(json_encode($device), true);
            // tmp file
            $tmpFileName = $namePrefix . time() . '.csv';
            $tmpFilePath = tempnam(sys_get_temp_dir(), $tmpFileName);
            $df = fopen($tmpFilePath, 'w');

            // hlavicka pro zarizeni
            fputcsv($df, array_keys($devArray));
            fputcsv($df, $devArray);

            // zaznamy o doprave
            $traffic = $this->stdClassToArray($traffic);
            $this->addArrayToCsvFile($df, $traffic);
            fclose($df);
            return response()->download($tmpFilePath, $tmpFileName, array())->deleteFileAfterSend(true);
        } else {
            return response('Not found.', 404);
        }
    }
}
