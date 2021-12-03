<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */

Route::set('apiJsonV2.info-center-project-messages-actions','api/json/v2/(<appToken>/)projects/<projectId>/messages(/<action>)',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'project_messages_'.$params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'InfoCenter',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.info-center-projects-message-actions','api/json/v2/(<appToken>/)projects/messages(/<action>)',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'projects_message_'.$params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'InfoCenter',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.info-center-projects-message-actions-dwada','api/json/v2/(<appToken>/)projects/message(/<messageId>)',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'projects_message_'.$params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'InfoCenter',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.info-center-projects-message-actions-id','api/json/v2/(<appToken>/)projects/messages/<messageId>/<action>',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'projects_message_'.$params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'InfoCenter',
        'directory' => 'Api/Projects'
    ]);



Route::set('apiJsonV2.info-center-projects-message-history-actions','api/json/v2/(<appToken>/)projects/messages/histories/<historyId>/<action>',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'projects_message_history_'.$params['action'].'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'InfoCenter',
        'directory' => 'Api/Projects'
    ]);