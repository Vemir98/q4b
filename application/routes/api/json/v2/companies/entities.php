<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV2.companies.entities.default','api/json/v2(/<appToken>)/companies(/<companyId>)/entities/<action>',['appToken' => '[a-f0-9]{32}','companyId' => '[0-9]+','action' => '[a-z0-9_]+'])
    ->filter(function($route, $params, $request)
    {
        if(empty($params['action'])) return false;
        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Companies',
    ]);