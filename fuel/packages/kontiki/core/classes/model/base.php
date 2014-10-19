<?php
namespace Kontiki_Core;
class Model_Base extends \Orm\Model_Soft
{
	/*
	 * default field names
	 */
	protected static $_default_created_field_name    = 'created_at';
	protected static $_default_expired_field_name    = 'expired_at';
	protected static $_default_visibility_field_name = 'is_visible';

	/*
	 * default authorize options
	 */
	protected static $_authorize_methods = array(
		'auth_expired',
		'auth_created',
		'auth_deleted',
		'auth_visibility',
	);

	/*
	 * set_authorize_methods()
	 */
	public static function set_authorize_methods($method)
	{
		
	}

	/*
	 * authorized_option()
	 * adjust Model::find(#, $options)
	 */
	public static function authorized_option($options = array())
	{
		$userinfo = \User\Controller_User::$userinfo;
		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));

		//view_anywayが許されているユーザにはsoft_delete判定を外してすべて返す
		if (\Acl\Controller_Acl::auth($controller.'/view_anyway', $userinfo)) {
			static::disable_filter();
		} else {
			//モデルが持っている判定材料を、適宜$optionsに足す。
			foreach(self::$_authorize_methods as $authorize_method):
				$options = self::$authorize_method($controller, $userinfo, $options);
			endforeach;

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

	/*
	 * auth_expired()
	*/
	public static function auth_expired($controller = null, $userinfo = null, $options = array())
	{
		$column = isset(static::$_expired_field_name) ?: static::$_default_expired_field_name;
		if (
			isset(static::properties()[$column]) &&
			! \Acl\Controller_Acl::auth($controller . '/view_expired', $userinfo)
		) {
			$options['where'][] = array(array($column, '<', date('Y-m-d'))
				, 'or' => (array($column, 'is', null)));
		}
		return $options;
	}

	/*
	 * auth_created()
	*/
	public static function auth_created($controller = null, $userinfo = null, $options = array())
	{
		$column = isset(static::$_created_field_name) ?: static::$_default_created_field_name;
		if (
			isset(static::properties()[$column]) &&
			!\Acl\Controller_Acl::auth($controller . '/view_yet', $userinfo)
		) {
			$options['where'][] = array(array($column, '<', date('Y-m-d'))
				, 'or' => (array($column, 'is', null)));
		}
		return $options;
	}

	/*
	 * auth_deleted()
	*/
	public static function auth_deleted($controller = null, $userinfo = null, $options = array())
	{
		if (
			(static::forge() instanceof \Orm\Model_Soft) &&
			!\Acl\Controller_Acl::auth($controller . '/view_deleted', $userinfo)
		) {
			static::enable_filter();
		} else {
			static::disable_filter();
		}
		return $options;
	}

	/*
	 * auth_visibility()
	*/
	public static function auth_visibility($controller = null, $userinfo = null, $options = array())
	{
		$column = isset(static::$_visibility_field_name) ?: static::$_default_visibility_field_name;
		if (
			isset(static::properties()[$column]) &&
			!\Acl\Controller_Acl::auth($controller . '/view_invisible', $userinfo)
		) {
			$options['where'][] = array($column, '=', 'false');
		}
		return $options;
	}
}
