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
$corsMiddle = 'cors';
$jwtMiddle = 'jwtauth';

/**
 * Welcome endpoint.
 */
$app->get('/', function ()  {
    return 'Welcome.';
});

/**
 * Vrati seznam mericich zarizeni.
 */
$app->get($apiUrlRoot.'devices', [
    'middleware' => [$corsMiddle, $jwtMiddle],
    'uses' => 'DeviceController@getDevice'
]);


/**
 * Vrati zaznamy o doprave za casovy usek pro dane zarizeni.
 */
$app->get($apiUrlRoot.'devices/{id}', [
    'middleware' => [$corsMiddle, $jwtMiddle],
    'uses' => 'DeviceController@getDeviceByIdWithTraffic'
]);

$app->get($apiUrlRoot.'devices/{id}/csv', [
    'middleware' => $jwtMiddle,
    'uses' => 'DeviceController@getDeviceByIdAsCsv'
]);

/**
 * Vrati prumery dopravy pro danze zarizeni za casovy usek.
 */
$app->get($apiUrlRoot.'devices/{id}/time-period', [
    'middleware' => [$corsMiddle, $jwtMiddle],
    'uses' => 'DeviceController@getTrafficAverageByDevice'
]);

/**
 * Vrati prumery dopravy pro danze zarizeni za casovy usek jako csv.
 */
$app->get($apiUrlRoot.'devices/{id}/time-period/csv', [
    'middleware' => $jwtMiddle,
    'uses' => 'DeviceController@getTrafficAverageByDeviceCsv'
]);


/**
 * Vrati denni prumery podle typu vozidla.
 */
$app->get($apiUrlRoot.'devices/{id}/day-period', [
    'middleware' => [$corsMiddle, $jwtMiddle],
    'uses' => 'DeviceController@getTrafficDayAverage'
]);

/**
 * Vrati denni prumery podle typu vozidla jako csv soubor.
 */
$app->get($apiUrlRoot.'devices/{id}/day-period/csv', [
    'middleware' => $jwtMiddle,
    'uses' => 'DeviceController@getTrafficDayAverageCsv'
]);

/**
 * Vrati vsechny typy aut.
 */
$app->get($apiUrlRoot.'vehicles', [
    'middleware' => [$corsMiddle, $jwtMiddle],
    'uses' => 'VehicleController@getAll'
]);

/**
 * Vrati vsechna mesta.
 */
$app->get($apiUrlRoot.'cities', [
    'middleware' => [$corsMiddle, $jwtMiddle],
    'uses' => 'LocationController@getCities'
]);

/**
 * Vygeneruje novy JWT s omezenou platnosti.
 */
$app->get($apiUrlRoot.'token', 'TokenController@generateToken');



// testovani
$app->get($apiUrlRoot.'header', 'DeviceController@headerTest');
