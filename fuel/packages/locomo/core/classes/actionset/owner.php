<?php
namespace Locomo_Core;
class Actionset_Owner
{
	public static $actions;

	/**
	 * get_actionset()
	 * @return  obj
	 */
	public static function get_actionset($module = null)
	{
		if( ! \Module::loaded($module)){
			if( ! \Module::load($module)) die("module doesn't exist");
		}

		$path = \Module::exists($module)."classes/actionset/{$module}_owner.php";
		if( ! file_exists($path)){
			return false;
		}

		require_once($path);
		$actionset_class = \Locomo\Util::get_valid_actionset_name($module).'_Owner';

		if(class_exists($actionset_class)){
			self::set_actionset();
			return self::$actions;
		}
		return false;
	}

	/**
	 * set_actionset()
	 * @param str $module
	 * @return  obj
	 */
	public static function set_actionset()
	{
		static::$actions = (object) array();
		static::$actions->view           = self::view();
		static::$actions->view_revision  = self::view_revision();
		static::$actions->view_expired   = self::view_expired();
		static::$actions->view_yet       = self::view_yet();
		static::$actions->view_deleted   = self::view_deleted();
		static::$actions->view_invisible = self::view_invisible();
		static::$actions->edit           = self::edit();
	}

	/**
	 * view()
	 * @return  array
	 */
	private static function view()
	{
		$retvals = array(
			'action_name' => '閲覧（通常項目）',
			'explanation' => '項目制作者への項目閲覧権限です。一覧の閲覧権限は許可されません。',
			'dependencies' => array(
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * view_revision()
	 * @return  array
	 */
	private static function view_revision()
	{
		$retvals = array(
			'action_name' => '閲覧（リビジョン）',
			'explanation' => '作業履歴の閲覧権限です。この権限を許可すると、元の項目が不可視、予約、期限切れ、削除済み等の状態であっても、履歴はみることができるようになります。また、通常項目の編集権限も許可されます。',
			'dependencies' => array(
				'view',
				'edit',
				'view_revision',
			)
		);
		return $retvals;
	}
	
	/**
	 * view_expired()
	 * @return  array
	 */
	private static function view_expired()
	{
		$retvals = array(
			'action_name' => '閲覧（期限切れ）',
			'explanation' => '期限切れ項目の閲覧権限です。',
			'dependencies' => array(
				'view_expired',
			)
		);
		return $retvals;
	}

	/**
	 * view_yet()
	 * @return  array
	 */
	private static function view_yet()
	{
		$retvals = array(
			'action_name' => '閲覧（予約項目）',
			'explanation' => '予約項目の閲覧権限です。',
			'dependencies' => array(
				'view_yet',
			)
		);
		return $retvals;
	}

	/**
	 * view_deleted()
	 * @return  array
	 */
	private static function view_deleted()
	{
		$retvals = array(
			'action_name' => '閲覧（削除された項目）',
			'explanation' => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'dependencies' => array(
				'index_deleted',
				'view_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_invisible()
	 * @return  array
	 */
	private static function view_invisible()
	{
		$retvals = array(
			'action_name' => '閲覧（不可視項目）',
			'explanation' => '不可視項目の閲覧権限',
			'dependencies' => array(
				'view_invisible',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 * @return  array
	 */
	private static function edit()
	{
		$retvals = array(
			'action_name' => '項目の編集',
			'explanation' => '特殊な条件のない項目の編集権限',
			'dependencies' => array(
				'view',
				'edit',
			)
		);
		return $retvals;
	}

	/**
	 * delete_file()
	 * @return  array
	 */
	private static function delete_file()
	{
		$retvals = array(
			'action_name' => 'ファイルの削除権限',
			'explanation' => '添付ファイルの削除権限です。',
			'dependencies' => array(
				'delete_file',
			)
		);
		return $retvals;
	}
}
