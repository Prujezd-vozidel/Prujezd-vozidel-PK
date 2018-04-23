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

$app->get('/', function ()  {
    return 'Funguje to.';
});

/**
 * Parametry v url:
 * address
 * showDirection
 */
$app->get($apiUrlRoot.'devices', 'DeviceController@getDevice');

$app->get($apiUrlRoot.'devices/all', 'DeviceController@getAll');

/**
 * Parametry v url:
 * dateFrom
 * dateTo
 * timeFrom
 * timeTo
 * direction
 */
$app->get($apiUrlRoot.'devices/{id}', 'DeviceController@getDeviceById');

//$app->get($apiUrlRoot.'devices/lastday', 'DeviceController@lastDay');

/**
 * Vrati vsechny typy aut.
 */
$app->get($apiUrlRoot.'vehicles', 'VehicleController@getAll');

$app->get($apiUrlRoot.'cities', 'LocationController@getCities');

