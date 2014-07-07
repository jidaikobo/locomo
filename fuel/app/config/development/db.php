<?php
/**
 * The development database settings. These get merged with the global settings.
 */

return array(
	'default' => array(
		'type' => 'mysql',
		'connection' => array(
			'hostname' => 'localhost',
//			'dsn' => 'mysql:host=localhost;dbname=fuel',
//			'hostname' => '127.0.0.1',
//			'socket'   => '/opt/local/var/run/mysql5/mysqld.sock',
//			'port' => '3306',
			'database' => 'fuel',
			'username' => 'root',
			'password' => '121212',
//			'charset'      => 'utf8',
			'enable_cache' => true,
		),
		'profiling' => true,
	),
);

//php /Users/jidaikobo/Sites/8083/html/oil refine migrate
//sudo vi /opt/local/etc/php55/php.ini
//php -r "echo function_exists('mysql_connect') ? 1 : 0 ;"
//sudo port install php55 php55-mysql
