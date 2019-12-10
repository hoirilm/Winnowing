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

Route::get('/', function()
{
    return view('welcome');
});

// Route::get('/corpus', 'PagesController@corpus');
// Route::get('/corpus/datatables', 'DocumentsController@datatables');
Route::get('/corpus', 'DocumentsController@index');
Route::get('/corpus/search', 'DocumentsController@search');
// Route::put('/corpus', 'DocumentsController@search');
Route::get('/corpus/{corpus}', 'DocumentsController@show');
Route::delete('corpus/{corpus}', 'DocumentsController@destroy');
Route::post('/corpus', 'DocumentsController@store');
Route::get('/advance', 'PagesController@advance');
Route::post('/advance', 'AdvanceController@winnowing');

Route::get('/onetomany', 'PagesController@oneToMany');
Route::post('/onetomany', 'OneToManyController@winnowing');
Route::put('/onetomany', 'OneToManyController@detail');


Route::get('/manytomany', 'PagesController@manytomany');
Route::post('/manytomany', 'ManyToManyController@winnowing');
Route::post('/export1', 'ExportController@export1');
Route::post('/export2', 'ExportController@export2');
// Route::get('/manytomany/result', 'PagesController@manytomanyresult');

Route::get('/translate', 'PagesController@translate');
Route::post('/translate', 'TranslateController@translate');

Route::get('/onetomany/result', 'PagesController@oneToManyResult');
