<?php
namespace Kontiki;
abstract class Controller_Usergroup extends \Kontiki\Controller_Crud
{
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
