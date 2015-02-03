<?php
namespace Locomo;
trait Actionset_Traits_Revision
{
	/**
	 * actionset_index_revision()
	 */
	public static function actionset_index_revision($controller, $obj = null, $id = null, $urls = array())
	{
		if ($id && in_array(\Request::main()->action, array('edit','view'))):
			$actions = array(array($controller.DS."each_index_revision/".$id, '編集履歴'));
			$urls = static::generate_urls($controller.DS.'index_revision', $actions);
		endif;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '編集履歴',
			'explanation'  => '編集履歴の閲覧。',
			'acl_exp'      => '編集履歴閲覧の権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.DS.'view',
				$controller.DS.'edit',
				$controller.DS.'view_revision',
				$controller.DS.'index_revision',
				$controller.DS.'each_index_revision',
			)
		);
		return $retvals;
	}
}
