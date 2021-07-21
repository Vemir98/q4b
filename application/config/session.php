<?php defined('SYSPATH') OR die('No direct script access.');

return array(
    'native' => array(
        'name' => 'session_name',
        'lifetime' => 1209600,
    ),
    'cookie' => array(
        'name' => 's',
        'encrypted' => false,
        'lifetime' => 1209600,
    ),
    'database' => array(
        'name' => 's',
        'encrypted' => false,
        'lifetime' => 1209600,
        'group' => 'default',
        'table' => 'sessions',
        'columns' => array(
            'session_id'  => 'session_id',
            'last_active' => 'last_active',
            'contents'    => 'contents'
        ),
        'gc' => 500,
    ),
);