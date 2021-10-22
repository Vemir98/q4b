<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 06.09.2021
 * Time: 16:00
 */
Route::set('apiJsonV2.quality-control','api/json/v2/(<appToken>/)quality-controls/<action>/<qcId>',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+','qcId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/QualityControl'
    ]);