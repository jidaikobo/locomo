<?php
namespace Revision;
class Observer_Revision extends \Orm\Observer
{
	/**
	 * __construct
	 */
	public function __construct($class)
	{
	}

	/**
	 * after_insert()
	 */
	public function after_insert(\Orm\Model $obj)
	{
		$this->after_save($obj);
	}

	/**
	 * after_save()
	 */
	public function after_save(\Orm\Model $obj)
	{
		//$objしたものをそのままserialize()するとunserialize()したときに__PHP_Incomplete_Classになってしまうので、いったん別のobjectにする。
		$tmps = $this->convert_model_to_simple_obj($obj);

		//prepare args
		$primary_key = $obj->get_primary_keys('first');
		$args = array();
		$args['model']       = get_class($obj);
		$args['pk_id']       = $obj->$primary_key;
		$args['data']        =  serialize($tmps);
		$args['comment']     = \Input::post('revision_comment') ?: '';
		$args['created_at']  = date('Y-m-d H:i:s');
		$args['modifier_id'] = isset($obj->modifier_id) ? $obj->modifier_id : 0;

		//save revision
		$model = \Revision\Model_Revision::forge($args);
		$model->insert_revision();
	}

	/**
	 * convert_model_to_simple_obj($obj)
	 */
	public function convert_model_to_simple_obj($obj)
	{
		$tmp = (object) array();
		if(is_array($obj)){
//			$tmps[] = $this->convert_model_to_simple_obj($obj);
		}

		//ORMを参照する - thx tuskitsume
		$model_name = get_class($obj);
		$form = $model_name::form_definition('revision');
		foreach($form->get_fields() as $property => $v):
			$tmp->{$property} = $obj->{$property};
		endforeach;

		return $tmp;
	}
}
