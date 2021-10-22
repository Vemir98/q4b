<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 04.04.2017
 * Time: 11:50
 */

Route::set('site.entities','entities(/<action>(/<id>(/<id2>)))',['action' => '[a-z0-9_]+', 'id' => '[0-9]+', 'id2' => '[0-9]+'])
    ->defaults([
        'controller' => 'entities',
    ]);


