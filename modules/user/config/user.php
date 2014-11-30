<?php
return array(
	'nicename'          => 'ユーザ',
	'main_controller' => '\\User\\Controller_User',

	// conditioned auth actions
	// ex) '\\Modname\\Controller_Something/action' => '\\Modname\\Controller_Something::VALIDATE_STATIC_METHOD'
	'conditioned_allowed' => array(
		'\\User\\Controller_User/view/' => array('\\User\\Controller_User', 'user_auth_find'),
		'\\User\\Controller_User/edit/' => array('\\User\\Controller_User', 'user_auth_find'),
	),

	// help document
	'help' => array(
		'ユーザモジュールについて' => dirname(__DIR__).'/help/user.html',
	),

	// user module setting
	'password_length' => 8, // default 8
);
