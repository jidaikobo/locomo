<?php
namespace Revision;
trait Traits_Actionset_Base_Revision
{
	/**
	 * actionset_revisions()
	 */
	public static function actionset_revisions($controller, $obj = null, $id = null, $urls = array())
	{
		if ($id && in_array(\Request::main()->action, array('edit','view'))):
			$actions = array(array($controller.DS."each_index_revision/".$id, '編集履歴'));
			$urls = static::generate_urls($controller.DS.'index_revision', $actions);
		endif;

		// overrides - when action in revisions, override "base" realm
		$overrides['base'] = false;
		$arr = ['index_revision', 'each_index_revision', 'view_revision'];
		if(in_array(\Request::main()->action, $arr))
		{
			$overrides_urls = array(
				array($controller.DS, $controller::$locomo['admin_home_name']),
			);
			$overrides['base'] = static::generate_urls($controller.DS.'edit', $overrides_urls, [], 'option');
		}

		$retvals = array(
			'urls'          => $urls ,
			'overrides'    => $overrides['base'] ? $overrides : array(),
			'action_name'  => '編集履歴',
			'explanation'  => '編集履歴の閲覧。',
			'acl_exp'      => '編集履歴閲覧の権限です。',
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
