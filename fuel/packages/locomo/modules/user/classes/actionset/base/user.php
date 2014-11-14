<?php
namespace User;
class Actionset_Base_User extends \Actionset_Base
{
	use \Revision\Traits_Actionset_Revision;

	/**
	 * edit_owner()
	 */
	public static function actionset_edit_owner($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'view' && $id):
			$actions = array(array($controller.DS."edit/".$id, '編集'));
			$urls = static::generate_uris($controller, 'edit', $actions, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '編集（本人の編集権限）',
			'explanation'  => 'ユーザIDが一致した者の編集権限',
			'order'        => 35,
			'dependencies' => array(
				$controller.DS.'view'.DS.'[user_id, =, id]',
				$controller.DS.'edit'.DS.'[user_id, =, id]',
			)
		);
		/*
			条件付き構文の書き方
			$controller.DS.'ACTNAME'.DS.'[[user_id], [=|<|>|<=|>=|<>], CONTENT_FIELD_NAME]',
		*/
		return $retvals;
	}

	/**
	 * edit_owner()
	 */
	public static function actionset_view_owner($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'view' && $id):
			$actions = array(array($controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($controller, 'view', $actions, ['view']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '閲覧（本人の閲覧権限）',
			'explanation'  => 'ユーザIDが一致した者の閲覧権限',
			'order'        => 35,
			'dependencies' => array(
				$controller.DS.'view'.DS.'[user_id, =, id]',
			)
		);
		return $retvals;
	}
}
