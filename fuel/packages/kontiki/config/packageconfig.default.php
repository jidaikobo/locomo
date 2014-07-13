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
		PKGPATH.'kontiki/modules_default'.DS
	),

	//modules settings
	//'{$module_name}' => array('is_admin_only' => bool...)
	//is_admin_onlyがtrueだと、aclの候補にならず、かつ管理者向けメニューにか表示されなくなります
	'modules' => array(
		'content' => array(
			'nicename'      => 'トップページ',
			'adminindex'    => 'home',
			'is_admin_only' => false,
		),
		'user' => array(
			'nicename'      => 'ユーザ',
			'adminindex'    => 'index_admin',
			'is_admin_only' => false,
		),
		'usergroup' => array(
			'nicename'      => 'ユーザグループ',
			'adminindex'    => 'index_admin',
			'is_admin_only' => false,
		),
		'acl' => array(
			'nicename'      => 'アクセス権管理',
			'adminindex'    => 'controller_index',
			'is_admin_only' => false,
		),
		'scaffold' => array(
			'nicename'      => '足場組み',
			'adminindex'    => 'main',
			'is_admin_only' => true,
		),
	),

	//always guest allowed actions
	'always_allowed' => array(
		'user/login',
		'user/logout',
	),
);
