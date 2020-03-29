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

Route::get('/preferences/module', 'Prefs\ModuleController@create');

Route::post('/preferences/module', 'Prefs\ModuleController@store')->name('storeModulePrefs');

Route::get('/preferences/ta', 'Prefs\TAController@create');

Route::post('/preferences/ta', 'Prefs\TAController@store')->name('storeTAPrefs');

// Prefs:: routes();
