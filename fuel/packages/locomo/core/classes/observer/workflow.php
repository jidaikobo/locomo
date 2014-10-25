<?php
namespace Locomo;
class Observer_Workflow extends \Orm\Observer
{
	public static $properties = array('workflow_status');
	protected $_properties;

	public static $workflow_status_filed = 'workflow_status';
	protected $_workflow_status_filed;

	/**
	 * __construct
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_properties = isset($props['properties']) ? $props['properties'] : static::$properties;

		$this->_workflow_status_filed = isset($props['workflow_status_filed']) ?
			$props['workflow_status_filed'] :
			static::$workflow_status_filed;
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
/*

		//日付の形式に
		foreach($this->_properties as $property):
			$value = \Input::post($property);
			if ( ! empty($value)):
				$value = strtotime($value);
				$obj->{$property} = date('Y-m-d H:i:s', $value);
//				$obj->{$property} = $value >= 253402268398 ? $maxdate : date('Y-m-d H:i:s', $value);
			endif;
		endforeach;

		//期限切れは特別扱い。空の場合は、unixtimeの上限を入れる
		if(in_array($this->_expired_filed, $this->_properties)):
			if (empty($obj->{$property})):
				$obj->expired_at = null ;
	//			$obj->expired_at = \Input::post($this->_expired_filed) ?: $maxdate ;
			endif;
		endif;
*/
	}
}
