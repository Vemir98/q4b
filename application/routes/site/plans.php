<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:16
 */
Route::set('site.plansWithFilters','plans(/status/<status>)(/sorting/<sorting>)(/page/<page>)(/export/<export>)',['status' => implode('|',Enum_ProjectStatus::toArray()),'sorting' => 'name|status', 'page' => '[0-9]+','export' => 'excel'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'list'
    ]);
Route::set('site.plansObjectQualityControl','plans/property_item_quality_control_list/<id>(/status/<status>)',['status' => implode('|',Enum_QualityControlApproveStatus::toArray()),'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'property_item_quality_control_list'
    ]);
Route::set('site.plans.toggleNotifications','plans/toggle_notifications/<projectId>/<userId>',['projectId' => '[0-9]+','userId' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'toggle_notifications'
    ]);
//Route::set('site.project.plansList','plans/<project_id>/plans_list(/object/<object_id>)(/professions/<professions>)(/page/<page>)',
//    ['project_id' => '[0-9]+','professions' => '[0-9-]+','object_id' => '[0-9]+', 'page' => '[0-9]+'])
//    ->defaults([
//        'controller' => 'plans',
//        'action' => 'plans_list'
//    ]);
Route::set('site.plans.plansList','plans/<project_id>/plans_list(/object/<object_id>)(/professions/<professions>)(/floors/<floors>)(/with_file/<with_file>)(/page/<page>)',
    ['project_id' => '[0-9]+','professions' => '[0-9-]+','object_id' => '[0-9]+', 'floors' => '[0-9-_]+','with_file' => '[/-1-1]+','page' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'plans_list'
    ]);
Route::set('site.plans.trackingListSearch','plans/search_in_plan_list/<id>(/search/<search>)(/page/<page>)',['id' => '[0-9]+', 'search' => '[^=\/]+(=|==)?','page' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'plan_list_search',
        'page' => 1,
    ]);
Route::set('site.plans.trackingList2','plans/tracking_list/<id>(/filter/<filter>/profession/<profession>/from/<from>/to/<to>)(/search/<search>)(/page/<page>)',['search' => '[^=\/]+(=|==)?', 'profession' => '[0-9]+', 'filter' => 'created_at|received_date|departure_date', 'id' => '[0-9]+', 'from' => '[0-9]{4}-[0-9]{2}-[0-9]{2}','to' => '[0-9]{4}-[0-9]{2}-[0-9]{2}','page' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'tracking_list',
        'page' => 1,
    ]);
Route::set('site.plans.copy','plans/copy_plan/<project_id>',['project_id' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'copy_plan',
    ]);
Route::set('site.plans.projectObjects','plans/project_objects/<project_id>',['project_id' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'project_objects',
    ]);
Route::set('site.plans.createPlan','plans/create_plan/<id>(/object/<object_id>)(/profession/<profession_id>)',['id' => '[0-9]+', 'profession_id' => '[0-9]+', 'object_id' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'create_plan'
    ]);
Route::set('site.plans','plans(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
        'action' => 'list'
    ]);
Route::set('site.plans.plansWith2params','plans/<action>/<param1>/<param2>',['action' => '[a-z0-9_]+', 'param1' => '[0-9]+', 'param2' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
    ]);
Route::set('site.plans.plansWith3params','plans/<action>/<param1>/<param2>/<param3>',['action' => '[a-z0-9_]+', 'param1' => '[0-9]+', 'param2' => '[0-9]+', 'param3' => '[0-9]+'])
    ->defaults([
        'controller' => 'plans',
    ]);
Route::set('site.plans.project.files','plans/<action>/<param1>/<param2>/<token>',['action' => 'delete_plans_file|download_certification_file|delete_certification_file|delete_quality_control_file', 'param1' => '[0-9]+', 'param2' => '[0-9]+','token' => '[a-z0-9]+'])
    ->defaults([
        'controller' => 'plans',
    ]);
//Route::set('site.file','file/<action>/<param1>/<param2>/<token>',['action' => '[a-z0-9_]+', 'param1' => '[0-9]+', 'param2' => '[0-9]+','token' => '[a-z0-9]+'])
//    ->defaults([
//        'controller' => 'file',
//    ]);