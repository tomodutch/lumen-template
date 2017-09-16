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

$router->get('/articles', 'ArticleController@getMany');
$router->get('/articles/{id}', 'ArticleController@getOne');
$router->put('/articles/{id}', 'ArticleController@update');
$router->delete('/articles/{id}', 'ArticleController@delete');
$router->post('/articles', 'ArticleController@create');

$router->get('/thomass', 'ThomasController@index');
$router->get('/thomass/{id}', 'ThomasController@show');
$router->put('/thomass/{id}', 'ThomasController@update');
$router->delete('/thomass/{id}', 'ThomasController@destroy');
$router->post('/thomass', 'ThomasController@store');