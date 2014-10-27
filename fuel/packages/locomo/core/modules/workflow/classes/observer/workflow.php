<?php
namespace Workflow;
class Observer_Workflow extends \Orm\Observer
{
	/**
	 * __construct
	 */
	public function __construct($class)
	{
	}

	/**
	 * after_load()
	 */
	public function after_load(\Orm\Model $obj)
	{
		if($obj->workflow_status == 'in_progress'):
			\Session::set_flash('success','承認進行中の項目です');
		endif;

		if($obj->workflow_status == 'before_progress'):
			\Session::set_flash('success','承認申請待ちの項目です');
		endif;
	}

	/**
	 * before_insert()
	 */
	public function before_insert(\Orm\Model $obj)
	{
		//ワークフロー管理下のコンテンツのworkflow_statusはbefore_progressで作成される
		$obj->workflow_status = 'before_progress';
	}

	/**
	 * before_save()
	 */
	public function before_save(\Orm\Model $obj)
	{
	}
}
