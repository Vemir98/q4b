<?php

Route::set('site.project.tasks','tasks/projects/update/<projectId>(/<action>)',['action' => '[a-z0-9_]+', 'projectId' => '[0-9]+'])
    ->defaults([
        'controller' => 'Tasks',
    ]);