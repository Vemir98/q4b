<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.06.2017
 * Time: 12:39
 */
Route::set('site.projects_manager','projects_manager(/<action>(/<id>))')
    ->defaults([
        'controller' => 'ProjectsManager',
        'action' => 'index'
    ]);