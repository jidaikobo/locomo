<?php
namespace Locomo;
class Controller_Scffld_Helper_Actionset extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig, $type, $model)
	{
		// cmd
		$cmd_mods = array();
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);// remove name
		foreach($cmds as $field)
		{
			list($field, $attr) = explode(':', $field);
			$field = self::remove_nicename($field);
			$cmd_mods[] = $field;
		}

		// default actionset
		$actionset_str = '';
		$actionset_idx_str = '';

		// Model_Base_Soft
		if ($model == 'Model_Base_Soft')
		{
			$actionset_str = '
	/**
	 * actionset_delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::delete($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::undelete($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_purge_confirm()
	 */
	public static function actionset_purge_confirm($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::purge_confirm($controller, $obj, $id, $urls);
	}
';
		}

		// $cmd_mods
		if (in_array('deleted_at', $cmd_mods))
		{
			$actionset_idx_str.= '
	/**
	 * actionset_index_deleted()
	 */
	public static function actionset_actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_deleted($controller, $obj, $id, $urls);
	}
';
		}

		// $cmd_mods
		if (in_array('created_at', $cmd_mods))
		{
			$actionset_idx_str.= '
	/**
	 * actionset_index_yet()
	 */
	public static function actionset_actionset_index_yet($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_yet($controller, $obj, $id, $urls);
	}
';
		}

		// $cmd_mods
		if (in_array('expired_at', $cmd_mods))
		{
			$actionset_idx_str.= '
	/**
	 * actionset_index_expired()
	 */
	public static function actionset_actionset_index_expired($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_expired($controller, $obj, $id, $urls);
	}
';
		}

		// $cmd_mods
		if (in_array('is_visible', $cmd_mods))
		{
			$actionset_idx_str.= '
	/**
	 * actionset_index_invisible()
	 */
	public static function actionsetindex_invisible($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_invisible($controller, $obj, $id, $urls);
	}
';
		}

		$val = static::fetch_temlpate('actionset.php');
		$val = str_replace('###ACTIONSET_ACTION###', $actionset_str, $val);
		$val = str_replace('###ACTIONSET_INDEX###', $actionset_idx_str, $val);
		// モジュール以外では名前空間を削除
		$val = $type !== 'module' ? str_replace("namespace XXX;\n", '', $val) : $val ;
		$val = self::replaces($name, $val);
		return $val;
	}
}
