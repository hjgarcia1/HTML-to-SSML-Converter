<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'SsmlController@index');
Route::get('/create', 'SsmlController@show');
Route::post('/store', 'SsmlController@store');
Route::get('/ssml/{id}', 'SsmlController@edit');
Route::delete('/ssml/{id}', 'SsmlController@delete');
