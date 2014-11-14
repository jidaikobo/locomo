<?php
namespace Office;
\Module::load('supportcontribute');
class Model_Supporter extends \Office\Model_Office
{
	public static $_conditions = array(
		'where' => array(
			array('is_support', 1),
		),
	);

	public static $_option_options = array();

	protected static $_belongs_to = array(
		'subject' => array(
			'key_from' => 'subject_id',
			'model_to' => '\Office\Model_Supporter_Subject',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),

		'customer' => array(
			'key_from' => 'customer_id',
			'model_to' => '\Customer\Model_Customer',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)

	);
	public static function form_definition($factory, $obj = null)
	{
		$form = parent::form_definition($factory, $obj);

		$form->add('is_support', '', array('type' => 'hidden'))->set_value(true);

		return $form;
	}

}
