<?php
class Model_Item extends \Locomo\Model_Base
{
//	use \Workflow\Traits_Model_Workflow;

	protected static $_table_name = 'items';
	public static $_subject_field_name = 'SOME_TRAITS_USE_SUBJECT_FIELD_NAME';

	protected static $_properties = array(
		'id',
		'category',
		'sub_category',
		'name',
		'data',
		'seq',
		'is_memo',
		'is_status',
		'created_at',
		'expired_at',
		'deleted_at',

// 'workflow_status',
	);

	protected static $_depend_modules = array();

	/**
	 * $_option_options - see sample at \User\Model_Usergroup
	 */
	public static $_option_options = array();

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
		if(static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;

		//forge
		$form = \Fieldset::forge($factory, \Config::get('form'));
/*
		//user_name
		$val->add('name', 'サンプル')
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',))
			->add_rule('unique', "users.user_name.{$id}");
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length', 255)
			->add_rule('unique', "users.email.{$id}");
*/
		//customer_name
		$form->add(
			'name',
			'顧客名',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->name);

		//customer_kana
		$form->add(
			'kana',
			'顧客名カタカナ',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->kana);

		//user_type
		$form->add(
			'user_type',
			'ユーザー区分',
			array('type' => 'text', 'size' => 25)
		)
		->add_rule('max_length', 25)
		->set_value(@$obj->user_type);

		//sex
		$form->add(
			'sex',
			'性別',
			array('type' => 'text', 'size' => 10)
		)
		->add_rule('max_length', 10)
		->set_value(@$obj->sex);

		//birthday_at
		$form->add(
			'birthday_at',
			'生年月日',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d'))
		)
		->set_value(@$obj->birthday_at);

		//representative
		$form->add(
			'representative',
			'代表者名',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->representative);

		//person_in_charge
		$form->add(
			'person_in_charge',
			'担当者名',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->person_in_charge);

		//area_type
		$form->add(
			'area_type',
			'地域区分',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->area_type);

		//zip
		$form->add(
			'zip',
			'郵便番号',
			array('type' => 'text', 'size' => 8)
		)
		->add_rule('max_length', 8)
		->set_value(@$obj->zip);

		//address
		$form->add(
			'address',
			'住所',
			array('type' => 'text', 'size' => 30)
		)
		->set_value(@$obj->address);

		//tel
		$form->add(
			'tel',
			'電話番号',
			array('type' => 'text', 'size' => 16)
		)
		->add_rule('max_length', 16)
		->set_value(@$obj->tel);

		//fax
		$form->add(
			'fax',
			'fax番号',
			array('type' => 'text', 'size' => 16)
		)
		->add_rule('max_length', 16)
		->set_value(@$obj->fax);

		//mobile_phone
		$form->add(
			'mobile_phone',
			'携帯電話番号',
			array('type' => 'text', 'size' => 16)
		)
		->add_rule('max_length', 16)
		->set_value(@$obj->mobile_phone);

		//email
		$form->add(
			'email',
			'email',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->email);

		//company_name
		$form->add(
			'company_name',
			'勤務先名',
			array('type' => 'text', 'size' => 30)
		)
		->set_value(@$obj->company_name);

		//company_zip
		$form->add(
			'company_zip',
			'勤務先郵便番号',
			array('type' => 'text', 'size' => 8)
		)
		->add_rule('max_length', 8)
		->set_value(@$obj->company_zip);

		//company_address
		$form->add(
			'company_address',
			'勤務先住所',
			array('type' => 'text', 'size' => 30)
		)
		->set_value(@$obj->company_address);

		//company_tel
		$form->add(
			'company_tel',
			'勤務先電話番号',
			array('type' => 'text', 'size' => 16)
		)
		->add_rule('max_length', 16)
		->set_value(@$obj->company_tel);

		//company_fax
		$form->add(
			'company_fax',
			'勤務先fax番号',
			array('type' => 'text', 'size' => 16)
		)
		->add_rule('max_length', 16)
		->set_value(@$obj->company_fax);

		//company_email
		$form->add(
			'company_email',
			'勤務先email',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->company_email);

		//volunteer_insurance_type
		$form->add(
			'volunteer_insurance_type',
			'ボランティア保険',
			array('type' => 'text', 'size' => 15)
		)
		->add_rule('max_length', 15)
		->set_value(@$obj->volunteer_insurance_type);

		//dm_issue_type
		$form->add(
			'dm_issue_type',
			'dm発行区分',
			array('type' => 'text', 'size' => 10)
		)
		->add_rule('max_length', 10)
		->set_value(@$obj->dm_issue_type);

		//dm_zip
		$form->add(
			'dm_zip',
			'dm郵便番号',
			array('type' => 'text', 'size' => 8)
		)
		->add_rule('max_length', 8)
		->set_value(@$obj->dm_zip);

		//dm_address
		$form->add(
			'dm_address',
			'dm住所',
			array('type' => 'text', 'size' => 30)
		)
		->set_value(@$obj->dm_address);

		//dm_name_1
		$form->add(
			'dm_name_1',
			'dm宛名1',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->dm_name_1);

		//dm_name_2
		$form->add(
			'dm_name_2',
			'dm宛名2',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->dm_name_2);

		//dm_tel
		$form->add(
			'dm_tel',
			'dm電話番号',
			array('type' => 'text', 'size' => 16)
		)
		->add_rule('max_length', 16)
		->set_value(@$obj->dm_tel);

		//memo
		$form->add(
			'memo',
			'備考',
			array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;')
		)
		->set_value(@$obj->memo);

		//status
		$form->add(
			'status',
			'ステータス',
			array('type' => 'hidden')
		)
		->add_rule('max_length', 20)
		->set_value(@$obj->status);

		//is_death
		$form->add(
			'is_death',
			'死亡フラグ',
			array('type' => 'checkbox', 'options' => array(0, 1))
		)
		->set_value(@$obj->is_death);

		//sys_date_at
		$form->add(
			'sys_date_at',
			'システム申請日',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))
		)
		->set_value(@$obj->sys_date_at);

		//sys_name
		$form->add(
			'sys_name',
			'システム申請者名',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->sys_name);

		//sys_position
		$form->add(
			'sys_position',
			'システム申請部署',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->sys_position);

		//sys_sub_name
		$form->add(
			'sys_sub_name',
			'システム代理申請者名',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->sys_sub_name);

		//sys_wf_status
		$form->add(
			'sys_wf_status',
			'システムWFステータス',
			array('type' => 'text', 'size' => 20)
		)
		->add_rule('max_length', 20)
		->set_value(@$obj->sys_wf_status);

		//created_at
		$form->add(
			'created_at',
			'created_at',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))
		)
		->set_value(isset($obj->created_at) ? $obj->created_at : date('Y-m-d H:i:s'));

		//expired_at
		$form->add(
			'expired_at',
			'expired_at',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))
		)
		->set_value(@$obj->expired_at);

		//deleted_at
		$form->add(
			'deleted_at',
			'deleted_at',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))
		)
		->set_value(@$obj->deleted_at);



		static::$_cache_form_definition = $form;
		return $form;
	}
}

