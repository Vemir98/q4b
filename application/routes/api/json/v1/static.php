<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV1.staticTranslation','api/json/v1/<appToken>/static/translation',['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'static',
        'action' => 'translation',
        'directory' => 'Api'
    ]);
Route::set('apiJsonV1.static','api/json/v1/<appToken>/static/not-sensitive',['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'static',
        'action' => 'notSensitive',
        'directory' => 'Api'
    ]);