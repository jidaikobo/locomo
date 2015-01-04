<?php
namespace Sample;
class Actionset_Option_Sample extends \Locomo\Actionset_Option
{
	use \Actionset_Traits_Option_Testdata;

	/**
	 * actionset_sample()
	 */
	public static function actionset_sample($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."sample", 'オプション'));
		$urls = static::generate_urls($controller.DS.'create', $actions);

		$retvals = array(
			'urls'         => $urls,
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
