<?php
namespace Customer;
class Model_Customer extends \Locomo\Model_Base
{
//	use \Workflow\Traits_Model_Workflow;

	protected static $_table_name = 'customers';
	public static $_subject_field_name = 'SOME_TRAITS_USE_SUBJECT_FIELD_NAME';

	protected static $_properties = array(
		'id',
		'name',
		'kana',
		'user_type',
		'sex',
		'birthday_at',
		'representative',
		'person_in_charge',
		'area_type',
		'zip',
		'address',
		'tel',
		'fax',
		'mobile_phone',
		'email',
		'company_name',
		'company_zip',
		'company_address',
		'company_tel',
		'company_fax',
		'company_email',
		'volunteer_insurance_type',
		'dm_issue_type',
		'dm_zip',
		'dm_address',
		'dm_name_1',
		'dm_name_2',
		'dm_tel',
		'memo',
		'status',
		'is_death',
		'sys_date_at',
		'sys_name',
		'sys_position',
		'sys_sub_name',
		'sys_wf_status',

		'created_at',
		'updated_at',
		'expired_at',
		'deleted_at',

// 'workflow_status',
	);



	public static $_type_config = array(
		'user_type' => array(
			'個人' => '個人',
			'団体等' => '団体等',
		),
		'area_type' => array(
			'市内' => '市内',
			'府内' => '府内',
			'他府県' => '他府県',
		),
		'volunteer_insurance_type' => array(
			'加入' => '加入',
			'未加入' => '未加入',
		),
		'dm_issue_type' => array(
			'自宅／所在地' => '自宅／所在地',
			'勤務先' => '勤務先',
		),
	);

	protected static $_depend_modules = array();

	/**
	 * $_option_options - see sample at \User\Model_Usergroup
	 */
	public static $_option_options = array();

	protected static $_has_many = array(
	);


	protected static $_many_many = array(
		// ユーザー区分個人
		'personal_group' => array(
			'key_from' => 'id',
			'key_through_from' => 'id',
			'table_through' => 'customers_items',
			'model_to' => '\Customer\Model_Item',
			'key_through_to' => 'id',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				array('category', 'LIKE', '個人'),
			),
		),
		// ユーザー区分団体等
		'common_group' => array(
			'key_from' => 'id',
			'key_through_from' => 'id',
			'table_through' => 'customers_items',
			'model_to' => '\Customer\Model_Item',
			'key_through_to' => 'id',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				array('category', 'LIKE', '団体'),
			),
		),

		// 関係部署
		'post' => array(
			'key_from' => 'id',
			'key_through_from' => 'customer_id',
			'table_through' => 'customers_posts',
			'model_to' => '\Model_Post',
			'key_through_to' => 'post_id',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),

	);


/*
	protected static $_belongs_to = array(
		'foo' => array(
						'key_from' => 'foo_id',
						'model_to' => 'Model_Foo',
						'key_to' => 'id',
						'cascade_save' => true,
						'cascade_delete' => false,
					)
	);
*/

	// todo observer 個人団体のチェック
	// 選択に応じて外す


/*
	//observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Expired' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
//		'Workflow\Observer_Workflow' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
	);
*/

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

/*
 * main
 */
		//is_death
		$form->add(
			'is_death',
			'死亡フラグ',
			array('type' => 'checkbox', 'value' => 1)
		)
		->set_value(@$obj->is_death);

		//customer_name
		$form->add(
			'name',
			'顧客名',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('required')
		->add_rule('max_length', 50)
		->set_value(@$obj->name);

		//customer_kana
		$form->add(
			'kana',
			'顧客名カタカナ',
			array('type' => 'text', 'size' => 50)
		)
		->add_rule('required')
		->add_rule('max_length', 50)
		->set_value(@$obj->kana);


		//user_type
		$form->add(
			'user_type',
			'ユーザー区分',
			array('type' => 'radio', 'size' => 25, 'options' => static::$_type_config['user_type'], 'default' => '個人')
		)
		->add_rule('required')
		->add_rule('max_length', 25)
		->set_value(!is_null($obj->user_type) ? $obj->user_type : '個人');



		$group = \Customer\Model_Item::get_options(array('where' => array(array('category', 'LIKE', '%個人%'))), 'name');
		$form->add('personal_group', 'ユーザーグループ個人', array('type' => 'checkbox', 'options' => $group))
			->set_template("\t\t<tr id=\"personal_group_tab\">\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n")
			->set_value(array_keys($obj->personal_group));
		$group = \Customer\Model_Item::get_options(array('where' => array(array('category', 'LIKE', '%団体%'))), 'name');
		$form->add('common_group', 'ユーザーグループ団体等', array('type' => 'checkbox', 'options' => $group))
			->set_template("\t\t<tr id=\"common_group_tab\">\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n")
			->set_value(array_keys($obj->common_group));


		// var_dump($obj->post);

		$form->add('post', 'ユーザーグループ団体等', array('type' => 'checkbox', 'options' => array(
			'(仮)法人事務所' => '(仮)法人事務所',
			'(仮)情報ステーョン' => '(仮)情報ステーョン',
			'(仮)情報製作センター' => '(仮)情報製作センター',
		)))
			->set_value(array_keys($obj->post));


		//area_type
		$form->add(
			'area_type',
			'地域区分',
			array('type' => 'select', 'options' => static::$_type_config['area_type'])
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->area_type);

		//zip
		$form->add(
			'zip',
			'郵便番号',
			array('type' => 'text', 'size' => 8)
		)
		->set_template("\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} <span>{description}</span> {error_msg} <input type=\"button\" value=\"住所検索\"></td>\n\t\t</tr>\n")
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


/*
 * その他詳細
 */
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
			array('type' => 'radio', 'options' => static::$_type_config['volunteer_insurance_type'])
		)
		->add_rule('required')
		->set_value(@$obj->volunteer_insurance_type);

		//dm_issue_type
		$form->add(
			'dm_issue_type',
			'dm発行区分',
			array('type' => 'radio',  'options' => static::$_type_config['dm_issue_type'])
		)
		->add_rule('required')
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

	public static function plain_definition($factory = 'plain', $obj = null)
	{
		$form = static::form_definition($factory, $obj);
		return $form;
	}
}
