<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 10.03.2020
 * Time: 11:12
 */
Route::set('site.acceptance','acceptance(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'acceptance',
        'action' => 'list'
    ]);