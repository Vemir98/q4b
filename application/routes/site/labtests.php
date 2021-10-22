<?php
//Route::set('site.quality.control','quality_control/<action>',['action' => 'create'])
//    ->defaults([
//        'controller' => 'QualityControl'
//    ]);
//Route::set('site.quality.control.with.2.params','quality_control/<action>/<param1>/<param2>',['action' => 'get_places_for_floor|get_plans', 'param1' => '[0-9]+', 'param2' => '[0-9-1]+'])
//    ->defaults([
//        'controller' => 'QualityControl'
//    ]);


Route::set('site.labtests','labtests(/<action>(/<projectId>))',['action' => '[a-z0-9_]+', 'projectId' => '[0-9]+'])
    ->defaults([
        'controller' => 'LabTests',
    ]);
Route::set('site.labtests.action-id','labtests/project/<projectId>/<action>(/<id>)',['action' => '[a-z0-9_]+','id' => '[0-9]+','projectId' => '[0-9]+'])
    ->defaults([
        'controller' => 'LabTests',
    ]);