<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.09.2017
 * Time: 12:15
 */
Route::set('site.consultantsWithFilters','consultants(/company/<company>)(/project/<project>)(/page/<page>)',['company' => '[0-9]+','project' => '[0-9]+', 'page' => '[0-9]+'])
    ->defaults([
        'controller' => 'consultants',
        'action' => 'list'
    ]);
Route::set('site.consultants','consultants/<action>(/<id>)',['action' => '[a-z0-9_]+', 'id' => '[0-9]+'])
    ->defaults([
        'controller' => 'consultants'
    ]);