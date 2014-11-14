<?php
namespace Customer;
class Controller_Customer extends \Locomo\Controller_Crud
{
	//trait
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;
	protected $test_datas = array(
		'name'    => 'text',
		'kana' => 'text',
		'user_type'     => 'text:test',
		'volunteer_insurance_type'        => 'text:test',
		'dm_address'        => 'text:test',
		'dm_issue_type'        => 'text:test',
		'is_death'        => 'bool',
		'status'       => 'text:public',
		'creator_id'   => 'int',
		'modifier_id'  => 'int',
	);

	public function action_edit($id = null) {
		\Asset::js('customer/edit.js', array(), 'js_group');
		parent::action_edit($id);
	}

}
