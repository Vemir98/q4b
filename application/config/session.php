<?php defined('SYSPATH') OR die('No direct script access.');

return array(
    'native' => array(
        'name' => 'session_name',
        'lifetime' => 43200,
    ),
    'cookie' => array(
        'name' => 's',
        'encrypted' => TRUE,
        'lifetime' => 43200,
    ),
);