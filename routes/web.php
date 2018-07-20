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

Route::get('/', function () {
    return view('welcome');
});



Route::get('contact/apiContact','ContactController@apiContact')->name('contact/apiContact');
Route::resource('contact','ContactController');

Route::get('/exportpdf',  'ContactController@exportPDF')->name('/exportpdf');
Route::get('/exportexcel','ContactController@exportEXCEL')->name('/exportexcel');

