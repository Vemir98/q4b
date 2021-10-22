<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:09
 */
Route::set('site.fileserver','fileserver/<action>',['action' => 'callback|test|planaddcallback|callbackimage|callbackplantrackingfile|certificationscallback|instructionscallback|qcimages|planImages'])
    ->defaults([
        'controller' => 'fileServer'
    ]);