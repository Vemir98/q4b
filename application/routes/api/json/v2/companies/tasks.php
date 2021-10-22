<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV2.companies.tasks','api/json/v2(/<appToken>)/companies/<projectId>/tasks(/<id>)(/module/<moduleId>)(/craft/<craftId>)(/page/<page>)',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+', 'projectId' => '[0-9]+', 'moduleId' => '[0-9]+','craftId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(empty($params['projectId']) OR strtolower($request->method()) !== 'get') return false;
        return $params;
    })
    ->defaults([
        'controller' => 'Tasks',
        'directory' => 'Api/Projects',
        'action' => 'tasks_get'
    ]);
Route::set('apiJsonV2.companies.default','api/json/v2(/<appToken>)/projects/<projectId>/tasks(/<id>)',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+','projectId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(empty($params['projectId'])) return false;
        $params['action'] = 'tasks_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Tasks',
        'directory' => 'Api/Projects',
    ]);