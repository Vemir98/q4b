<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:16
 */
Route::set('site.quality_control','quality_control(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'QControls',
        'action' => 'create'
    ]);