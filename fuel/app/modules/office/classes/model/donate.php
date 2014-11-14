<?php
namespace Office;
\Module::load('customer');
class Model_Support extends \Office\Model_Office
{
//	use \Workflow\Traits_Model_Workflow;


	public static $_conditions = array(
		'where' => array(
			array('is_support' , 0),
		),
	);

	protected static $_belongs_to = array(
		'subject' => array(
			'key_from' => 'subject_id',
			'model_to' => 'Support\Model_Subject',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				array('is_support', 0)
			),
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

		$form->add('is_support', '', array('type' => 'hidden'))->set_value(false);

		return $form;
	}
}
