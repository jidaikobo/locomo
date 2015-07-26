<?php
class Presenter_Flr_Dir_Permission extends \Presenter_Base
{
	/**
	 * form()
	 */
	public static function form($obj = NULL)
	{
		$form = parent::form($obj);

		// hidden current name
		$form->field('name')->set_type('hidden');
		$form->add_after('display_name', 'ディレクトリ名', array('type' => 'textarea', 'class' => 'textarea', 'style' => 'height: 3.5em', 'disabled' => 'disabled'),array(), 'name')->set_value(@$obj->name);

		// delete is form
		$form->delete('is_sticky');
		$form->field('explanation')->set_type('hidden');

		// opener
		$form->add_before('div_opener', '', array('type' => 'text'),array(), 'display_name')->set_template('<div class="input_group">');
		$form->add_after('div_closer', '', array('type' => 'text'),array(), 'display_name')->set_template('</div>');

		// === usergroup_id ===
		$options = \Model_Usrgrp::find_options('name', array('where' => array(array('is_available', true), array('customgroup_uid', 'is', null))));
		$options = array('-10' => 'ログインユーザすべて', '0' => 'ゲスト') + $options;

		// $formset
		$options = array(''=>'選択してください') + $options;
		$formset = array('type' => 'select', 'options' => $options, 'class' => 'varchar usergroup', 'style' => 'width: 12em;');

		// \Model_Flr::$_properties_cachedに値を足すのは少々奇怪だが、static::get_parent()で関係テーブルにアクセスすると、モデルの初期状態でキャッシュされるため、これを上書きしないと、Model::properties()がキャッシュしか返さないので。
		\Model_Flr_Usergroup::$_properties['usergroup_id']['form'] = $formset;
		if (isset(\Model_Flr::properties_cached()['Locomo\Model_Flr_Usergroup']['usergroup_id']['form']))
		{
			$arr = array('Locomo\Model_Flr_Usergroup' => array('usergroup_id' => array('form' => $formset)));
			\Model_Flr::set_properties_cached($arr);
		}
		$usergroup_id = \Fieldset::forge('permission_usergroup')->set_tabular_form('\Model_Flr_Usergroup', 'permission_usergroup', $obj, 2);
		$form->add_after($usergroup_id, 'ユーザグループ権限', array(), array(), 'explanation');

		// === user_id ===
		$options = \Model_Usr::find_options('display_name');

		// $formset
		$options = array(''=>'選択してください') + $options;
		$formset = array('type' => 'select', 'options' => $options, 'class' => 'varchar usergroup', 'style' => 'width: 12em;');

		\Model_Flr_User::$_properties['user_id']['form'] = $formset;
		if (isset(\Model_Flr::properties_cached()['Locomo\Model_Flr_User']['user_id']['form']))
		{
			$arr = array('Locomo\Model_Flr_User' => array('user_id' => array('form' => $formset)));
			\Model_Flr::set_properties_cached($arr);
//			\Model_Flr::$_properties_cached['Locomo\Model_Flr_User']['user_id']['form'] = $formset;
		}
		$user_id = \Fieldset::forge('permission_user')->set_tabular_form('\Model_Flr_User', 'permission_user', $obj, 2);
		$form->add_after($user_id, 'ユーザ権限', array(), array(), 'permission_usergroup');

		return $form;
	}
}
