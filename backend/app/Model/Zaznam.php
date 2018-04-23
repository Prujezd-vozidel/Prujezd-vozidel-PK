<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 23.4.2018
 * Time: 12:47
 */

namespace App\Model;

use Illuminate\Support\Facades\DB;


/**
 * Trida reprezentujici entitu zarizeni v databazi.
 * @package App\Model
 */
class Zaznam extends BaseModel
{
    protected $table = 'zaznam';

    /**
     * Vrati posledni datum pro ktere existuji nejake zaznamy.
     */
    public static function lastInsertedDate() {
        return DB::table('zaznam_cas')->select(DB::raw('max(date(datetime_od)) as last_day'))->get();
    }

    /**
     * Vrati zaznamy pro urcite zarizeni.
     * Typ vozidla je vracen s kazdym zaznamem.
     *
     * @param $deviceId Id zarizeni pro ktere budou vraceny zaznamy.
     * @param $dateFrom Pocatecni datum. Null znamená poledni vlozeny den.
     * @param $dateTo Koncove datum. Null znamena posledni vlozeny den.
     * @param $timeFrom Pocatecni cas. Null znamena 00:00.
     * @param $timeTo Koncovy cas. Null znamena 23:59.
     * @param $direction Pozadovany smer. Null znamena oba smery.
     */
    public static function findByDevice($deviceId, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction) {
        $dateTimeFrom = null;
        $dateTimeTo = null;
        $lastDate = null;
        $dir = null;

        // jedno z omezujicich dat je null => ziskej posledni vlozene datum
        if ($dateFrom == null || $dateTo == null) {
            $lastDate = Zaznam::lastInsertedDate();
            if ($lastDate == null) {
                // database is empty
                // todo: error codes
                return "no-data";
            } else {
                $lastDate = $lastDate[0]->last_day;
            }
        }
        if ($dateFrom == null) {
            $dateFrom = $lastDate;
        }
        if ($dateTo == null) {
            $dateTo = $lastDate;
        }
        // omezujici casy
        if ($timeFrom == null) {
            $timeFrom = '00:00:00';
        }
        if ($timeTo == null) {
            $timeTo = '23:59:59';
        }
        $dateTimeFrom = date('Y-m-d H:i:s', strtotime("$dateFrom $timeFrom"));
        $dateTimeTo = date('Y-m-d H:i:s', strtotime("$dateTo $timeTo"));


        // vytvoreni query - vsechno to dat dohromady
        $query = DB::table('zaznam')
            ->join('zaznam_cas', 'zaznam.zaznam_cas_id', '=', 'zaznam_cas.id')
            ->join('vozidlo', 'zaznam.vozidlo_id', '=', 'vozidlo.id')
            ->select('zaznam_cas.datetime_od as datetimeFrom',
                'zaznam_cas.datetime_do as datetimeTo',
                'zaznam_cas.smer as direction',
                'zaznam.rychlost_prumer as speedAverage',
                'zaznam.vozidla_pocet as numberVehicle',
                'vozidlo.nazev as typeVehicle',
                'vozidlo.id as typeVehicleId')
            ->where('zaznam_cas.datetime_od', '>=', $dateTimeFrom)
            ->where('zaznam_cas.datetime_do', '<=', $dateTimeTo)
            ->where('zaznam_cas.zarizeni_id', '=', $deviceId);

        if($direction != null) {
            $query = $query->where('zaznam_cas.smer', '=', $direction);
        }

        return $query->get();
    }
}