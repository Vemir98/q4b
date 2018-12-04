<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 14.06.2017
 * Time: 19:55
 */
Route::set('site.security','<key>',['key' => 'wp-admin|admin|administrator|manager|manage|wp-login.php|admin.php|user|login'])
    ->defaults([
        'controller' => 'security',
        'action' => 'block'
    ]);

Route::set('site.securityRnd','<int><str><int1>aD<key>',['key' => '[0-9A-z]+','int' => '[0-9]','str' => '[A-z]{2}','int1' => '[0-9]{2}'])
    ->defaults([
        'controller' => 'security',
        'action' => 'block'
    ]);