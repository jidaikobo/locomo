<?php
namespace Locomo;
class Actionset_Scdl extends \Actionset_Base
{
//	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;

	/*
	(arr)  urls          メニューに表示するリンク先
	(arr)  overrides     urlをオーバライドする際に設定。ユーザグループのActionset_Optionにサンプルがある
	(bool) show_at_top   モジュール／コントローラトップに表示するかどうか
	(str)  action_name   ACL設定画面などで用いる
	(str)  explanation   ACL設定画面などで用いる説明文
	(int)  order         表示順
	(arr)  dependencies  このアクションセットが依存するアクション
	*/


	/**
	 * [actionset_edit_action description]
	 * @param  [type] $controller [description]
	 * @param  [type] $obj        [description]
	 * @param  [type] $id         [description]
	 * @param  array  $urls       [description]
	 * @return [type]             [description]
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array()) {

		if (\Request::main()->action != 'edit' && $id) {
			$actions = array(array($controller.DS."edit/" . $id, '編集'));
			$urls = static::generate_urls($controller.DS.'edit/' . $id, $actions, []);
		}
		
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '編集',
			'show_at_top'  => true,
			'acl_exp'      => '編集',
			'explanation'  => '編集',
			'help'         => '',
			'order'        => 10
		);
		return $retvals;
	}


	/**
	 * [actionset_edit_action description]
	 * @param  [type] $controller [description]
	 * @param  [type] $obj        [description]
	 * @param  [type] $id         [description]
	 * @param  array  $urls       [description]
	 * @return [type]             [description]
	 */
	public static function actionset_copy($controller, $obj = null, $id = null, $urls = array()) {
		if (\Request::main()->action != 'edit' && $id) {
			$actions = array(array($controller.DS."edit/?from=" . $id, 'コピー'));
			$urls = static::generate_urls($controller.DS.'edit', $actions, []);
		}
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'コピー',
			'show_at_top'  => true,
			'acl_exp'      => 'コピー',
			'explanation'  => 'コピー',
			'help'         => '',
			'order'        => 10
		);
		return $retvals;
	}

	/**
	 * [actionset_attend_action description]
	 * @param  [type] $controller [description]
	 * @param  [type] $obj        [description]
	 * @param  [type] $id         [description]
	 * @param  array  $urls       [description]
	 * @return [type]             [description]
	 */
	public static function actionset_attend($controller, $obj = null, $id = null, $urls = array())
	{

		if(\Request::main()->action == 'viewdetail' && $id && $obj->attend_flg):
			$schedule_data = \DB::select()->from("lcm_scdl_members")->where("schedule_id", 1)->where("user_id", \Auth::get('id'))->execute()->as_array();
			// 自分がメンバーであった場合
			if (count($schedule_data) > 0) {
				$actions = array(array($controller.DS."attend/" . $id, '出席確認'));
				$urls = static::generate_urls($controller.DS.'attend', $actions, ['']);
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
