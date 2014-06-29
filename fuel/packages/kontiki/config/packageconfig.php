<?php

return array(
	//locale settings
	'default_timezone'   => 'Asia/Tokyo',
	'language'          => 'ja',
	'language_fallback' => 'en',
	'locale'            => 'ja_JP.utf8',

	//modules settings
	'module_paths' => array(
		APPPATH.'modules'.DS,
		PKGPATH.'kontiki/modules'.DS
	),

	//acl modules settings
	'acl' => array(
		'user',
		'usergroup',
	),
);
