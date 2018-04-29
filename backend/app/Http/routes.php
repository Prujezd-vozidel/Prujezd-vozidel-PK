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

$apiUrlRoot='/api/v1/';

/**
 * Welcome endpoint.
 */
$app->get('/', function ()  {
    return 'Welcome.';
});

/**
 * Parametry v url:
 * address
 * showDirection
 */
$app->get($apiUrlRoot.'devices', 'DeviceController@getDevice');

//$app->get($apiUrlRoot.'devices/all', 'DeviceController@getAll');

/**
 * Parametry v url:
 * dateFrom
 * dateTo
 * timeFrom
 * timeTo
 * direction
 */
$app->get($apiUrlRoot.'devices/{id}', 'DeviceController@getDeviceById');

/**
 * Vrati vsechny typy aut.
 */
$app->get($apiUrlRoot.'vehicles', 'VehicleController@getAll');

/**
 * Vrati vsechna mesta.
 */
$app->get($apiUrlRoot.'cities', 'LocationController@getCities');

/**
 * Vygeneruje novy JWT s omezenou platnosti.
 */
$app->get($apiUrlRoot.'token', 'TokenController@generateToken');



// testovani
$app->get($apiUrlRoot.'header', 'DeviceController@headerTest');
