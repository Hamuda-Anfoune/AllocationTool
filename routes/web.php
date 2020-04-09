<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
//     // return 'Hello';
// });

// Calling index() from PagesController

use App\Http\Controllers\PreferencesController;

Route::get('/', 'HomeController@index'); // the '/' sets the home page

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/Module/add', 'Prefs\ModuleController@create');

Route::post('/Module/add', 'Prefs\ModuleController@store')->name('storeModulePrefs');

Route::get('/Module', 'Prefs\ModuleController@index');

Route::get('/TA/add', 'Prefs\TAController@create');

Route::post('/TA/add', 'Prefs\TAController@store')->name('storeTAPrefs');

Route::get('/TA', 'Prefs\TAController@index');

Route::get('/allocations/create', 'Allocation\AllocationController@create');

Route::get('/config', 'Admin\ConfigurationController@index');

Route::get('/config/add', 'Admin\ConfigurationController@create')->name('addConfigs');

Route::post('/config/add', 'Admin\ConfigurationController@store')->name('storeConfigs');



// Prefs:: routes();
