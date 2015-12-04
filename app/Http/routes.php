<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
/* =========== Login Authentication ============ */

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

/*Route::get('/',function(){
	$baseURL = '../public/';
	return view('auth.login',[])->with('baseURL', $baseURL);;
});*/

/* =========== Static Page (As per requirements) ============ */
Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

Route::get('index', 'HomeController@index');


/* =========== Web Request ============ */

Route::post('plantrip', 'WebController@planTrip');

Route::post('places/{latlng}/{placetype}/{pitstops}', 'WebController@placesNearby');

Route::get('trips', 'WebController@allTrips');

Route::post('addPlace', 'WebController@addPlace');

Route::any('saveTrip/{itineraryKey}/{pitstops}', 'WebController@saveTrip');

Route::post('editTrip', 'WebController@editTrip');

Route::post('removePitstop', 'WebController@removePitstop');

Route::post('removeTrip', 'WebController@removeTrip');

Route::post('deleteTrip', 'WebController@deleteTrip');

Route::post('viewtrip', 'WebController@viewTrip');

Route::get('checkKey/{itineraryKey}', 'WebController@checkKey');

/* =========== API Request ============ */

Route::get('plantrip/{tripStart}/{tripEnd}', 'RestController@planTripLocations');

Route::get('places/{latlng}/{placetype}', 'RestController@placesNearbyCity');

Route::get('allTrips', 'RestController@allTrips');

Route::any('viewtrip/{itineraryKey}/{tripId}', 'RestController@apiviewTrip');

Route::any('trip/{tripId}', 'RestController@tripById');
