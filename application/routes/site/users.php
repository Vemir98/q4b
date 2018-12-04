<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:09
 */
Route::set('site.user','user/<action>',['action' => 'profile|agree_terms'])
    ->defaults([
        'controller' => 'user'
    ]);
//Route::set('site.users','users(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
//    ->defaults([
//        'controller' => 'users',
//        'action' => 'list'
//    ]);