<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

class TestClass {
    public $someInt = 10;
    public $someDouble = 10.5478;
    public $someString = 'Hell yeah bro!';
    public $someArray = array( "a", "b", 6 => "c", "d", );
}

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/hello', function () {
   return "Hello world!";
});

$router->get('/class', function() {
    return response()->json(new TestClass());
});

$router->get('/json', function () {
    return response()->json(['name' => 'Abigail', 'state' => 'CA']);
});

$router->get('/controller', 'TestClassController@getTestClass');

