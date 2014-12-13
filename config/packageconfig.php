<?php

$admins = array(
	'root' => array(
		// 'username' => array('username', 'password')
		'root' => array('root', '131313'),
	),
	'admin' => array(
		'admin'  => array('admin', '121212'),
		'admin2' => array('admin2', '121212'),
	),
);
defined('LOCOMO_ADMINS') or define('LOCOMO_ADMINS', serialize($admins));

// fuel config
return array(
	// base
	'site_title' => 'Locomo',
	'slogan' => 'Accessible Web System Package for FuelPHP',

	// identity
	'identity' => array(
		'logo' => array(
			'svg'   => '', //ex) logo.svg
			'image' => 'logo.png', //ex) logo.png
		),
		'color' => array(
			'background' => '9,73,153', //ex) 9,73,153
			'figure' => '',     //ex) #fff
		),
	),

	// locale settings
	'language'          => 'ja',
	'language_fallback' => 'en',
	'locale'            => 'ja_JP.utf8',

	// user_ban_setting
	'user_ban_setting' => array(
		'limit_deny_time' => 10,  //◯分間の間に
		'limit_count'     => 3,   //◯回エラーがあると
		'limit_time'      => 300, //◯秒間バンする
	),

	// module_paths
	'module_paths' => array(
		APPPATH.'modules'.DS,
		PKGPATH.'locomo/modules'.DS
	),

	// no_home
	'no_home' => false,

	// default_dashboard
	'default_dashboard' => array(
		array(
			'name' => 'コントローラ一覧',
			'action' => '\\Admin\\Controller_Admin/home',
			'size' => 1,
		),
		array(
			'name' => '現在時刻',
			'action' => '\\Admin\\Controller_Admin/clock',
			'size' => 1,
		),
	),

	// always guest allowed actions
	// ex) '\\Modname\\Controller_Something/action'
	'always_allowed' => array(
		'\\User\\Controller_Auth/login',
		'\\User\\Controller_Auth/logout',
		'\\Content\\Controller_Content/home',
		'\\Content\\Controller_Content/403',
		'\\Content\\Controller_Content/404',
		'\\Content\\Controller_Content/fetch_view',
	),
);
