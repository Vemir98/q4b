<?php defined('SYSPATH') or die('No direct script access.');Route::set('imagefly', 'images/<params>/<imagepath>', array('imagepath' => '.*'))    ->defaults(array(        'controller' => 'images',    ));    