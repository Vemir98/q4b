<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV1.certifications.action-id','api/json/v1(/<appToken>)/certifications/<action>(/<id>(/<id2>))',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+', 'id' => '[0-9]+','id2' => '[0-9]+'])
    ->defaults([
        'controller' => 'certifications',
        'directory' => 'Api'
    ]);