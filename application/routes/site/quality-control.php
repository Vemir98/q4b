<?php
Route::set('site.quality.control','quality_control/<action>',['action' => 'create'])
    ->defaults([
        'controller' => 'QualityControl'
    ]);