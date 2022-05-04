<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */
Route::set('apiJsonV2.el-approvals.list','api/json/v2/(<appToken>/)el-approvals/list(/page/<page>)',['appToken' => '[a-f0-9]{32}','page' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        if(strtolower($request->method()) !== 'post') return false;
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects',
        'action' => 'list_post'
    ]);

Route::set('apiJsonV2.el-approvals.notifications','api/json/v2/(<appToken>/)el-approvals/<id>/notifications',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'notifications_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.speciality','api/json/v2/(<appToken>/)el-approvals/<elAppId>/specialities/<craftId>',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'speciality_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.add-signature','api/json/v2/(<appToken>/)el-approvals/<elAppId>/add_signature(/<craftId>)',['appToken' => '[a-f0-9]{32}', 'elAppId' => '[0-9]+', 'craftId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'add_signature_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.positions','api/json/v2/(<appToken>/)el-approvals/positions/<id>',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'positions_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.change-status','api/json/v2/(<appToken>/)el-approvals/<id>/status',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'status_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.partial-process','api/json/v2/(<appToken>/)el-approvals/<id>/partial-process',['appToken' => '[a-f0-9]{32}', 'id' => '[0-9]+',])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'partial_process_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.change-note','api/json/v2/(<appToken>/)el-approvals/<id>/note',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'note_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.delete','api/json/v2/(<appToken>/)el-approvals/<id>/delete',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'remove_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.project.el-approval','api/json/v2/(<appToken>/)projects/<projectId>/el-approvals/<elApprovalId>',['appToken' => '[a-f0-9]{32}', 'elApprovalId' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'project_index_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.export_xls','api/json/v2/(<appToken>/)projects/<projectId>/el-approvals/export_xls',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'export_xls_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.export_pdf','api/json/v2/(<appToken>/)projects/<projectId>/el-approvals/export_pdf',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'export_pdf_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);

Route::set('apiJsonV2.el-approvals.list.user','api/json/v2/(<appToken>/)el-approvals/list/user',['appToken' => '[a-f0-9]{32}'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'list_user_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);


Route::set('apiJsonV2.el-approvals.default','api/json/v2/(<appToken>/)el-approvals(/<id>)',['appToken' => '[a-f0-9]{32}','id' => '[0-9]+'])
    ->filter(function($route, $params, $request)
    {
        $params['action'] = 'index_'.strtolower($request->method());
        return $params;
    })
    ->defaults([
        'controller' => 'ElApprovals',
        'directory' => 'Api/Projects'
    ]);