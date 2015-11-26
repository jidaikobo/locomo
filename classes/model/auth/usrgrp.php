<?php
namespace Locomo;
class Model_Auth_Usrgrp extends Model_Base
{
	protected static $_table_name = 'lcm_usrgrps';

	// $_properties
	protected static $_properties = array(
		'id',
		'name',
		'description',
		'seq',
		'is_available',
		'is_for_acl',
	);
}
