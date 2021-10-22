<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/13/21
 * Time: 12:12 PM
 */

Route::set('apiJsonV1.devices','api/json/v1(/<appToken>)/devices/register', ['appToken' => '[a-f0-9]{32}'])
    ->defaults([
        'controller' => 'devices',
        'action' => 'register',
        'directory' => 'Api'
    ]);