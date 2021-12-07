<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 06.12.2021
 * Time: 14:00
 */
Route::set('apiJsonV2.user.preferences.action','api/json/v2(/<appToken>)/user/<userId>/preferences/<action>(/<type>)',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = $params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Preferences',
        'directory' => 'Api/Users',
    ]);

