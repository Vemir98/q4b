<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */

Route::set('apiJsonV2.project-chapter-actions','api/json/v2/(<appToken>/)projects/<projectId>/chapters',['appToken' => '[a-f0-9]{32}', 'projectId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'project_chapters'.'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Chapters',
        'directory' => 'Api/Projects'
    ]);