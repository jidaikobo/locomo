<?php
namespace Locomo;
class Controller_Scdl_Admin extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Testdata;
	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'main_controller'  => '\Controller_Scdl',
		'no_acl' => true,
	);

	// model
	protected $model_name = '\Model_Scdl';

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		$this->model_name = static::$controller == "\Controller_Scdl_Admin" ? '\Model_Scdl' : '\Reserve\Model_Reserve';
		$model = $this->model_name;

		\Model_Scdl::$_options = array(
			'where' => array(
				array('kind_flg', $model::$_kind_flg),
			),
			'order_by' => array('id' => 'desc')
		);
		parent::index_admin();
		
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible()
	{
		parent::index_invisible();
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		parent::index_deleted();
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all()
	{
		parent::index_all();
	}
}
