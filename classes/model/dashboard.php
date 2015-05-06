<?php
namespace Locomo;
class Model_Dashboard extends Model_Base
{
	protected static $_table_name = 'lcm_dashboards';

	protected static $_conditions = array(
		'order_by' => array(array('seq', 'ASC')),
	);
	public static $_options = array();


	public static $_properties = array(
		'id',
		'user_id' => array(
			'label' => 'ユーザID',
			'form' => array(
				'type' => false
			)
		),
		'action' => array(
			'label' => 'アクション',
			'form' => array(
				'type' => 'select',
				'options' => array(),
			),
		),
		'size' => array(
			'label' => 'サイズ',
			'form' => array(
				'type' => 'select',
				'options' => array(''=>'サイズ', '3'=>'大', '2'=>'中', '1'=>'小'),
			),
		),
		'seq' => array(
			'label' => '順序',
			'form' => array(
				'type' => 'text',
				'attribute' => array('size' => '3'),
			),
		),
	);

	/**
	* _init()
	*/
	public static function _init()
	{
		// widgets
		$widgets = array('' => '選択してください');
		foreach (\Util::get_mod_or_ctrl() as $k => $v)
		{
			if ( ! $widget = \Arr::get($v, 'widgets')) continue;

			$tmps = array();

			// auth
			foreach ($widget as $kk => $vv)
			{
				if(\Auth::has_access($vv['uri']))
				{
					$tmps[$vv['uri']] = $vv['name'];
				}
			}
			if (empty($tmps)) continue;

			// values
			$key = \Arr::get($v, 'nicename');
			$widgets[$key] = \Arr::get($widgets, $key) ?: array();
			$widgets[$key] = $tmps;
		}
		static::$_properties['action']['form']['options'] = $widgets ;
	}
}

