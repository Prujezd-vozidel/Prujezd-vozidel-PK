<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 20.4.2018
 * Time: 20:25
 */

namespace App\Model;


class Vehicle
{
    public $id;
    public $name;

    public static function create($id, $name) {
        $inst = new Vehicle();
        $inst->id = $id;
        $inst->name = $name;

        return $inst;
    }
}