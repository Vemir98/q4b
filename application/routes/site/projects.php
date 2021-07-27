<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:16
 */
Route::set('site.projectTasks','projects/update/<id>/<action>',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
        'action' => 'tasks_list'
    ]);
Route::set('site.projectsWithFilters','projects(/status/<status>)(/sorting/<sorting>)(/page/<page>)(/export/<export>)',['status' => implode('|',Enum_ProjectStatus::toArray()),'sorting' => 'name|status', 'page' => '[0-9]+','export' => 'excel'])
    ->defaults([
        'controller' => 'projects',
        'action' => 'list'
    ]);
Route::set('site.projectsObjectQualityControl','projects/property_item_quality_control_list/<id>(/status/<status>)',['status' => implode('|',Enum_QualityControlApproveStatus::toArray()),'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
        'action' => 'property_item_quality_control_list'
    ]);
Route::set('site.toggleNotifications','projects/toggle_notifications/<projectId>/<userId>',['projectId' => '[0-9]+','userId' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
        'action' => 'toggle_notifications'
    ]);
//Route::set('site.project.plansList','projects/<project_id>/plans_list(/object/<object_id>)(/professions/<professions>)(/page/<page>)',
//    ['project_id' => '[0-9]+','professions' => '[0-9-]+','object_id' => '[0-9]+', 'page' => '[0-9]+'])
//    ->defaults([
//        'controller' => 'projects',
//        'action' => 'plans_list'
//    ]);
Route::set('site.project.plansList','projects/<project_id>/plans_list(/object/<object_id>)(/professions/<professions>)(/floors/<floors>)(/page/<page>)',
    ['project_id' => '[0-9]+','professions' => '[0-9-]+','object_id' => '[0-9]+', 'floors' => '[0-9-_]+','page' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
        'action' => 'plans_list'
    ]);
Route::set('site.project.trackingListSearch','projects/search_in_plan_list/<id>(/search/<search>)(/page/<page>)',['id' => '[0-9]+', 'search' => '[^=\/]+(=|==)?','page' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
        'action' => 'plan_list_search',
        'page' => 1,
    ]);
Route::set('site.project.trackingList2','projects/tracking_list/<id>(/filter/<filter>/profession/<profession>/from/<from>/to/<to>)(/search/<search>)(/page/<page>)',['search' => '[^=\/]+(=|==)?', 'profession' => '[0-9]+', 'filter' => 'created_at|received_date|departure_date', 'id' => '[0-9]+', 'from' => '[0-9]{4}-[0-9]{2}-[0-9]{2}','to' => '[0-9]{4}-[0-9]{2}-[0-9]{2}','page' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
        'action' => 'tracking_list',
        'page' => 1,
    ]);
Route::set('site.projects','projects(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
        'action' => 'list'
    ]);
Route::set('site.projectsWith2params','projects/<action>/<param1>/<param2>',['action' => '[a-z0-9_]+', 'param1' => '[0-9]+', 'param2' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
    ]);
Route::set('site.projectsWith3params','projects/<action>/<param1>/<param2>/<param3>',['action' => '[a-z0-9_]+', 'param1' => '[0-9]+', 'param2' => '[0-9]+', 'param3' => '[0-9]+'])
    ->defaults([
        'controller' => 'projects',
    ]);
Route::set('site.project.files','projects/<action>/<param1>/<param2>/<token>',['action' => 'delete_plans_file|download_certification_file|delete_certification_file|delete_quality_control_file', 'param1' => '[0-9]+', 'param2' => '[0-9]+','token' => '[a-z0-9]+'])
    ->defaults([
        'controller' => 'projects',
    ]);

//Route::set('site.file','file/<action>/<param1>/<param2>/<token>',['action' => '[a-z0-9_]+', 'param1' => '[0-9]+', 'param2' => '[0-9]+','token' => '[a-z0-9]+'])
//    ->defaults([
//        'controller' => 'file',
//    ]);