<?php
namespace XXX;
class Actionset_XXX extends \Actionset
{
	//use \Revision\Actionset_Revision;
	//use \Workflow\Actionset_Workflow;

	/**
	 * set_actionset()
	 */
	public static function set_actionset($module = null, $obj = null)
	{
		parent::set_actionset($module, $obj);
//		static::$actions->sample_action = self::sample_action($controller, $item);

		//revision
		/*
		static::$actions->view_revision = self::view_revision($controller, $item);
		*/

		//workflow
		/*
		static::$actions->index_workflow   = self::index_workflow($controller, $item);
		static::$actions->workflow         = self::workflow($controller, $item);
		static::$actions->workflow_process = self::workflow_process($controller, $item);
		static::$actions->workflow_actions = self::workflow_actions($controller, $item);
		if(@$item->workflow_status == 'in_progress') unset(static::$actions->edit);
		*/
	}

	/*
	(bool) is_admin_only 管理者のみに許された行為。ACL設定画面に表示されなくなる
	(bool) is_index      メニューに表示する際、インデクス系として表示する
	(str)  url           メニューに表示するリンク先
	(int)  id_segment    \Kontiki\Controller::set_current_id()で用いる。個票系の際は必要
	(str)  action_name   ACL設定画面で用いる
	(str)  explanation   ACL設定画面で用いる説明文
	(str)  menu_str      メニューで用いる
	(arr)  dependencies  このアクションセットが依存するアクション。通常はactionだけだが、action/argumentのように書くこともできる。前後のスラッシュはつけないこと
	*/

	/**
	 * sample_action()
	 * @return  array
	 */
	private static function sample_action($controller, $item)
	{
		$url = parent::check_auth($controller, 'sample_action') ? "{$controller}/sample_action" : '';

		$retvals = array(
			'is_admin_only' => false,
			'is_index'      => true,
			'url'           => $url,
			'id_segment'    => null,
			'action_name'   => 'sample_action',
			'explanation'   => 'sample_action',
			'menu_str'      => 'sample_action',
			'dependencies' => array(
				'sample_action',
			)
		);
		return $retvals;
	}
}
