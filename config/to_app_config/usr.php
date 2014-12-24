<?php
return array(
	// conditioned auth actions
	// ex) '\\Modname\\Controller_Something/action' => '\\Modname\\Controller_Something::VALIDATE_STATIC_METHOD'
	'conditioned_allowed' => array(
		'\\Controller_Usr/view/' => array('\\Controller_Usr', 'user_auth_find'),
		'\\Controller_Usr/edit/' => array('\\Controller_Usr', 'user_auth_find'),
	),

	// user module setting
	'password_length' => 8, // default 8
);
