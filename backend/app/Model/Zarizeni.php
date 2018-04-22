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
    public static function getAllJoinAddress() {
        return DB::table('zarizeni')
            ->join('ulice', 'zarizeni.ulice_id', '=', 'ulice.id')
            ->join('mesto', 'ulice.mesto_id', '=', 'mesto.id')
            ->select('zarizeni.*', 'ulice.nazev as ulice_nazev', 'ulice.id as ulice_id', 'mesto.nazev as mesto_nazev', 'mesto.id as mesto_id')
            ->get();
    }

    /**
     * Vrati zarizeni nalezene podle adresy (mesto+ulice).
     * Mesto a ulice jsou vraceny spolu se zarizenim.
     *
     * @param $street Nazev ulice.
     * @param $town Nazev mesta.
     * @return mixed
     */
    public static function findByAddressJoinAddress($street, $town) {
        return DB::table('zarizeni')
            ->join('ulice', 'zarizeni.ulice_id', '=', 'ulice.id')
            ->join('mesto', 'ulice.mesto_id', '=', 'mesto.id')
            ->select('zarizeni.*', 'ulice.nazev as ulice_nazev', 'ulice.id as ulice_id', 'mesto.nazev as mesto_nazev', 'mesto.id as mesto_id')
            ->where('ulice.nazev', '=', $street)
            ->where('mesto.nazev', '=', $town)
            ->get();
    }

    /**
     * Vrati zarizeni se zadanym id.
     * Mesto a ulice jsou vraceny spolu se zarizenim.
     *
     * @param $id Id zarizeni.
     * @return mixed
     */
    public static function findByIdJoinAddress($id) {
        return DB::table('zarizeni')
            ->join('ulice', 'zarizeni.ulice_id', '=', 'ulice.id')
            ->join('mesto', 'ulice.mesto_id', '=', 'mesto.id')
            ->select('zarizeni.*', 'ulice.nazev as ulice_nazev', 'ulice.id as ulice_id', 'mesto.nazev as mesto_nazev', 'mesto.id as mesto_id')
            ->where('zarizeni.id', '=', $id)
            ->get();
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

    public function ulice() {
        return $this->belongsTo('App\Model\Ulice');
    }
}