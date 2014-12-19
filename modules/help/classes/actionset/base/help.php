<?php
namespace Help;
class Actionset_Base_Help extends \Actionset
{
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
	 * create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		$qstr = static::generate_qstr($obj, 'action');
		$actions = array(array($controller.DS."create".$qstr, '新規作成'));
		$urls = static::generate_urls($controller.DS.'create', $actions, ['create']);

		$retvals = array(
			'urls'         => $urls,
			'order'        => 10,
		);

		return $retvals;
	}

	/**
	 * view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		if ( ! in_array(\Request::main()->action, ['index_admin', 'create']))
		{
			$qstr = static::generate_qstr($obj, 'searches[action]');
			$actions = array(array($controller.DS."index_admin".$qstr, '閲覧'));
			$urls = static::generate_urls($controller.DS.'create', $actions, ['create']);
		}

		$retvals = array(
			'urls'         => $urls,
			'order'        => 10,
		);

		return $retvals;
	}
}
