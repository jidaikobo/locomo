<?php
namespace Kontiki_Core;
class Model_Base extends \Orm\Model_Soft
{

/*
	protected $_authorize_options = array(
		'view_anyway', => true,
		'deleted' => true,
		'view_expired' => array(array('expired_at', '<', date('Y-m-d')), 'or' => (array('expired_at', 'is', null))),
		'created_at' => array(array('created_at', '<', date('Y-m-d')), 'or' => (array('created_at', 'is', null))),
		'invisible' => array('status', '=', 'invisible'),
	);
*/
	public static function authorized_option($options = array()) {

		$userinfo = \User\Controller_User::$userinfo;

		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));

		if (\Acl\Controller_Acl::auth($controller . '/view_anyway', $userinfo)) {
			static::disable_filter();

		} else {
			if (
				isset(static::properties()['expired_at']) &&
				!\Acl\Controller_Acl::auth($controller . '/view_expired', $userinfo)
			) {
				$options['where'][] = array(array('expired_at', '<', date('Y-m-d'))
					, 'or' => (array('expired_at', 'is', null)));
			}

			if (
				isset(static::properties()['created_at']) &&
				!\Acl\Controller_Acl::auth($controller . '/view_yet', $userinfo)
			) {
				$options['where'][] = array(array('created_at', '<', date('Y-m-d'))
					, 'or' => (array('created_at', 'is', null)));
			}

			if (
				(static::forge() instanceof \Orm\Model_Soft) &&
				!\Acl\Controller_Acl::auth($controller . '/view_deleted', $userinfo)
			) {
				static::enable_filter();
			} else {
				static::disable_filter();
			}

			if (
				isset(static::properties()['status']) &&
				!\Acl\Controller_Acl::auth($controller . '/view_invisible', $userinfo)
			) {
				$options['where'][] = array('status', '=', 'invisible');
			}

			// worlflow 権限は invisible
			// コントローラで、in_progressだったらstatusをinvisibleに
			if (
				isset(static::properties()['workflow_status']) &&
				!\Acl\Controller_Acl::auth($controller . '/view_invisible', $userinfo)
			) {
				$conditions['where'][] = array('workflow_status', '!=', 'in_progress');
			}
		}

		return $options;
	}
}
