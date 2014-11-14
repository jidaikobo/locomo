<?php
namespace Sample;
class Actionset_Option_Sample extends \Locomo\Actionset_Option
{
	use \Locomo\Actionset_Traits_Testdata;

	/**
	 * actionset_sample()
	 */
	public static function actionset_sample($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."sample", 'オプション'));
		$urls = static::generate_uris($controller, 'create', $actions);

		//overrides
		$overrides = static::generate_bulk_anchors(
			$module      = 'user',
			$controller  = 'user',
			$model       = 'usergroup',
			$opt         = 'usergroup',
			$nicename    = 'ユーザグループ',
			$is_override = $urls
		);

		$retvals = array(
			'urls'         => $urls,
			'overrides'    => $overrides,
			'action_name'  => 'オプション',
			'explanation'  => 'オプションの設定権限です。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'sample',
			)
		);
		return $retvals;
	}
}
