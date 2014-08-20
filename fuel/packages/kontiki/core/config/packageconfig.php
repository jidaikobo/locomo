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

	//use_login_as_top
	'use_login_as_top' => false,

	//modules settings
	'module_paths' => array(
		PKGPATH.'kontiki/modules'.DS,
		PKGPATH.'kontiki/core/modules'.DS
	),

	//always guest allowed actions
	'always_allowed' => array(
		'user/login',
		'user/logout',
	),
);
