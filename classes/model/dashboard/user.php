<?php
namespace Locomo;
class Model_Dashboard_User extends Model_Base
{
	protected static $_table_name = 'lcm_usrs';
	protected static $_properties = array(
		'id'
	);

	/**
	 * $_has_many
	 */
	protected static $_has_many = array(
		'dashboard' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Dashboard',
			'key_to' => 'user_id',
			'cascade_save' => true,
			'cascade_delete' => false,
//			'conditions' => array('where'=>array(array('position',1))),
		),
	);

	/**
	 * find()
	 * to find admins who are not in users table
	 */
	public static function find($id = NULL, array $options = array())
	{
		$retvals = parent::find($id, $options);
		if ($retvals) return $retvals;

		// admin
		return \Model_Dashboard_Admin::find('first', array('where' => array(array('username'=>\Auth::get('username')))));
	}

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory = 'user', $obj = null)
	{
		// parent
		$form = parent::form_definition($factory, $obj);

		// widgets
		$widgets = array('' => '選択してください');
		foreach (\Util::get_mod_or_ctrl() as $k => $v)
		{
			if ( ! $widget = \Arr::get($v, 'widgets')) continue;

			$tmps = array();

			// auth
			foreach ($widget as $kk => $vv)
			{
				if(\Auth::instance()->has_access($vv['uri']))
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

		// actions
		\Model_Dashboard::$_properties['action']['form']['options'] =$widgets ;
		$fieldset = \Fieldset::forge('dashboard');
		$fieldset->set_tabular_form('Model_Dashboard', 'dashboard', $obj, 3);

		$form->add_before($fieldset, 'ダッシュボードウィジェット', array(), array(), 'submit');

		return $form;
	}
}
