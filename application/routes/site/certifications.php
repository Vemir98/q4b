<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 10.03.2020
 * Time: 11:12
 */
Route::set('site.certifications','certifications(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'certifications',
        'action' => 'index'
    ]);