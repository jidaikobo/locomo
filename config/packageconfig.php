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
defined('LOCOMO_ADMIN_MAIL') or define('LOCOMO_ADMIN_MAIL', 'example@example.com');
defined('LOCOMO_MAIL_SIGNATURE') or define('LOCOMO_MAIL_SIGNATURE', "-- \n");

// fuel config
return array(
	// base
	'site_title' => 'Locomo',
	'slogan' => 'Accessible Web System Package for FuelPHP',

	// is_allow_mailaddress_login
	'is_allow_mailaddress_login' => true,

	// user_registration_type
	// by_admin: only administrator can add new user.
	// by_user_only: user can activate account by email activation.
	// by_user_admin: user can apply to registration, administrator activates user.
	'user_registration_type' => 'by_admin',

	// user_registration_limit_days
	'user_registration_limit_days' => 3,

	// default_usergroup_id
	'default_usergroup_id' => null,

	// allowed_ip_access_admin
	'allowed_ip_access_admin' => '',

	// is_use_customusergroup
	'is_use_customusergroup' => true,

	// is_remind_password
	'is_remind_password' => true,

	// is_admin_knows_password
	'is_admin_knows_password' => false,

	// upload path *not* terminated with /
	'upload_path' => APPPATH.'locomo/uploads',

	// menu_separators - add separators
	'menu_separators' => array(
		//'\Controller_Name' => '[has_top_separator|has_bottom_separator]',
		'\Controller_Scdl' => 'has_top_separator',
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
			'action' => '\\Controller_Sys/admin_dashboard',
			'size' => 1,
			'seq' => 1,
		),
		array(
			'name' => '現在時刻',
			'action' => '\\Controller_Sys/clock',
			'size' => 1,
			'seq' => 2,
		),
	),

	// disable controller even if for administrator
	// ex) '\\Modname\\Controller_Something'
	'disabled_controllers' => array(
		// '\\Controller_Scdl',
		// '\\Controller_Msgbrd',
		// '\\Controller_Adrs',
		// '\\Controller_Flr',
		// '\\Controller_Usr',
		// '\\Controller_Usrgrp',
		// '\\Controller_Acl',
		// '\\Controller_Scffld',
		// '\\Controller_Wrkflwadmin',
		// '\\Controller_Bkmk',
		// '\\Controller_Pg',
		// '\\Controller_Srch',
	),

	// always guest allowed actions
	// ex) '\\Modname\\Controller_Something/action'
	'always_allowed' => array(
		'\\Controller_Auth/login',
		'\\Controller_Auth/logout',
		'\\Controller_Sys/home',
		'\\Controller_Sys/403',
		'\\Controller_Sys/404',
		'\\Controller_Flr/dl',
		'\\Controller_Auth/registration', // ACL controled by controller
		'\\Controller_Auth/by_user_only',
		'\\Controller_Auth/by_user_admin',
	),

	// always user allowed actions
	'always_user_allowed' => array(
		'\\Controller_Hlp/view',
		'\\Controller_Sys/admin_dashboard',
		'\\Controller_Sys/admin',
		'\\Controller_Sys/dashboard',
		'\\Controller_Sys/edit',
		'\\Controller_Sys/clock',
	),

	// always group allowed actions
	// KeyVal: Key must be existence usergroup's id
	'always_group_allowed' => array(
/*
		1 => array(
			'\\Controller_Hlp/view',
		)
*/
	),
);
