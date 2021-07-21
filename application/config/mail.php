<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 16.06.2017
 * Time: 14:16
 */
return [
    'debug_mode' => true,

    // SMTP object: ``$mail = new SMTP('my_connection')``.

    'default' => 'primary',
    'connections' => [
        'primary' => [
            'secure' => 'tls', // null, 'ssl', or 'tls'
            'auth' => true, // true if authorization required
            'host'      => 'sunrisedvp.systems',
            'port'      => 587,
            'user'  => 'starphone@sunrisedvp.systems',
            'pass'  => 'Ipr59m@2',
        ],
    ],

    // http://stackoverflow.com/questions/5294478/significance-of-localhost-in-helo-localhost

    'localhost' => 'info@qforb.net',

];