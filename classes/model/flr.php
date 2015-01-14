<?php
namespace Locomo;
class Model_Flr extends \Model_Base
{
	/**
	 * vals
	 */
	protected static $_table_name = 'lcm_flrs';
	public static $_subject_field_name = 'username';
	public static $_creator_field_name = 'id';

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'name' => array(
			'label' => 'ファイル名',
			'form' => array('type' => 'text', 'size' => 20, 'class' => 'name'),
			'validation' => array(
				'required',
				'max_length' => array(255)
			),
		),
		'password' => array(
			'label' => 'パスワード',
			'form' => array('type' => 'password', 'size' => 20, 'placeholder'=>'新規作成／変更する場合は入力してください'),
			'validation' => array(
				'min_length' => array(8),
				'max_length' => array(50),
				'match_field' => array('confirm_password'),
				'valid_string' => array(array('alpha','numeric','dot','dashes')),
			),
			'default' => '',
		),
		'path' => array(
			'label' => '物理パス',
			'form' => array('type' => 'text', 'size' => 20),
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
		'Locomo\Observer_Password' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Users' => array(
			'events' => array('before_insert', 'before_save'),
		),
	);

	/**
	 * form_definition_edit_dir()
	 */
	public static function form_definition_edit_dir($factory = 'form', $obj = null)
	{
		$id = isset($obj->id) ? $obj->id : '';

		// forge
		$form = parent::form_definition($factory, $obj);

		// list of upload directories - for choose parent dir.
		$paths = \File::read_dir(LOCOMOUPLADPATH);
//$paths = \Arr::flatten($paths, '/');
		//$paths = \Arr::flatten(\Arr::flatten($paths, '/'));


echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">' ;
var_dump( $paths ) ;
echo '</textarea>' ;
die();


		return $form;
	}

	/**
	 * form_definition()
	 */
	public static function form_definition($factory = 'form', $obj = null)
	{
//		if (static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;
		$id = isset($obj->id) ? $obj->id : '';

		//forge
		$form = parent::form_definition($factory, $obj);
		$form->field('password')->set_value('');
		$form
			->add_after(
				'upload',
				'アップロード',
				array('type' => 'file'),
				array(),
				'is_visible'
			)
			->set_template(
				'<tr><th>{label}</th><td>{field}</td></tr></table><h2>パーミッション</h2><table>'
			);

		// usergroup_id
		$options = array(''=>'選択してください') + \Model_Usrgrp::get_options(array('where' => array(array('is_available', true))), 'name');
		\Model_Flr_Usergroup::$_properties['usergroup_id']['form'] = array(
			'type' => 'select',
			'options' => $options,
			'class' => 'varchar usergroup',
		);
		$usergroup_id = \Fieldset::forge('permission_usergroup')->set_tabular_form_blank('\Model_Flr_Usergroup', 'permission_usergroup', $obj, 2);
		$form->add($usergroup_id);

		// user_id
		$options = array(''=>'選択してください') + \Model_Usr::get_options(array(), 'display_name');
		\Model_Flr_User::$_properties['user_id']['form'] = array(
			'type' => 'select',
			'options' => $options,
			'class' => 'varchar user',
		);
		$user_id = \Fieldset::forge('permission_user')->set_tabular_form_blank('\Model_Flr_User', 'permission_user', $obj, 2);
		$form->add($user_id);

		return $form;
	}
}
