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

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');


Route::get('/set', 'viewController@index');
Route::get('/action/set', 'viewController@set')->name('action_set');
Route::get('/action/update', 'viewController@update')->name('action_Update');
Route::get('/action/disable', 'viewController@disable')->name('action_disable');
Route::get('/action/info', 'viewController@info')->name('action_info');


Route::get('data/get','data@getData')->name('getData');
Route::get('test/code','viewController@testCode');