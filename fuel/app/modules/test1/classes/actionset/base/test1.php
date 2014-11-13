<?php
namespace Test1;
class Actionset_Base_Test1 extends \Actionset_Base
{
//	use \Revision\Traits_Actionset_Base_Revision;
//	use \Workflow\Traits_Actionset_Base_Workflow;

	/*
	(arr)  urls          メニューに表示するリンク先
	(arr)  overrides     urlをオーバライドする際に設定。ユーザグループのActionset_Optionにサンプルがある
	(str)  action_name   ACL設定画面で用いる
	(str)  explanation   ACL設定画面で用いる説明文
	(int)  order         表示順
	(arr)  dependencies  このアクションセットが依存するアクション
	*/

	/**
	 * actionset_sample_action()
	 * to use remove first underscore at the function name
	 */
	public static function _actionset_sample_action($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'edit' && $id):
			$actions = array(array($module.DS.$controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($module, $controller, 'view', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'sample_action',
			'explanation'  => 'explanation of sample_action',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'sample_action',
			)
		);
		return $retvals;
	}
}
