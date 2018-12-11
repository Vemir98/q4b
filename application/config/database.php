<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'alternate' => array
	(
		'type'       => 'MySQL',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname     server hostname, or socket
			 * string   database     database name
			 * string   username     database username
			 * string   password     database password
			 * boolean  persistent   use persistent connections?
			 * array    variables    system variables as "key => value" pairs
			 *
			 * Ports and sockets may be appended to the hostname.
			 */
			'hostname'   => 'localhost',
			'database'   => 'kohana',
			'username'   => FALSE,
			'password'   => FALSE,
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
	),
	'default1' => array(
		'type'       => 'PDO',
		'connection' => array(
			/**
			 * The following options are available for PDO:
			 *
			 * string   dsn         Data Source Name
			 * string   username    database username
			 * string   password    database password
			 * boolean  persistent  use persistent connections?
			 */
			'dsn'        => 'mysql:host=localhost;dbname=db_constructmngr',
			'username'   => 'root',
			'password'   => FALSE,
			'persistent' => FALSE,
		),
		/**
		 * The following extra options are available for PDO:
		 *
		 * string   identifier  set the escaping identifier
		 */
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
	),
	/**
	 * MySQLi driver config information
	 *
	 * The following options are available for MySQLi:
	 *
	 * string   hostname     server hostname, or socket
	 * string   database     database name
	 * string   username     database username
	 * string   password     database password
	 * boolean  persistent   use persistent connections?
	 * array    ssl          ssl parameters as "key => value" pairs.
	 *                       Available keys: client_key_path, client_cert_path, ca_cert_path, ca_dir_path, cipher
	 * array    variables    system variables as "key => value" pairs
	 *
	 * Ports and sockets may be appended to the hostname.
	 *
	 * MySQLi driver config example:
	 *
	 */
 	'default' => array
 	(
 		'type'       => 'MySQLi',
 		'connection' => array(
 			'hostname'   => 'localhost',
 			'database'   => 'horizor0_q4u',
 			'username'   => 'root',
 			'password'   => '',
 			'persistent' => FALSE,
 			'ssl'        => NULL,
 		),
 		'table_prefix' => '',
 		'charset'      => 'utf8',
 		'caching'      => FALSE,
 	),
	'persistent' => array
	(
        'type'       => 'MySQLi',
        'connection' => array(
            'hostname'   => 'localhost',
            'database'   => 'horizor0_q4u',
            'username'   => 'horizor0_q4u',
            'password'   => 'p@ssword1',
            'persistent' => TRUE,
            'ssl'        => NULL,
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
        'caching'      => FALSE,
	),
);
