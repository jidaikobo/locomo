<?php
namespace Revision;
trait Traits_Actionset_Revision
{
	/**
	 * actionset_index_revision()
	 */
	public static function actionset_index_revision($controller, $obj = null, $id = null, $urls = array())
	{
		if ($id && in_array(\Request::main()->action, array('edit','view'))):
			//if model name is different from controller name, copy from this file and write controller own actionset.
			$model = basename(\Inflector::ctrl_to_dir($controller));
			$actions = array(array($controller.DS."each_index_revision/{$model}/".$id, '編集履歴'));
			$urls = static::generate_uris($controller, 'index_revision', $actions);
		endif;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '編集履歴',
			'explanation'  => '編集履歴の閲覧。',
			'acl_exp'      => '編集履歴閲覧の権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.DS.'index_revision',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_view_revision()
	 */
	public static function actionset_view_revision($controller, $obj = null, $id = null, $urls = array())
	{
		if ($id):
			//if model name is different from controller name, copy from this file and write controller own actionset.
			$model = basename(\Inflector::ctrl_to_dir($controller));
			$actions = array(array($controller.DS."each_index_revision/{$model}/".$id, '編集履歴'));
			$urls = static::generate_uris($controller, 'index_revision', $actions);
		endif;

		$retvals = array(
			'urls'        => $urls ,
			'action_name' => '閲覧（リビジョン）',
			'explanation' => '編集履歴を閲覧します。',
			'acl_exp'     => '編集履歴の閲覧権限です。この権限を許可すると、元の項目が不可視、予約、期限切れ、削除済み等の状態であっても、履歴は閲覧することができるようになります。また、通常項目の編集権限も許可されます。',
			'order'        => 100,
			'dependencies' => array(
				$controller.DS.'view',
				$controller.DS.'edit',
				$controller.DS.'view_revision',
				$controller.DS.'index_revision',
			)
		);
		return $retvals;
	}
}
