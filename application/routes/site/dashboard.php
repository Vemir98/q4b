<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 14:33
 */
Route::set('site.dashboard.qualityControlList','dashboard/quality_control_list/project/<project>/objects/<objects>(/status/<status>)(/page/<page>)',
['project' => '[0-9]+','objects' => '[0-9-]+','status' => implode('|',Enum_QualityControlApproveStatus::toArray()), 'page' => '[0-9]+'])
    ->defaults([
        'controller' => 'dashboard',
        'action' => 'quality_control_list'
    ]);
Route::set('site.dashboard.plansList','dashboard/plans_list/project/<project>/objects/<objects>(/status/<status>)(/page/<page>)',
['project' => '[0-9]+','objects' => '[0-9-]+','status' => implode('|',Enum_QualityControlApproveStatus::toArray()), 'page' => '[0-9]+'])
    ->defaults([
        'controller' => 'dashboard',
        'action' => 'plans_list'
    ]);
Route::set('site.dashboard.certificationsList','dashboard/certifications_list/project/<project>(/status/<status>)(/page/<page>)',
    ['project' => '[0-9]+','status' => implode('|',Enum_QualityControlApproveStatus::toArray()), 'page' => '[0-9]+'])
    ->defaults([
        'controller' => 'dashboard',
        'action' => 'certifications_list'
    ]);
Route::set('site.dashboard','dashboard(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'dashboard',
        'action' => 'index'
    ]);

Route::set('site.dashboard.export_pdf','dashboard/export_pdf',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'dashboard',
        'action' => 'export_pdf'
    ]);

Route::set('site.dashboard.print','dashboard/print',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'dashboard',
        'action' => 'print'
    ]);
