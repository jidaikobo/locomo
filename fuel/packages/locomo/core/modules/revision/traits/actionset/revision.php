<?php
namespace Revision;
trait Actionset_Revision
{
	/**
	 * view_revision()
	 * @return  array
	 */
	private static function view_revision($controller, $item)
	{
		$url = isset($item->id) ? "$controller/index_revision/$item->id" : null ;
		$url = self::check_auth($controller, 'index_revision') ? $url : '';

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
			'action_name'  => '閲覧（リビジョン）',
			'menu_str'     => '編集履歴',
			'explanation'  => '編集履歴の閲覧権限です。この権限を許可すると、元の項目が不可視、予約、期限切れ、削除済み等の状態であっても、履歴は閲覧することができるようになります。また、通常項目の編集権限も許可されます。',
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
