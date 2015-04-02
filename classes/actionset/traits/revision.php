<?php
namespace Locomo;
trait Actionset_Traits_Revision
{
	/**
	 * actionset_index_revision()
	 */
	public static function actionset_index_revision($controller, $obj = null, $id = null, $urls = array())
	{
		if ($id && in_array(\Request::main()->action, array('edit','view')))
		{
			$urls = array(array($controller.DS."each_index_revision/".$id, '編集履歴'));
		}

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '編集履歴',
			'explanation'  => '編集履歴の閲覧。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/edit',
				$controller.'/view_revision',
				$controller.'/index_revision',
				$controller.'/each_index_revision',
			)
		);
		return $retvals;
	}
}
