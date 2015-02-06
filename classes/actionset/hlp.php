<?php
namespace Locomo;
class Actionset_Hlp extends \Actionset
{
	use \Actionset_Traits_Revision;

	/**
	 * generate_qstr()
	 */
	private static function generate_qstr($obj = null, $key = null)
	{
		// なるべく引き回す
		$qstr = \Input::get('searches.action') ? \Input::get('searches.action') : '' ;
		$qstr = ! $qstr ? \Input::get('action') : $qstr ;
		if(is_object($obj) && property_exists($obj, 'action') && ! $qstr)
		{
			$qstr = $obj->action;
		}
		$qstr = $qstr ? '?'.$key.'='.urlencode(urldecode($qstr)) : '' ;
		return $qstr;
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		$qstr = static::generate_qstr($obj, 'action');
		$actions = array(array($controller.DS."edit".$qstr, '編集'));
		$urls = static::generate_urls($controller.'::action_create', $actions, ['edit']);

		$retvals = array(
			'urls'  => $urls,
			'order' => 10,
			'action_name' => '編集',
			'acl_exp' => 'ヘルプの編集権限です。',
			'dependencies' => array(
				$controller.'::action_edit',
			)
		);

		return $retvals;
	}

	/**
	 * view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		if ( ! in_array(\Request::main()->action, ['view', 'edit']))
		{
			$qstr = static::generate_qstr($obj, 'searches[action]');
			$actions = array(array($controller.DS."view".$qstr, '閲覧'));
			$urls = static::generate_urls($controller.'::action_create', $actions, ['edit']);
		}

		$retvals = array(
			'urls'  => $urls,
			'order' => 10,
		);

		return $retvals;
	}
}
