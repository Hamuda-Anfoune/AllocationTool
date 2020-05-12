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

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/admin/university/signup', 'Admin\UniversityController@index');

Route::post('/admin/university/signup', 'Admin\UniversityController@register')->name('storeUni');


Route::get('/admin/university-users', 'Admin\UniversityUsersController@index');

Route::get('/admin/university-users/add', 'Admin\UniversityUsersController@create');

Route::post('/admin/university-users/add', 'Admin\UniversityUsersController@store')->name('storeUniversityUser');


Route::get('/admin/show/all-registered-users', 'Admin\UserController@index');


Route::get('/admin/modules/add', 'Admin\ModuleController@create');

Route::post('/admin/modules/add', 'Admin\ModuleController@store')->name('storeModule');


Route::post('/admin/academic-years/update', 'Admin\AcademicYearController@update')->name('updateAcademicYear');


Route::get('/admin/config', 'Admin\ConfigurationController@index');

Route::get('/admin/config/add', 'Admin\ConfigurationController@create')->name('addConfigs');

Route::post('/admin/config/add', 'Admin\ConfigurationController@store')->name('storeConfigs');


Route::get('/admin/dashboard', 'Allocation\AllocationController@allocationData');


// will finish creating ROLs then allcate
Route::get('/admin/allocations/allocate', 'Allocation\AllocationController@store')->name('allocate');

Route::get('/admin/allocations/store', 'Allocation\AllocationController@store');

Route::get('/admin/allocations', 'Allocation\AllocationController@index');

Route::get('admin/allocations/show/{allocation_id}', 'Allocation\AllocationController@show');

Route::get('/admin/allocations/delete/{allocation_id}', 'Allocation\AllocationController@destroy');

Route::get('/admin/allocations/delete', 'Allocation\AllocationController@destroy')->name('deleteCurrentAllocation');

Route::get('/admin/allocations/module/delete-rol', 'Allocation\AllocationController@deleteModuleROLs')->name('deleteModuleROLs');

Route::get('/admin/allocations/missing-prefs', 'Allocation\AllocationController@missingPrefs');


// All modules with or without prefs for all convenors
Route::post('/modules/prefs/all', 'Prefs\moduleController@showAll')->name('showAllModulePrefs');

Route::get('/modules/prefs/all', 'Prefs\moduleController@index');


Route::get('/module/convenor', 'Prefs\ConvenorController@index');

Route::get('/module/prefs/add', 'Prefs\ModuleController@create');

Route::post('/module/prefs/add', 'Prefs\ModuleController@store')->name('storeModulePrefs');

Route::get('/modules/prefs/show/{module_id}/{academic_year}', 'Prefs\moduleController@show');

Route::get('/module/prefs/edit/{module_id}/{academic_year}', 'Prefs\ModuleController@edit');

Route::post('/module/prefs/update', 'Prefs\ModuleController@update')->name('updateModulePrefs');

Route::get('/module/prefs/delete/{module_id}/{academic_year}', 'Prefs\ModuleController@destroy');



Route::get('/TA', 'Prefs\TAController@index');

Route::get('/TA/prefs/add', 'Prefs\TAController@create');

Route::post('/TA/prefs/add', 'Prefs\TAController@store')->name('storeTAPrefs');

Route::get('/TA/prefs/show/{preference_id}', 'Prefs\TAController@show');

Route::get('/TA/prefs/edit/{preference_id}', 'Prefs\TAController@edit');

Route::post('/TA/prefs/update', 'Prefs\TAController@update')->name('updateTAPrefs');

Route::get('/TA/prefs/delete/{preference_id}', 'Prefs\TAController@destroy');





// Route::get('/', function () {
//     return view('welcome');
//     // return 'Hello';
// });

// Calling index() from PagesController
