<?php
namespace Sample;
class Model_Sample extends \Kontiki\Model_Crud
{
	protected static $_table_name = 'samples';
	protected static $_primary_name = '';
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);


	protected static $_properties = array(
		'id',
		'name',
		'belongsto_id',
		'created_at',
		'expired_at',
		'deleted_at',

// 'workflow_status',
	);

	protected static $_depend_modules = array(
	);
	protected static $_belongs_to = array(
		'belongsto' => array(
			'key_from' => 'belongsto_id',
			'model_to' => '\Sample\Model_Belongsto',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);

	protected static $_has_one = array(
		'hasone' => array(
			'key_from' => 'id',
			'model_to' => '\Sample\Model_Hasone',
			'key_to' => 'sample_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);

	protected static $_has_many = array(
		'hasmany' => array(
			'key_from' => 'id',
			'model_to' => '\Sample\Model_Hasmany',
			'key_to' => 'sample_id',
			'cascade_save' => true,
			'cascade_delete' => false
		)
	);

	protected static $_many_many = array(
		'manymany' => array(
			'key_from' => 'id',
			'key_through_from' => 'sample_id',
			'table_through' => 'samples_manymany',
			'key_through_to' => 'manymany_id',
			'model_to' => '\Sample\Model_Manymany',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	//observers
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
		'Kontiki\Observer\Expired' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
	);

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory, $obj = null, $id = '')
	{
		$form = \Fieldset::forge('sample', \Config::get('form'));
/*
		//user_name
		$val->add('name', 'サンプル')
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',))
			->add_rule('unique', "users.user_name.{$id}");
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length', 255)
			->add_rule('unique', "users.email.{$id}");
*/
		//name
		$form->add(
			'name',
			'samples表題',
			array('type' => 'text', 'rows' => 7, 'style' => 'width:100%;')
		)
		->add_rule('required')
		->add_rule('max_length', 50)
		->set_value(@$obj->name);

		//belongsto_id
		$form->add(
			'belongsto_id',
			'BELONGSTO ID',
			array('type' => 'text', 'size' => 30)
		)
		->set_value(@$obj->belongsto_id);

		// hasone フォーム
		$ho_form = Model_Hasone::form_definition('hasone', $obj->hasone ?: $obj->hasone = Model_Hasone::forge())->populate($obj->hasone);
		$ho_form->set_input_name_array();
		//$form->add( $ho_form );

		// belongsto フォーム
		$bt_form = Model_Belongsto::form_definition('belongsto', $obj->belongsto ?: $obj->belongsto = Model_Belongsto::forge())->populate($obj->belongsto);
		$bt_form->set_input_name_array();
		//$form->add( $bt_form );

		// hasmany フォーム
		//$form->add(\Fieldset::forge('hasmany')->set_tabular_form('Sample\Model_Hasmany', 'hasmany', $obj, 3));



		// manymany checkbox
		$manymany_option = Model_Manymany::find('all', array('select' => array('name')));
		$manymany_option = \Arr::assoc_to_keyval($manymany_option, 'id', 'name');
		$form->add(
			'manymany',
			'',
				array('type' => 'checkbox', 'options' => $manymany_option)
			)
			->set_value(array_keys($obj->manymany));


		return $form;
	}
}
