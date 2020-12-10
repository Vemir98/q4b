<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('site.login','')
    ->defaults([
        'controller' => 'auth',
        'action' => 'login'
    ]);
Route::set('site.auth','(<action>(/<param1>))',['action' => 'logout|forgot_password|reset_password|accept_invitation|demo_login','param1' => '[a-z0-9][a-z0-9_-]+'])
    ->defaults([
        'controller' => 'auth',
    ]);