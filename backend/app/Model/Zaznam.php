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
     * @return String Posledni datum pro ktere existuji zaznamy.
     */
    public static function lastInsertedDate() {
        return DB::table('zaznam_cas')->select(DB::raw('max(date(datetime_od)) as last_day'))->get();
    }

    /**
     * Vrati prumery dopravy v casovem useku podle typu vozidla.
     *
     * @param String $deviceId Id zarizeni pro ktere budou vraceny zaznamy.
     * @param String $dateFrom Pocatecni datum. Null znamená poledni vlozeny den.
     * @param String $dateTo Koncove datum. Null znamena posledni vlozeny den.
     * @param String $timeFrom Pocatecni cas. Null znamena 00:00.
     * @param String $timeTo Koncovy cas. Null znamena 23:59.
     * @param int $direction Pozadovany smer. Null znamena oba smery.
     * @return array Prumery dopravy pro casovy usek podle typu vozidla.
     */
    public static function averageByDevice($deviceId, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction) {
        $dateTimeFrom = null;
        $dateTimeTo = null;
        $lastDate = null;
        $dir = null;

        // jedno z omezujicich dat je null => ziskej posledni vlozene datum
        if ($dateFrom == null || $dateTo == null) {
            $lastDate = Zaznam::lastInsertedDate();
            if ($lastDate == null) {
                // database is empty
                return "no-data";
            } else {
                $lastDate = $lastDate[0]->last_day;
            }
        }
        $dateTimeFrom = self::concatDateTime($dateFrom, $timeFrom, $lastDate, '00:00:00');
        $dateTimeTo = self::concatDateTime($dateTo, $timeTo, $lastDate, '23:59:59');


        // vytvoreni query - vsechno to dat dohromady
        $query = DB::table('zaznam')
            ->join('zaznam_cas', 'zaznam.zaznam_cas_id', '=', 'zaznam_cas.id')
            ->join('vozidlo', 'zaznam.vozidlo_id', '=', 'vozidlo.id')
            ->select(DB::raw("
                date_format(zaznam_cas.datetime_od, '%Y-%m-%d') as dateFrom,
                date_format(zaznam_cas.datetime_do,  '%Y-%m-%d') as dateTo,
                date_format(zaznam_cas.datetime_od, '%H:%i:%s') as timeFrom,
                date_format(zaznam_cas.datetime_do, '%H:%i:%s') as timeTo,
                zaznam_cas.smer as direction,
                avg(zaznam.rychlost_prumer) as speedAverage,
                sum(zaznam.vozidla_pocet) as numberVehicle,
                vozidlo.nazev as typeVehicle,
                vozidlo.id as typeVehicleId
            "))
            ->where('zaznam_cas.datetime_od', '>=', $dateTimeFrom)
            ->where('zaznam_cas.datetime_do', '<=', $dateTimeTo)
            ->where('zaznam_cas.zarizeni_id', '=', $deviceId)
        ;

        if($direction != null) {
            $query = $query->where('zaznam_cas.smer', '=', $direction);
        }

        // pridat grouping a razeni nakonec
        $query = $query
                ->groupBy('timeFrom', 'timeTo', 'typeVehicleId')
                ->orderBy('dateFrom','timeFrom', 'typeVehicleId');

        return $query->get();
    }

    /**
     * Vrati zaznamy pro urcite zarizeni.
     * Typ vozidla je vracen s kazdym zaznamem.
     *
     * @param String $deviceId Id zarizeni pro ktere budou vraceny zaznamy.
     * @param String $dateFrom Pocatecni datum. Null znamená poledni vlozeny den.
     * @param String $dateTo Koncove datum. Null znamena posledni vlozeny den.
     * @param String $timeFrom Pocatecni cas. Null znamena 00:00.
     * @param String $timeTo Koncovy cas. Null znamena 23:59.
     * @param int $direction Pozadovany smer. Null znamena oba smery.
     * @return array Zaznamy o doprave v casovem useku pro dane zarizeni.
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
                return "no-data";
            } else {
                $lastDate = $lastDate[0]->last_day;
            }
        }
        $dateTimeFrom = self::concatDateTime($dateFrom, $timeFrom, $lastDate, '00:00:00');
        $dateTimeTo = self::concatDateTime($dateTo, $timeTo, $lastDate, '23:59:59');


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

    /**
     * Spoji datum a cas. Pokud je jedna ze slozek null,
     * pouzije se defaultni hodnota.
     *
     * Vysledny datovy format: Y-m-d H:i:s
     *
     * @param String $date Datova slozka.
     * @param String $time Casova slozka.
     * @param String $defDate Defaultni hodnota pro datovou slozku.
     * @param String $defTime Defaultni hodnota pro casovou slozku.
     * @return String Spojene datum a cas.
     */
    private static function concatDateTime($date, $time, $defDate, $defTime) {
        $dateTime = null;
        $d = $date == null ? $defDate : $date;
        $t = $time == null ? $defTime : $time;

        $dateTime = date('Y-m-d H:i:s', strtotime("$d $t"));
        return $dateTime;
    }
}