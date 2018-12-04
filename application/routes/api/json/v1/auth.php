<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV1.login','api/json/v1/login')
    ->defaults([
        'controller' => 'auth',
        'action' => 'login',
        'directory' => 'Api'
    ]);