<?php
namespace User;
class Model_Admin extends \Locomo\Model_Base
{
	/**
	 * vals
	 */
	protected static $_table_name = 'user_admins';

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'username',
	);
}
