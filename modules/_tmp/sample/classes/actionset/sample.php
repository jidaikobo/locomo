<?php
namespace Sample;
class Actionset_Sample extends \Actionset_Base
{
	use \Actionset_Traits_Testdata;

	/**
	 * actionset_sample()
	 */
	public static function actionset_sample($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."sample", 'オプション'));
		$urls = static::generate_urls($controller.'::action_create', $actions);

		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'action_name'  => 'オプション',
			'explanation'  => 'オプションの設定権限です。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'::action_sample',
			)
		);
		return $retvals;
	}

}
