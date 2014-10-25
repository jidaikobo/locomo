<?php
namespace Sample;
class Model_Belongsto extends \Locomo\Model_Base
{
	protected static $_table_name = 'belongsto';

	protected static $_properties = array(
		'id',
		'name',
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
		$form = \Fieldset::forge('belongsto', \Config::get('form'));

		//name
		$form->add(
			'name',
			'BELONGSTO NAME',
			array('type' => 'text')
		)
		//->add_rule('required')
		//->add_rule('max_length', 50)
		->set_value(@$obj->name);

		return $form;
	}


}
