<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 26.08.2021
 * Time: 16:56
 */
Route::set('apiJsonV2.users.entities.list','api/json/v2(/<appToken>)/users/list',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'list_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Users',
    ]);

Route::set('apiJsonV2.company.users.entities.list','api/json/v2(/<appToken>)/users/company/<id>',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'company_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Users',
    ]);

Route::set('apiJsonV2.project.users.entities.list','api/json/v2(/<appToken>)/users/project/<id>',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'project_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Users',
    ]);

Route::set('apiJsonV2.role.users.entities.list','api/json/v2(/<appToken>)/users/role/<id>',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'role_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Users',
    ]);

