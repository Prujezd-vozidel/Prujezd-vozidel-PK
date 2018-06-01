<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 22.4.2018
 * Time: 20:21
 */

namespace App\Model;

use Illuminate\Support\Facades\DB;

/**
 * Trida reprezentujici entitu zarizeni.
 * @package App\Model
 */
class Zarizeni extends BaseModel
{

    /**
     * Jmeno tabulky v databazi.
     *
     * @var string
     */
    protected $table = 'zarizeni';

    /**
     * Vrati vsechna zarizeni.
     * Mesto a ulice jsou vraceny spolu se zarizenim.
     *
     * @return mixed
     */
    public static function getAllJoinAddress()
    {
        return DB::table('zarizeni')
            ->join('ulice', 'zarizeni.ulice_id', '=', 'ulice.id')
            ->join('mesto', 'ulice.mesto_id', '=', 'mesto.id')
            ->select('zarizeni.id as id',
                'zarizeni.smer_popis as name',
                'ulice.nazev as street',
                'ulice.id as street_id',
                'ulice.zem_sirka as lat',
                'ulice.zem_delka as lng',
                'mesto.nazev as town',
                'mesto.id as town_id')
            ->get();
    }

    /**
     * Vrati zarizeni nalezene podle adresy (mesto+ulice).
     *
     * @param $address Adresa, jsou vraceny zaznamy u kterych ulice, nebo mesto odpovida adrese.
     * @param $showDirection 1 pokud má být rozlišen směr zařízení.
     * @return mixed
     */
    public static function findByAddressJoinAddress($address, $showDirection)
    {
        if (empty($address)) {
            Zarizeni::getAllJoinAddress();
        }

        $query = DB::table('zarizeni')
            ->join('ulice', 'zarizeni.ulice_id', '=', 'ulice.id')
            ->join('mesto', 'ulice.mesto_id', '=', 'mesto.id')
            ->join('zaznam_cas', 'zaznam_cas.zarizeni_id', '=', 'zarizeni.id')
            ->select('zarizeni.id as id',
                'zarizeni.smer_popis as name',
                'ulice.nazev as street',
                'ulice.id as street_id',
                'ulice.zem_sirka as lat',
                'ulice.zem_delka as lng',
                'mesto.nazev as town',
                'mesto.id as town_id',
                $showDirection ? 'zaznam_cas.smer as direction' : DB::Raw('null as direction'))
            ->where('ulice.nazev', 'like', '%' . $address . '%')
            ->orWhere('mesto.nazev', 'like', '%' . $address . '%')
            ->orWhere('zarizeni.smer_popis', 'like', '%' . $address . '%');

        if (!$showDirection) {
            $query = $query->groupBy('zarizeni.id');
        } else {
            $query = $query->groupBy('zarizeni.id', 'zaznam_cas.smer');
        }


        return $query->get();
    }

    /**
     * Vrati zarizeni se zadanym id.
     * Mesto a ulice jsou vraceny spolu se zarizenim.
     *
     * @param $id Id zarizeni.
     * @return mixed
     */
    public static function findByIdJoinAddress($id)
    {
        return DB::table('zarizeni')
            ->join('ulice', 'zarizeni.ulice_id', '=', 'ulice.id')
            ->join('mesto', 'ulice.mesto_id', '=', 'mesto.id')
            ->select('zarizeni.id as id',
                'zarizeni.smer_popis as name',
                'ulice.nazev as street',
                'ulice.id as street_id',
                'ulice.zem_sirka as lat',
                'ulice.zem_delka as lng',
                'mesto.nazev as town',
                'mesto.id as town_id')
            ->where('zarizeni.id', '=', $id)
            ->orderBy('zarizeni.id')
            ->first();
    }

    /**
     * Databazovy klic.
     *
     * @var long
     */
    public $id;

    /**
     * Popis smeru zarizeni.
     *
     * @var string
     */
    public $smer_popis;

    /**
     * Stav zarizeni.
     *
     * @var integer
     */
    public $stav;

    public function ulice()
    {
        return $this->belongsTo('App\Model\Ulice');
    }
}