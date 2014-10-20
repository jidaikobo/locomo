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
	'site_title' => 'Light Staff',

	//identity
	'identity' => array(
		'logo' => array(
			'svg'   => '', //ex) logo.svg
			'image' => 'logo.png', //ex) logo.png
		),
		'color' => array(
			'background' => '9,73,153', //ex)  lighthouseのロゴカラーは#094999
			'figure' => '',     //ex) #fff
		),
	),

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

	//module_paths
	'module_paths' => array(
		PKGPROJPATH.'modules'.DS,
		PKGPATH.'kontiki/core/modules'.DS
	),

	//use_login_as_top
	'use_login_as_top' => true,

	//home_url
	'home_url' => 'content/home',

	//always guest allowed actions
	'always_allowed' => array(
		'user/login',
		'user/logout',
		'content/home',
		'content/403',
		'content/404',
		'content/fetch_view',
	),

	//

);
