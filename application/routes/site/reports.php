<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.04.2017
 * Time: 11:50
 */
//delivery reports
Route::set('site.delivery.reports','reports/delivery(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'DeliveryReports',
    ]);


//quality reports
Route::set('site.quality.reports','reports/quality(/<action>)',['action' => '[a-z0-9_]+'])
    ->defaults([
        'controller' => 'QualityReports',
    ]);
Route::set('site.quality.reports2','reports/quality/send_reports/<id>',['action' => '[a-z0-9_]+', 'id' => '[p0-9-]+'])
    ->defaults([
        'controller' => 'QualityReports',
        'action' => 'send_reports'
    ]);
Route::set('site.quality.reports1','reports/quality(/<action>/<id>)',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'QualityReports',
        'action' => 'index'
    ]);


//reports
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


//tasks reports
Route::set('site.tasks.reports4','reports/tasks/property_item_quality_control_list/<id>/<craft_id>(/status/<status>)',['id' => '[0-9]+','craft_id' => '[0-9]+','status' => implode('|',Enum_QualityControlApproveStatus::toArray())])
    ->defaults([
        'controller' => 'TasksReports',
        'action' => 'property_item_quality_control_list'
    ]);
Route::set('site.tasks.reports2','reports/tasks/details/<type>/<id>/<craft_id>(/<placeType>)',['type' => 'project|object|floor|place','id' => '[0-9]+','craft_id' => '[0-9]+','placeType' => 'public|private'])
    ->defaults([
        'controller' => 'TasksReports',
        'action' => 'details'
    ]);
Route::set('site.tasks.reports3','reports/tasks/places/<type>/<id>/<craft_id>(/<placeType>)',['type' => 'project|object|floor','id' => '[0-9]+','craft_id' => '[0-9]+','placeType' => 'public|private'])
    ->defaults([
        'controller' => 'TasksReports',
        'action' => 'places'
    ]);
Route::set('site.tasks.reports','reports/tasks(/<action>)',['action' => '[a-z0-9_]+'])
    ->defaults([
        'controller' => 'TasksReports',
    ]);
Route::set('site.tasks.reports1','reports/tasks(/<action>/<id>)',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'TasksReports',
        'action' => 'index'
    ]);

Route::set('site.place.reports2','reports/place/qc_list(/status/<status>)/<qc_status>/<qcStatus>/<id>/<crafts>',['qcStatus' => 'all|'.implode('|',Enum_QualityControlStatus::toArray()),'status' => implode('|',Enum_QualityControlApproveStatus::toArray()),'crafts' => '[0-9,]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'PlaceReports',
        'action' => 'qc_list'
    ]);
Route::set('site.place.reports1','reports/place(/<action>/<id>)',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'PlaceReports',
        'action' => 'index'
    ]);

Route::set('site.place.reports','reports/place(/<action>)',['action' => '[a-z0-9_]+'])
    ->defaults([
        'controller' => 'PlaceReports',
    ]);

Route::set('site.reports','reports(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'reports',
        'action' => 'index'
    ]);

