<?php
namespace Sample;
class Model_Hasone extends \Kontiki\Model_Crud
{
	protected static $_table_name = 'hasone';

	protected static $_properties = array(
		'id',
		'name',
		'sample_id',
		'created_at',
		'expired_at',
		'deleted_at',
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
		$form = \Fieldset::forge('hasone', \Config::get('form'));

		//name
		$form->add(
			'name',
			'HASONE NAME',
			array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;')
		)
		->add_rule('required')
		->add_rule('max_length', 50)
		->set_value(@$obj->name);



		return $form;
	}

}


