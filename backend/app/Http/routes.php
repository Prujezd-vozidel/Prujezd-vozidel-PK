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
 * Vrati seznam mericich zarizeni.
 */
$app->get($apiUrlRoot.'devices', [
    'middleware' => 'jwtauth',
    'uses' => 'DeviceController@getDevice'
]);


/**
 * Vrati zaznamy o doprav e za casovy usek pro dane zarizeni.
 */
$app->get($apiUrlRoot.'devices/{id}', [
    'middleware' => 'jwtauth',
    'uses' => 'DeviceController@getDeviceById'
]);

/**
 * Vrati prumery dopravy pro danze zarizeni za casovy usek.
 */
$app->get($apiUrlRoot.'devices/{id}/time-period', [
   'middleware' => 'jwtauth',
    'uses' => 'DeviceController@getTrafficAverageByDevice'
]);


/**
 * Vrati vsechny typy aut.
 */
$app->get($apiUrlRoot.'vehicles', [
    'middleware' => 'jwtauth',
    'uses' => 'VehicleController@getAll'
]);

/**
 * Vrati vsechna mesta.
 */
$app->get($apiUrlRoot.'cities', [
    'middleware' => 'jwtauth',
    'uses' => 'LocationController@getCities'
]);

/**
 * Vygeneruje novy JWT s omezenou platnosti.
 */
$app->get($apiUrlRoot.'token', 'TokenController@generateToken');



// testovani
$app->get($apiUrlRoot.'header', 'DeviceController@headerTest');
