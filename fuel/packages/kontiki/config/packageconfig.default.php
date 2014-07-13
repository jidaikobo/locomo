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
		PKGPATH.'kontiki/modules'.DS,
		PKGPATH.'kontiki/modules_base'.DS
	),

	//modules settings
	//'{$module_name}' => array('is_admin_only' => bool)
	//is_admin_onlyがtrueだと、aclの候補にならず、かつ管理者向けメニューにか表示されなくなります
	'modules' => array(
		'user'      => array('is_admin_only' => false,),
		'usergroup' => array('is_admin_only' => false,),
		'acl'       => array('is_admin_only' => false,),
		'scaffold'  => array('is_admin_only' => true,),
	),

	//acl modules settings
	'acl' => array(
		'user',
		'usergroup',
	),

	//always guest allowed actions
	'always_allowed' => array(
		'user/login',
		'user/logout',
	),
);
