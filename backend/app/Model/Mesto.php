<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 22.4.2018
 * Time: 19:51
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Entita reprezentujici tabulku mesto v databazi.
 * @package App\Model
 */
class Mesto extends BaseModel
{
    /**
     * Nazev tabulky tak jak je v databazi.
     *
     * @var string
     */
    protected $table = 'mesto';

    /**
     * Databazovy klic.
     */
    public $id;

    /**
     * Nazev mesta.
     */
    public $nazev;

    /**
     * Ulice prirazene mestu.
     */
    public function ulice() {
        return $this->hasMany('App\Model\Ulice');
    }

    public function zarizeni() {
        return $this->hasManyThrough('App\Model\Zarizeni', 'App\Model\Ulice');
    }
}