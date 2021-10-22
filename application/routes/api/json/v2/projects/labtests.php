<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV2.projects.labtests.default','api/json/v2(/<appToken>)/projects/<projectId>/labtests(/<id>)(/page/<page>)',['appToken' => '[a-f0-9]{32}','projectId' => '[0-9]+','page' => '[0-9]+','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(!empty($params['id']) AND !empty($params['page'])) return false;
        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'labtests',
        'directory' => 'Api/Projects',
        'action' => 'index'
    ]);
Route::set('apiJsonV2.projects.labtests.entities','api/json/v2(/<appToken>)/projects/labtests/<action>(/<id>)',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(empty($params['action'])) return false;
        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'labtests',
        'directory' => 'Api/Projects',
    ]);
Route::set('apiJsonV2.projects.labtests.id-action','api/json/v2(/<appToken>)/projects/<projectId>/labtests/<id>/<action>(/<ticketId>)',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+','id' => '[0-9]+','projectId' => '[0-9]+', 'labtestId' => '[0-9]+', 'ticketId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(in_array($request->method(),['PUT','DELETE']) AND empty($params['id'])) return false;

        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'labtests',
        'directory' => 'Api/Projects'
    ]);
Route::set('apiJsonV2.projects.labtests.action-id','api/json/v2(/<appToken>)/projects/<projectId>/labtests/<action>(/<id>)',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+','id' => '[0-9]+','projectId' => '[0-9]+','labtestId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(in_array($request->method(),['PUT','DELETE']) AND empty($params['id'])) return false;

        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'labtests',
        'directory' => 'Api/Projects'
    ]);
Route::set('apiJsonV2.projects.labtests.action-id','api/json/v2(/<appToken>)/projects/<projectId>/labtests/<action>(/<id>)',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+','id' => '[0-9]+','projectId' => '[0-9]+','labtestId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(in_array($request->method(),['PUT','DELETE']) AND empty($params['id'])) return false;

        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'labtests',
        'directory' => 'Api/Projects'
    ]);
Route::set('apiJsonV2.projects.labtests.action-effect','api/json/v2(/<appToken>)/projects/<projectId>/labtests/<action>/<effect>',['appToken' => '[a-f0-9]{32}','action' => '[a-z0-9_]+','effect' => '[a-z0-9_]+','projectId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = $params['action'].'_'.$params['effect'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'labtests',
        'directory' => 'Api/Projects'
    ]);
