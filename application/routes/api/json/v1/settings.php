<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV1.acceptance.add','api/json/v1(/<appToken>)/settings/spaces_types',['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'settings',
        'action' => 'spaces_types',
        'directory' => 'Api'
    ]);