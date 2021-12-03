<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 17.11.2021
 * Time: 11:20
 */
Route::set('site.info-center','info-center(/<action>)',['action' => '[a-z0-9_]+'])
    ->defaults([
        'controller' => 'InfoCenter',
    ]);