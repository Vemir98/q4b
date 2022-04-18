<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:00
 */

Route::set('apiJsonV2.guides','api/json/v2/(<appToken>/)guides/<guideType>',['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'Entities',
        'directory' => 'Api/Guides',
        'action' => 'guides_get'
    ]);