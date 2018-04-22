<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 22.4.2018
 * Time: 20:08
 */

namespace App\Model;


/**
 * Trida reprezentujici entitu ulice.
 * @package App\Model
 */
class Ulice extends BaseModel
{
    /**
     * Nazev tabulky.
     * @var string
     */
    protected $table = 'ulice';

    /**
     * Databazovy klic.
     */
    public $id;

    /**
     * Nazev ulice.
     */
    public $nazev;

    /**
     * 1:N relace s entitou mesto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mesto() {
        return $this->belongsTo('App\Model\Mesto');
    }


    public function zarizeni() {
        return $this->hasMany('App\Model\Zarizeni');
    }
}