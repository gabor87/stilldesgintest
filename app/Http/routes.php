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

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');
Route::get('/tasks', 'TasksController@index')->middleware('auth');
Route::any('/tasks/create', 'TasksController@create')->middleware('auth');
Route::any('/tasks/{id}/update', 'TasksController@update')->middleware('auth');
Route::get('/tasks/{id}/delete', 'TasksController@delete')->middleware('auth');
