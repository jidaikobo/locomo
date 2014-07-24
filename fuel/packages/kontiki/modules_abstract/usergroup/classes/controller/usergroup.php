<?php
namespace Kontiki;
abstract class Controller_Usergroup_Abstract extends \Kontiki\Controller_Crud
{
	/**
	 * @var bool is_workflowed
	 */
	public $is_workflowed = true;

	/**
	 * test datas
	 * 
	 */
	protected $test_datas = array(
		'usergroup_name' => 'text',
	);

	/**
	 * action_add_testdata()
	 * 
	 */
	public function action_add_testdata($num = 10)
	{
		parent::action_add_testdata($num);
	}
}
