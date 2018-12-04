<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 06.09.2016
 * Time: 17:08
 * Роуты лицевой части сайта
 */
Route::set('docs.api','development/docs/api/json/v1')
    ->defaults([
        'controller' => 'welcome',
        'action' => 'apidoc'
    ]);
