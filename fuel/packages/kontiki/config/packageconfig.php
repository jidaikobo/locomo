<?php
//root user
define('ROOT_USER_NAME','root');
define('ROOT_USER_PASS','131313');

//admin user
define('ADMN_USER_NAME','admin');
define('ADMN_USER_PASS','121212');

//fuel config
return array(
	//base
	'site_title' => 'kontiki package',

	//locale settings
	'language'          => 'ja',
	'language_fallback' => 'en',
	'locale'            => 'ja_JP.utf8',

	//user_ban_setting
	'user_ban_setting' => array(
		'limit_deny_time' => 10,  //◯分間の間に
		'limit_count'     => 3,   //◯回エラーがあると
		'limit_time'      => 300, //◯秒間バンする
	),

	//modules settings
	'module_paths' => array(
		APPPATH.'modules'.DS,
		PKGPATH.'kontiki/modules'.DS
	),

	//acl modules settings
	'acl' => array(
		'user',
		'usergroup',
	),

	//always guest allowed controllers
	'always_allowed' => array(
		'user/login',
		'user/logout',
	),
);
