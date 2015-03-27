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

	// upload path *not* terminated with /
	'upload_path' => APPPATH.'locomo/uploads',

	// menu_separators - add separators
	'menu_separators' => array(
		//'\Controller_Name' => '[has_top_separator|has_bottom_separator]',
	),

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
			'action' => '\\Controller_Sys/admin',
			'size' => 1,
		),
		array(
			'name' => '現在時刻',
			'action' => '\\Controller_Sys/clock',
			'size' => 1,
		),
	),

	// always guest allowed actions
	// ex) '\\Modname\\Controller_Something/action'
	'always_allowed' => array(
		'\\Controller_Auth/login',
		'\\Controller_Auth/logout',
		'\\Controller_Sys/home',
		'\\Controller_Sys/403',
		'\\Controller_Sys/404',
		'\\Controller_Sys/fetch_view',
		'\\Controller_Flr/dl',
	),

	// always user allowed actions
	'always_user_allowed' => array(
		'\\Controller_Hlp/view',
		'\\Controller_Sys/admin',
		'\\Controller_Sys/dashboard',
		'\\Controller_Sys/edit',
		'\\Controller_Sys/clock',
	),
);
