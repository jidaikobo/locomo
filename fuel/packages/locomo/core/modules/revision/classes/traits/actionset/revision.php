<?php
namespace Revision;
trait Traits_Actionset_Revision
{
	/**
	 * actionset_index_revision()
	 */
	public static function actionset_index_revision($module, $obj, $get_authed_url)
	{
		if(
			$obj &&
			in_array(\Request::main()->action, array('edit','view')) &&
			$get_authed_url
		):
			//個票のとき、履歴へのリンクを表示する
			$pk = $obj::get_primary_keys('first');
			$url = "{$module}/index_revision/{$obj->$pk}";
			$url = static::check_auth($module, 'index_revision') ? $url : '' ;
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '編集履歴',
			'menu_str'     => '編集履歴',
			'explanation'  => '編集項目の権限です。',
			'dependencies' => array(
				'index_revision',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_view_revision()
	 */
	public static function actionset_view_revision($module, $obj, $get_authed_url)
	{
		if($get_authed_url):
			$url = isset($item->id) ? "$module/index_revision/$obj->id" : null ;
			$url = self::check_auth($module, 'index_revision') ? $url : '';
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'menu_str'     => '編集履歴',
			'action_name'  => '閲覧（リビジョン）',
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
