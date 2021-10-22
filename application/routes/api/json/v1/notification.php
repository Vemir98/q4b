<?php

Route::set('apiJsonV1.notificationSend','api/json/v1/notify')
    ->defaults([
        'controller' => 'notification',
        'action' => 'send',
        'directory' => 'Api'
    ]);