<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 28.03.2022
 * Time: 6:00
 */

Route::set('apiJsonV2.project-certificates-actions','api/json/v2/(<appToken>/)projects/<projectId>/certificates',['appToken' => '[a-f0-9]{32}', 'projectId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'project_certificates'.'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Certificates',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.project-certificate-projectId-actions','api/json/v2/(<appToken>/)projects/<projectId>/certificate',['appToken' => '[a-f0-9]{32}', 'projectId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(strtolower($request->method()) !== 'post') return false;
        $params['action'] = 'project_certificate'.'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Certificates',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.project-certificates-copy-actions','api/json/v2/(<appToken>/)companies/<companyId>/projects/<projectId>/certificates/copy',['appToken' => '[a-f0-9]{32}', 'projectId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'project_certificates_copy'.'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Certificates',
        'directory' => 'Api/Projects'
    ]);


Route::set('apiJsonV2.project-certificate-actions','api/json/v2/(<appToken>/)projects/certificate/<certificateId>',['appToken' => '[a-f0-9]{32}', 'certificateId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'project_certificate'.'_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'Certificates',
        'directory' => 'Api/Projects'
    ]);