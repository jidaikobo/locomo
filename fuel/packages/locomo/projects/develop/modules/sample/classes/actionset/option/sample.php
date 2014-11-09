<?php
namespace Sample;
class Actionset_Option_Sample extends \Locomo\Actionset_Option
{
	use \Locomo\Actionset_Traits_Testdata;

	/**
	 * actionset_sample()
	 */
	public static function actionset_sample($module, $obj, $id, $urls = array())
	{
		//urls
		$actions = array(array("/user/sample/", 'オプション'));
		$urls = static::generate_anchors('user', 'sample', $actions, $obj);

		//overrides
		$overrides = static::generate_bulk_anchors(
			$module      = 'sample',
			$model       = 'sample',
			$opt         = 'sample',
			$nicename    = 'オプション',
			$is_override = $urls
		);

		$retvals = array(
			'urls'         => $urls,
			'overrides'    => $overrides,
			'action_name'  => 'オプション',
			'explanation'  => 'オプションの設定権限です。',
			'order'        => 10,
			'dependencies' => array(
				'sample',
			)
		);
		return $retvals;
	}
}
