<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function ($router) {
    $router->post('/auth/register', 'Auth\AuthController@register');
    $router->post('/auth/login', 'Auth\AuthController@login');

    $router->group(['middleware' => 'auth'], function ($router) {
        $router->get('/division', 'Division\DivisionController@index');

        $router->get('/user', 'User\UserController@index');
        $router->get('/user/{id}', 'User\UserController@show');
        $router->post('/user', 'User\UserController@store');
        $router->patch('/user/{id}', 'User\UserController@update');
        $router->delete('/user/{id}', 'User\UserController@destroy');
    });
});