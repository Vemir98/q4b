<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV2.projects.dashboard.statistics','api/json/v2/(<appToken>/)projects/statistics/<action>',['appToken' => '[a-f0-9]{32}', 'action' => '[a-z0-9_]+'])
    ->filter(function($route, $params, $request)
    {
        if(empty($params['action'])) return false;
        $params['action'] = 'statistics_'.$params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Dashboard',
        'directory' => 'Api/Projects'
    ]);