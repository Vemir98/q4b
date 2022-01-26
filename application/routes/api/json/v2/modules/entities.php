<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */

Route::set('apiJsonV2.modules','api/json/v2/(<appToken>/)modules',['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Modules',
        'action' => 'modules_get'
    ]);