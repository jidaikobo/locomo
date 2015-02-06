<?php
namespace Locomo;
class Actionset_Scdl extends \Actionset_Base
{
//	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;

	/*
	(str)  realm         メニューの表示位置。デフォルトはbase
	(arr)  urls          メニューに表示するリンク先
	(bool) show_at_top   モジュール／コントローラトップに表示するかどうか
	(str)  action_name   ACL設定画面などで用いる
	(str)  explanation   モジュール先頭画面等で用いる説明文
	(str)  acl_exp       ACL設定画面などで用いる説明文
	(int)  order         表示順
	(arr)  dependencies  このアクションセットが依存するアクション
	*/

	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
				$controller.'::action_create',
				$controller.'::action_edit',
				$controller.'::action_viewdetail',
				$controller.'::action_attend',
				$controller.'::action_regchange',
				$controller.'::action_somedelete',
				$controller.'::action_calendar',
				$controller.'::action_view_invisible', // action is not exist yet
		);
		\Arr::set($retvals, 'dependencies', $actions);
		\Arr::set($retvals, 'action_name', 'スケジューラの管理権限');
		\Arr::set($retvals, 'acl_exp', 'スケジューラの管理権限です。');
		return $retvals;
	}

	/**
	 * actionset_edit
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_edit($controller, $obj, $id);

		if(\Request::main()->action == 'viewdetail' && $id):
			$actions = array(array($controller.DS."edit/" . $id, '編集'));
			$urls = static::generate_urls($controller.'::action_edit', $actions);
		endif;

		\Arr::set($retvals, 'urls', $urls);
		\Arr::set($retvals, 'action_name', '編集');
		\Arr::set($retvals, 'acl_exp', 'スケジューラの編集権限です。');
		return $retvals;
	}

	/**
	 * actionset_attend
	 */
	public static function actionset_attend($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'viewdetail' && $id && $obj->attend_flg):
			$schedule_data = \DB::select()->from("lcm_scdls_members")->where("schedule_id", $id)->where("user_id", \Auth::get('id'))->execute()->as_array();
			// 自分がメンバーであった場合
			if (count($schedule_data) > 0) {
				$actions = array(array($controller.DS."attend/" . $id, '出席確認'));
				$urls = static::generate_urls($controller.'::action_attend', $actions, ['']);
			}
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '出席',
			'show_at_top'  => true,
			'acl_exp'      => '出席するかどうかを確認',
			'explanation'  => '出席するかどうかを確認',
			'help'         => '',
			'order'        => 10
		);
		return $retvals;
	}


	/**
	 * actionset_somedelete
	 */
	public static function actionset_somedelete($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'viewdetail' && $id && $obj->repeat_kb >= 1):
			$actions = array(array($controller.DS."somedelete/" . $id . "/" . \Uri::segment(4) . "/" . \Uri::segment(5) . "/" . \Uri::segment(6), '部分削除'));
			$urls = static::generate_urls($controller.'::action_somedelete', $actions, ['']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '部分削除',
			'show_at_top'  => true,
			'acl_exp'      => '部分削除',
			'explanation'  => '部分削除',
			'help'         => '',
			'order'        => 12
		);
		return $retvals;
	}

	/**
	 * actionset_regchange
	 */
	public static function actionset_regchange($controller, $obj = null, $id = null, $urls = array())
	{

		if(\Request::main()->action == 'viewdetail' && $id && $obj->provisional_kb):
			$actions = array(array($controller.DS."regchange/" . $id . "/" . \Uri::segment(4) . "/" . \Uri::segment(5) . "/" . \Uri::segment(6), '本登録'));
			$urls = static::generate_urls($controller.'::action_regchange', $actions, ['']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '本登録',
			'show_at_top'  => true,
			'acl_exp'      => '本登録',
			'explanation'  => '本登録',
			'help'         => '',
			'order'        => 10
		);
		return $retvals;
	}


	/**
	 * actionset_copy
	 */
	public static function actionset_copy($controller, $obj = null, $id = null, $urls = array()) {

		if (\Request::main()->action != 'edit' && $id) {
			$actions = array(array($controller.DS."copy/?from=" . $id, 'コピー'));
			$urls = static::generate_urls($controller.'::action_copy', $actions, []);
		}
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'コピー',
			'show_at_top'  => true,
			'acl_exp'      => 'コピー',
			'explanation'  => 'コピー',
			'help'         => '',
			'order'        => 20
		);
		return $retvals;
	}
	
	// actionset_view
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array()) {
		
		$urls = array();
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '',
			'show_at_top'  => true,
			'acl_exp'      => '',
			'explanation'  => '',
			'help'         => '',
			'order'        => 10
		);
		return $retvals;
	}


}
