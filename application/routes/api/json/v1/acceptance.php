<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV1.acceptance.add','api/json/v1(/<appToken>)/acceptance/add',['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'acceptance',
        'action' => 'add',
        'directory' => 'Api'
    ]);
Route::set('apiJsonV1.pre-acceptance.add','api/json/v1(/<appToken>)/pre_acceptance/add',['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'acceptance',
        'action' => 'pre_add',
        'directory' => 'Api'
    ]);
Route::set('apiJsonV1.public-acceptance.add','api/json/v1(/<appToken>)/public_acceptance/add',['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'acceptance',
        'action' => 'public_add',
        'directory' => 'Api'
    ]);
Route::set('apiJsonV1.acceptance.action-id','api/json/v1(/<appToken>)/acceptance/<action>(/<id>)',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'acceptance',
        'directory' => 'Api'
    ]);