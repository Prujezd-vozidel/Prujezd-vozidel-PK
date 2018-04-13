<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 2.4.2018
 * Time: 17:01
 */

namespace App\Http\Controllers;

class TestClass {
    public $someInt = 10;
    public $someDouble = 10.5478;
    public $someString = 'Hell yeah bro!';
    public $someArray = array( "a", "b", 6 => "c", "d", );
}

class TestClassController extends Controller
{
    public function getTestClass() {
        return response()->json(new TestClass());
    }
}