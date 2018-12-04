<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV1.companies.default','api/json/v1/<appToken>/companies(/page/<page>)',['appToken' => '[a-f0-9]{32}','page' => '[0-9]+'])
    ->defaults([
        'controller' => 'companies',
        'action' => 'list',
        'directory' => 'Api'
    ]);
Route::set('apiJsonV1.companies.action-id','api/json/v1/<appToken>/companies/<action>(/<id>)',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'companies',
        'directory' => 'Api'
    ]);