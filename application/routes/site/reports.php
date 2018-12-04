<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.04.2017
 * Time: 11:50
 */
Route::set('site.reportsAdvanced','reports/advanced_filter')
    ->defaults([
        'controller' => 'reports',
        'action' => 'advanced_options'
    ]);
Route::set('site.reportsGuestAccess','reports/guest_access/<token>',['token' => '[a-z0-9]+'])
    ->defaults([
        'controller' => 'reports',
        'action' => 'guest_access'
    ]);
Route::set('site.sendReportsEmail','reports/send_reports/<id>/<token>',['id' => '[0-9]+','token' => '[a-z0-9]+'])
    ->defaults([
        'controller' => 'reports',
        'action' => 'send_reports'
    ]);
Route::set('site.reportsNumberFilter','reports/get_spaces/<type>/<num_type>/<number>/<properties>',['num_type' => 'pn|pcn','type' => implode('|',Enum_ProjectPlaceType::toArray()),'properties' => '[0-9-]+', 'id' => '[0-9-]+'])
    ->defaults([
        'controller' => 'reports',
        'action' => 'get_spaces'
    ]);
Route::set('site.reports.tasks','reports/tasks/<projectId>(/<objectId>(/<floorId>(/<placeId>)))',['projectId' => '[0-9]+','objectId' => '[0-9]+','floorId' => '[0-9]+','placeId' => '[0-9]+'])
    ->defaults([
        'controller' => 'reports',
        'action' => 'tasks'
    ]);
Route::set('site.reports','reports(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'reports',
        'action' => 'index'
    ]);