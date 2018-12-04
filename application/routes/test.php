<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 06.09.2016
 * Time: 17:08
 * Роуты лицевой части сайта
 */

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */





Route::set('test','test(/<action>(/<id>))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'welcome',
        'action' => 'index'
    ]);
//Route::set('default', '(<controller>(/<action>(/<id>)))')
//    ->defaults(array(
//        'controller' => 'welcome',
//        'action'     => 'index',
//    ));
