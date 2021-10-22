<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV2.projects.entities.object-floors','api/json/v2(/<appToken>)/projects/entities/objects/<id>/floors',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(empty($params['id'])) return false;
        $params['action'] = 'floors_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Projects',
    ]);
Route::set('apiJsonV2.projects.entities.floor-places','api/json/v2(/<appToken>)/projects/entities/floors/<id>/places',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(empty($params['id'])) return false;
        $params['action'] = 'places_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Projects',
    ]);

Route::set('apiJsonV2.entities.labtests_all_projects_list','api/json/v2(/<appToken>)/projects/entities/labtests_all_projects_list')
    ->filter(function($route, $params, $request)
    {
        if(strtolower($request->method()) !== 'get') return false;
        return $params;
    })
    ->defaults([
        'controller' => 'entities',
        'directory' => 'Api/Projects',
        'action' => 'labtests_all_projects_list_get'
    ]);

Route::set('apiJsonV2.entities.labtests_projects','api/json/v2(/<appToken>)/projects/entities/labtests_projects(/page(/<page>))')
    ->filter(function($route, $params, $request)
    {
        if(strtolower($request->method()) !== 'get') return false;
        return $params;
    })
    ->defaults([
        'controller' => 'entities',
        'directory' => 'Api/Projects',
        'action' => 'labtests_projects_get'
    ]);

Route::set('apiJsonV2.projects.entities.default','api/json/v2(/<appToken>)/projects(/<projectId>)/entities/<action>(/<id>)(/page(/<page>))',['appToken' => '[a-f0-9]{32}','projectId' => '[0-9]+','action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(empty($params['action'])) return false;
        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Projects',
    ]);

//Route::set('apiJsonV2.projects.entities.default','api/json/v2(/<appToken>)/projects(/<projectId>)/entities/<action>(/<id>)(/page(/<page>))',['appToken' => '[a-f0-9]{32}','projectId' => '[0-9]+','action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
//    ->filter(function($route, $params, $request)
//    {
//        if(empty($params['action'])) return false;
//        $params['action'] = $params['action'].'_'.strtolower($request->method());
//        return $params;
//    })
//    ->defaults([
//        'controller' => 'Entities',
//        'directory' => 'Api/Projects',
//    ]);