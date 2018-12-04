<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Инициализация Модуля ACL
 */
//Если на стадии разработки то разрешаем обновление информации о ресурсах
if(Kohana::$environment == Kohana::DEVELOPMENT){

    Route::set('acl.resources-manager', 'acl-resmanager(/<action>(/<id>))')
        ->defaults(array(
            'controller' => 'ResManager',
            'action'     => 'index',
            'directory'  => 'ACL'
        ));

}