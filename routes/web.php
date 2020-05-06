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


Auth::routes();


Route::get('/', 'HomeController@index'); // the '/' sets the home page


Route::get('/Admin/university/signup', 'Admin\UniversityController@index');

Route::post('/Admin/university/signup', 'Admin\UniversityController@register')->name('storeUni');


Route::get('/home', 'HomeController@index')->name('home');


Route::get('/Admin/university-users/add', 'Admin\UniversityUsersController@create');

Route::post('/Admin/university-users/add', 'Admin\UniversityUsersController@store')->name('storeUniversityUser');



Route::get('/modules/prefs/show/{module_id}/{academic_year}', 'Prefs\moduleController@show');

Route::post('/modules/prefs/all', 'Prefs\moduleController@showAll')->name('showAllModulePrefs');

Route::get('/modules/prefs/all', 'Prefs\moduleController@index');


// All modules with or without prefs for all convenors
Route::get('/module/convenor', 'Prefs\ConvenorController@index');


Route::get('/Module/add', 'Prefs\ModuleController@create');

Route::post('/Module/add', 'Prefs\ModuleController@store')->name('storeModulePrefs');

Route::get('/Module/edit/{module_id}/{academic_year}', 'Prefs\ModuleController@edit');

Route::get('/Module/delete/{module_id}/{academic_year}', 'Prefs\ModuleController@destroy');

Route::post('/Module/update', 'Prefs\ModuleController@update')->name('updateModulePrefs');







Route::get('/TA/add', 'Prefs\TAController@create');

Route::post('/TA/add', 'Prefs\TAController@store')->name('storeTAPrefs');

Route::get('/TA', 'Prefs\TAController@index');


Route::get('/Admin/modules/add', 'Admin\ModuleController@create');

Route::post('/Admin/modules/add', 'Admin\ModuleController@store')->name('storeModule');


Route::post('/Admin/academic-years/update', 'Admin\AcademicYearController@update')->name('updateAcademicYear');


Route::get('/config', 'Admin\ConfigurationController@index');

Route::get('/config/add', 'Admin\ConfigurationController@create')->name('addConfigs');

Route::post('/config/add', 'Admin\ConfigurationController@store')->name('storeConfigs');


Route::get('/allocations', 'Allocation\AllocationController@index');

Route::get('/allocations/store', 'Allocation\AllocationController@store')->name('allocate');

Route::get('/allocations/delete', 'Allocation\AllocationController@destroy')->name('deleteAllocation');

Route::get('/allocations/module/rol', 'Allocation\AllocationController@createModuleROLs')->name('createModuleROLs');

Route::get('/allocations/module/delete-rol', 'Allocation\AllocationController@deleteModuleROLs')->name('deleteModuleROLs');

Route::get('allocations/missing-prefs', 'Allocation\AllocationController@missingPrefs');




// Route::get('/', function () {
//     return view('welcome');
//     // return 'Hello';
// });

// Calling index() from PagesController
