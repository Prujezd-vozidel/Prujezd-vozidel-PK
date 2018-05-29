<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 23.4.2018
 * Time: 14:57
 */

namespace App\Model;


/**
 * Trida reprezentujici entitu vozidlo.
 *
 * @package App\Model
 */
class Vozidlo extends BaseModel
{
    /**
     * Nazev tabulky v databazi.
     * @var string
     */
    protected $table = 'vozidlo';

    public static function getAll() {
        return DB::table('vozidlo')
            ->select('vozidlo.id', 'vozidlo.nazev as name')
            ->get();
    }
}