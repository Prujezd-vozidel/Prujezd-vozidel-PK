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
     * Vrati posledni datum pro ktere existuji zaznamy v tabulce zaznam_cas.
     * @return String Posledni datum pro ktere existuji zaznamy.
     */
    public static function lastInsertedDate()
    {
        return DB::table('zaznam_cas')
            ->join('datum', 'zaznam_cas.datum_id', '=', 'datum.id')
            ->select(DB::raw(
                'max(date(datum.od)) as last_day'
            ))
            ->get();
    }

    /**
     * Vrati posledni datum pro ktere existuji zaznamy v tabulce zaznam_prum_den.
     * @return String Posledni datum pro ktere existuji zaznamy.
     */
    public static function lastDayAverageInsertedDate() {
        return DB::table('zaznam_prum_den')
            ->join('datum', 'zaznam_prum_den.datum_id', '=', 'datum.id')
            ->select(DB::raw('
                max(date(datum.od)) as last_day_from,
                max(date(datum.do)) as last_day_to'
            ))
            ->get();
    }

    /**
     * Vrati posledni datum pro ktere existuji zaznamy v tabulce zaznam_prum_den.
     * @return String Posledni datum pro ktere existuji zaznamy.
     */
    public static function lastDateAndFirstDate() {
        return DB::table('zaznam_prum_den')
            ->join('datum', 'zaznam_prum_den.datum_id', '=', 'datum.id')
            ->select(DB::raw('
                max(date(datum.od)) as last_date,
                min(date(datum.od)) as first_date'
            ))
            ->first();
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
     * @return \stdClass Prumery dopravy pro casovy usek podle typu vozidla.
     */
    public static function averageByDevice($deviceId, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction)
    {
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

        // vytvoreni query - vsechno to dat dohromady
        $query = DB::table('zaznam_cas')
            ->join('zaznam', 'zaznam.zaznam_cas_id', '=', 'zaznam_cas.id')
            ->join('datum', 'zaznam_cas.datum_id', '=', 'datum.id')
            ->select(DB::raw("
                date_format(datum.od, '%H:%i') as timeFrom,
                date_format(datum.do, '%H:%i') as timeTo,
                ROUND(AVG(zaznam.rychlost_prumer), 0) as speedAverage,
                CAST(SUM(zaznam.vozidla_pocet) as UNSIGNED) as numberVehicle,
                ROUND(AVG(zaznam.vozidla_pocet), 0) as numberVehicleAverage,
                zaznam.vozidlo_id as typeVehicleId
            "))
            ->whereDate('datum.od', '>=', $dateFrom == null ? $lastDate : $dateFrom)
            ->whereDate('datum.od', '<=', $dateTo == null ? $lastDate : $dateTo)
            ->whereTime('datum.od', '>=', $timeFrom == null ? '08:00:00' : $timeFrom)
            ->whereTime('datum.od', '<=', $timeTo == null ? '23:59:59' : $timeTo)
            ->where('zaznam_cas.zarizeni_id', '=', $deviceId);

        if ($direction != null) {
            $query = $query->where('zaznam_cas.smer', '=', $direction);
        }

        // pridat grouping a razeni nakonec
        $query = $query
            ->groupBy('timeFrom', 'zaznam.vozidlo_id')
            ->orderBy('timeFrom', 'asc')
            ->orderBy('zaznam.vozidlo_id', 'asc');

        return $query->get();
    }

    /**
     * Vrati denni prumery podle typu vozidla.
     *
     * @param integer $deviceId Id Zarizeni.
     * @param String $dateFrom Pocatecni datum. Null znamená poledni vlozeny den.
     * @param String $dateTo Koncove datum. Null znamena posledni vlozeny den.
     * @param integer $direction Pozadovany smer. Null znamena oba smery.
     * @return \stdClass Denni prumery podle typu vozidla.
     */
    public static function averageByDay($deviceId, $dateFrom, $dateTo, $direction) {
        $lastDateFrom = null;
        $lastDateTo = null;
        $lastDate = null;
        $dir = null;

        // jedno z omezujicich dat je null => ziskej posledni vlozene datum
        if ($dateFrom == null || $dateTo == null) {
            $lastDate = Zaznam::lastDayAverageInsertedDate();
            if ($lastDate == null) {
                // database is empty
                return "no-data";
            } else {
                $lastDateFrom = $lastDate[0]->last_day_from;
                $lastDateTo = $lastDate[0]->last_day_to;
            }
        }

        // vytvoreni query - vsechno to dat dohromady
        $query = DB::table('zaznam_prum_den')
            ->join('datum', 'zaznam_prum_den.datum_id', '=', 'datum.id')
            ->select(DB::raw("
                date_format(datum.od, '%Y-%m-%d') as date,
                ROUND(AVG(zaznam_prum_den.rychlost_prumer), 0) as speedAverage,
                CAST(SUM(zaznam_prum_den.vozidla_pocet) as UNSIGNED) as numberVehicle,
                zaznam_prum_den.vozidlo_id as typeVehicleId
            "))
            ->whereDate('datum.od', '>=', $dateFrom == null ? $lastDateFrom : $dateFrom)
            ->whereDate('datum.od', '<=', $dateTo == null ? $lastDateTo : $dateTo)
            ->where('zaznam_prum_den.zarizeni_id', '=', $deviceId);

        if ($direction != null) {
            $query = $query->where('zaznam_prum_den.smer', '=', $direction);
        }

        // pridat grouping a razeni nakonec
        $query = $query
            ->groupBy('date', 'zaznam_prum_den.vozidlo_id')
            ->orderBy('date', 'asc')
            ->orderBy('zaznam_prum_den.vozidlo_id', 'asc');

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
    public static function findByDevice($deviceId, $dateFrom, $dateTo, $timeFrom, $timeTo, $direction)
    {
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
            ->join('datum', 'zaznam_cas.datum_id', '=', 'datum.id')
            ->select(
                'datum.od as datetimeFrom',
                'datum.do as datetimeTo',
                'zaznam_cas.smer as direction',
                'zaznam.rychlost_prumer as speedAverage',
                'zaznam.vozidla_pocet as numberVehicle',
                'vozidlo.nazev as typeVehicle',
                'vozidlo.id as typeVehicleId')
            ->where('datum.od', '>=', $dateTimeFrom)
            ->where('datum.do', '<=', $dateTimeTo)
            ->where('zaznam_cas.zarizeni_id', '=', $deviceId);

        if ($direction != null) {
            $query = $query->where('zaznam_cas.smer', '=', $direction);
        }

        $query = $query->orderBy('datetimeFrom', 'asc');

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
    private static function concatDateTime($date, $time, $defDate, $defTime)
    {
        $dateTime = null;
        $d = $date == null ? $defDate : $date;
        $t = $time == null ? $defTime : $time;

        $dateTime = date('Y-m-d H:i:s', strtotime("$d $t"));
        return $dateTime;
    }

}