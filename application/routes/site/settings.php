<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 30.06.2017
 * Time: 12:39
 */
Route::set('site.settings','settings(/<action>(/<id>))')
    ->defaults([
        'controller' => 'settings',
        'action' => 'index'
    ]);