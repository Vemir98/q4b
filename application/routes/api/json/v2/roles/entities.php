<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 26.08.2021
 * Time: 16:56
 */
Route::set('apiJsonV2.roles.entities.subcontractors.crafts','api/json/v2(/<appToken>)/roles/subcontractors/crafts',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'subcontractors_crafts_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Roles',
    ]);

