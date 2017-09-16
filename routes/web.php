<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/items', 'ItemController@index');
$router->get('/items/{id}', 'ItemController@show');
$router->put('/items/{id}', 'ItemController@update');
$router->delete('/items/{id}', 'ItemController@destroy');
$router->post('/items', 'ItemController@store');

$router->get('/persons', 'PersonController@index');
$router->get('/persons/{id}', 'PersonController@show');
$router->put('/persons/{id}', 'PersonController@update');
$router->delete('/persons/{id}', 'PersonController@destroy');
$router->post('/persons', 'PersonController@store');


$router->get('/posts', 'PostController@index');
$router->get('/posts/{id}', 'PostController@show');
$router->put('/posts/{id}', 'PostController@update');
$router->delete('/posts/{id}', 'PostController@destroy');
$router->post('/posts', 'PostController@store');


$router->get('/items', 'ItemController@index');
$router->get('/items/{id}', 'ItemController@show');
$router->put('/items/{id}', 'ItemController@update');
$router->delete('/items/{id}', 'ItemController@destroy');
$router->post('/items', 'ItemController@store');

