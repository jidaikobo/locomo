<?php
//root user
defined('ROOT_USER_NAME') or define('ROOT_USER_NAME','root');
defined('ROOT_USER_PASS') or define('ROOT_USER_PASS','131313');

//admin user
defined('ADMN_USER_NAME') or define('ADMN_USER_NAME','admin');
defined('ADMN_USER_PASS') or define('ADMN_USER_PASS','121212');

//fuel config
return array(
	//base
	'site_title' => 'locomo package',

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
		PKGPATH.'locomo/core/modules'.DS
	),

	//use_login_as_top
	'use_login_as_top' => false,

	//home_url
	'home_url' => 'content/home',

	//always guest allowed actions
	'always_allowed' => array(
		'user/login',
		'user/logout',
		'content/home',
		'content/404',
		'content/403',
		'content/fetch_view',
	),

);
