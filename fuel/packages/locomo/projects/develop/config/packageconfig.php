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

//root user
defined('ROOT_USER_NAME') or define('ROOT_USER_NAME','root');
defined('ROOT_USER_PASS') or define('ROOT_USER_PASS','131313');

//admin user
defined('ADMN_USER_NAME') or define('ADMN_USER_NAME','admin');
defined('ADMN_USER_PASS') or define('ADMN_USER_PASS','121212');

//fuel config
return array(
	//base
	'site_title' => 'Lightstaff',

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
		PKGPROJPATH.'modules'.DS,
		PKGPATH.'locomo/core/modules'.DS
	),

	//use_login_as_top
	'use_login_as_top' => true,

	//home_url
	'home_url' => 'content/home',

	//always guest allowed actions
	//always guest allowed actions
	'always_allowed' => array(
		'user/\\User\\Controller_User/login',
		'user/\\User\\Controller_User/logout',
		'content/\\Content\\Controller_Content/home',
		'content/\\Content\\Controller_Content/403',
		'content/\\Content\\Controller_Content/404',
		'content/\\Content\\Controller_Content/fetch_view',
	),
);
