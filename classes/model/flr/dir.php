<?php
namespace Locomo;
class Model_Flr_Dir extends \Model_Base
{
	/**
	 * vals
	 */
	protected static $_table_name = 'lcm_flrs';

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'name' => array(
			'label' => 'ディレクトリ名',
			'form' => array('type' => 'text', 'size' => 20, 'class' => 'name'),
			'validation' => array(
				'required',
				'max_length' => array(255),
				'valid_string' => array(array('alpha','numeric','dot','dashes')),
			),
		),
		'path' => array(
			'label' => '物理パス',
			'form' => array('type' => 'hidden'),
		),
		'is_visible' => array(
			'label' => '可視属性',
			'form' => array(
				'type' => 'hidden',
				'options' => array('0' => '不可視', '1' => '可視')
			),
			'default' => 1,
			'validation' => array(
				'required',
			),
		),
		'expired_at' => array('form' => array('type' => false), 'default' => null),
		'creator_id' => array('form' => array('type' => false), 'default' => -1),
		'updater_id' => array('form' => array('type' => false), 'default' => -1),
		'created_at' => array('form' => array('type' => false), 'default' => null),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
	);

	/**
	 * $_has_many
	 */
	protected static $_has_many = array(
		'permission_usergroup' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Flr_Usergroup',
			'key_to' => 'flr_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'permission_user' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Flr_User',
			'key_to' => 'flr_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	/**
	 * $_soft_delete
	 */
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	/**
	 * $_observers
	 */
	protected static $_observers = array(
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Expired' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
	);

	/**
	 * form_definition()
	 */
	public static function form_definition($factory = 'form', $obj = null)
	{
		$id = isset($obj->id) ? $obj->id : '';

		// forge
		$form = parent::form_definition($factory, $obj);

		// create or move
		if (in_array(\Request::active()->action, array('create_dir', 'move_dir')))
		{
			$form = static::directory_list($form, $obj);
		}

		// create or rename
		if (in_array(\Request::active()->action, array('create_dir', 'rename_dir')))
		{

		}

		// permission
		if (in_array(\Request::active()->action, array('permission_dir')))
		{
			$form = static::permission($form, $obj);
		}



		return $form;
	}

	/**
	 * directory_list()
	 */
	public static function directory_list($form, $obj)
	{
		// list of upload directories - for choose parent dir.
		$current_dir = @$obj->path ?: '';
		$current_dir = $current_dir ? rtrim($current_dir, '/').DS : '';
		$selected = $current_dir ? dirname($current_dir).DS : '' ;
		$dirs = \Util::get_file_list(LOCOMOUPLADPATH, $type = 'dir');
		$options = array();
		foreach ($dirs as $dir)
		{
			// is exist on database
			if( ! \Model_Flr::find('first', array('where' => array(array('path', $dir)))) && $dir != LOCOMOUPLADPATH) continue;

			// cannot choose myself and children
			if ($current_dir && substr($dir, 0, strlen($current_dir)) == $current_dir) continue;
		
			// check auth
			if ( ! \Controller_Flr::check_auth($dir)) continue;

			$options[$dir] = substr($dir, strlen(LOCOMOUPLADPATH) - 1);
		}
		$form->add_after(
				'parent',
				'親ディレクトリ',
				array('type' => 'select', 'options' => $options, 'style' => 'width: 10em;'),
				array(),
				'name'
			)
			->set_value($selected);

		return $form;
	}

	/**
	 * permission()
	 */
	public static function permission($form, $obj)
	{
		// usergroup_id
		$options = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true))), 'name');
		$options = array(''=>'選択してください', '0' => 'ゲスト') + $options;
		\Model_Flr_Usergroup::$_properties['usergroup_id']['form'] = array(
			'type' => 'select',
			'options' => $options,
			'class' => 'varchar usergroup',
		);
		$usergroup_id = \Fieldset::forge('permission_usergroup')->set_tabular_form('\Model_Flr_Usergroup', 'permission_usergroup', $obj, 2);
		$form->add($usergroup_id);

		// user_id
		$options = array(''=>'選択してください') + \Model_Usr::get_options(array(), 'display_name');
		\Model_Flr_User::$_properties['user_id']['form'] = array(
			'type' => 'select',
			'options' => $options,
			'class' => 'varchar user',
		);
		$user_id = \Fieldset::forge('permission_user')->set_tabular_form('\Model_Flr_User', 'permission_user', $obj, 2);
		$form->add($user_id);

		return $form;
	}
}
