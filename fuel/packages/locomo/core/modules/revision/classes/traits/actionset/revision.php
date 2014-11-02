<?php
namespace Revision;
trait Traits_Actionset_Revision
{
	/**
	 * actionset_index_revision()
	 */
	public static function actionset_index_revision($module, $obj, $id, $urls = array())
	{
		if($id && in_array(\Request::main()->action, array('edit','view'))):
			$actions = array(array("{$module}/each_index_revision/{$module}/".$id, '編集履歴'));
			$urls = static::generate_anchors($module, 'index_revision', $actions, $obj);
		endif;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '編集履歴',
			'explanation'  => '編集項目の権限です。',
			'order'        => 10,
			'dependencies' => array(
				'index_revision',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_view_revision()
	 */
	public static function actionset_view_revision($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array("{$module}/each_index_revision/{$module}/".$id, '編集履歴'));
			$urls = static::generate_anchors($module, 'index_revision', $actions, $obj);
		endif;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '閲覧（リビジョン）',
			'explanation'  => '編集履歴の閲覧権限です。この権限を許可すると、元の項目が不可視、予約、期限切れ、削除済み等の状態であっても、履歴は閲覧することができるようになります。また、通常項目の編集権限も許可されます。',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'edit',
				'view_revision',
				'index_revision',
			)
		);
		return $retvals;
	}
}
