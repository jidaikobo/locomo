<?php

$admins = array(
	'root' => array(
		//'username' => array('username', 'password')
		'root' => array('root', '131313'),
	),
	'admin' => array(
		'admin'  => array('admin', '121212'),
		'admin2' => array('admin2', '121212'),
	),
);
defined('LOCOMO_ADMINS') or define('LOCOMO_ADMINS',serialize($admins));

//fuel config
return array(
	//base
	'site_title' => 'Lightstaff',
	'slogan' => 'barrier free ERP',

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
		APPPATH.'modules'.DS,
		PKGPATH.'locomo/modules'.DS
	),

	//no_home
	'no_home' => false,

	//home_url
	'home_url' => 'content/home',

	//always guest allowed actions
	'always_allowed' => array(
		'\\User\\Controller_Auth/login/',
		'\\User\\Controller_Auth/logout/',
		'\\Content\\Controller_Content/home/',
		'\\Content\\Controller_Content/403/',
		'\\Content\\Controller_Content/404/',
		'\\Content\\Controller_Content/fetch_view/',
		'\\Admin\\Controller_Admin/home',
		'\\Admin\\Controller_Admin/dashboard',
		'\\Help\\Controller_Help/index_admin',//not for guest. ban by \Help\Controller_Help::before()
	),
);
