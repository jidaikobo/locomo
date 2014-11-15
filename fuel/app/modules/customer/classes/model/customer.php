<?php
namespace Customer;
class Model_Customer extends \Locomo\Model_Base
{
//	use \Workflow\Traits_Model_Workflow;

	protected static $_table_name = 'customers';
	public static $_subject_field_name = 'name';

	protected static $_properties = array(
		'id',
		'is_death' => array(
			'form' => array(
				'type' => 'checkbox',
			),
		),
		'name' => array(
			'label' => '顧客名',
			'data_type' => 'varchar(50)',
			'form' => array(
				'type' => 'text',
				'size' => 50,
			),
			'validation' => array(
				'required',
				'max_length' => array(50),
			),
			'default' => '',
		),
		'kana' => array(
			'label' => '顧客名カタカナ',
			'data_type' => 'varchar(50)',
			'form' => array(
				'type' => 'text',
				'size' => 50,
			),
			'validation' => array(
				'required',
				'max_length' => array(50),
			),
			'default' => '',
		),
		'user_type' => array(
			'label' => 'ユーザー区分',
			'data_type' => 'varchar',
			'form' => array(
				'type' => 'radio',
				'options' => array(
					'個人' => '個人',
					'団体等' => '団体等',
				),
			),
			'validation' => array(
				'required',
			),
			'default' => '個人',
		),
		'area_type' => array(
			'label' => '地域区分',
			'data_type' => 'int',
			'form' => array(
				'type' => 'select',
				'options' =>  array(
					'市内' => '市内',
					'府内' => '府内',
					'他府県' => '他府県',
				),
			),
		),
		'zip' => array(
			'label' => '郵便番号',
			'data_type' => 'varchar(8)',
			'form' => array(
				'type' => 'text',
				'size' => 8,
			),
			'validation' => array(
				'required',
				'max_length' => array(8),
			),
			'default' => '',
		),
		'address' => array(
			'label' => '住所',
			'data_type' => 'varchar(50)',
			'form' => array(
				'type' => 'textarea',
				'cols' => 50,
				'rows' => 3,
			),
			'validation' => array(
				'required',
				'max_length' => array(255),
			),
			'default' => '',
		),
		'tel' => array(
			'label' => '電話番号',
			'data_type' => 'varchar(16)',
			'form' => array(
				'type' => 'text',
				'size' => 16,
			),
			'validation' => array(
				'max_length' => array(16),
			),
			'default' => '',
		),
		'fax' => array(
			'label' => 'FAX番号',
			'data_type' => 'varchar(16)',
			'form' => array(
				'type' => 'text',
				'size' => 16,
			),
			'validation' => array(
				'max_length' => array(16),
			),
			'default' => '',
		),
		'mobile_phone' => array(
			'label' => '携帯電話番号',
			'data_type' => 'varchar(16)',
			'form' => array(
				'type' => 'text',
				'size' => 16,
			),
			'validation' => array(
				'max_length' => array(16),
			),
			'default' => '',
		),
		'email' => array(
			'label' => 'E-Mail',
			'data_type' => 'varchar(50)',
			'form' => array(
				'type' => 'text',
				'size' => 50,
			),
			'validation' => array(
				'max_length' => array(50),
			),
			'default' => '',
		),
		// その他詳細
		'sex' => array(
			'label' => '性別',
			'data_type' => 'varchar',
			'form' => array(
				'type' => 'radio',
				'options' => array(
					'男' => '男',
					'女' => '女',
				),
			),
			'validation' => array(
			),
			'default' => '個人',
		),
		'birthday_at',
		'representative',
		'person_in_charge',

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


	/*
	 * relations
	 */
	protected static $_many_many = array(
		// ユーザー区分個人
		'personal_group' => array(
			'key_from' => 'id',
			'key_through_from' => 'object_id',
			'table_through' => 'customers_items_personal',
			'model_to' => '\Customer\Model_Item',
			'key_through_to' => 'devision_id',
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
			'key_through_from' => 'object_id',
			'table_through' => 'customers_items_common',
			'model_to' => '\Customer\Model_Item',
			'key_through_to' => 'devision_id',
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


	// todo observer 個人団体のチェック
	// 選択に応じて外す


	//observers
	protected static $_observers = array(
		'\\Orm\\Observer_Self' => array(),

/*
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
*/
	);

	public function _event_before_save() {
		// single checkbox
		is_null(\Input::post('is_death')) and $this->is_death = 0;
		if (\Input::post('user_type') == '個人') {
			unset($this->common_group);
		} elseif (\Input::post('user_type') == '団体等') {
			unset($this->personal_group);
		 }
	}


	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory = 'customer', $obj = null)
	{
		if(static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;

		//forge
		$form = parent::form_definition($factory, $obj);

		/*
		 * add field
		 */
		$options = \Customer\Model_Item::get_options(array('where' => array(array('category', 'LIKE', '%個人%'))), 'name');
		$form->add_after('personal_group', 'ユーザーグループ個人', array('type' => 'checkbox', 'options' => $options), array(), 'user_type')
			->set_value(array_keys($obj->personal_group));

		/*
		var_dump($obj->personal_group);
		var_dump($obj->common_group);
		 */
		$options = \Customer\Model_Item::get_options(array('where' => array(array('category', 'LIKE', '%団体%'))), 'name');
		$form->add_after('common_group', 'ユーザーグループ団体等', array('type' => 'checkbox', 'options' => $options), array(), 'personal_group')
			->set_value(array_keys($obj->common_group));

		$options = \Customer\Model_Post::get_options(array('order' => 'seq'), 'name');
		$form->add_after('post', '関係部署', array('type' => 'checkbox', 'options' => $options), array(), 'common_group')
			// ->add_rule('required') // todo validation
			->set_value(array_keys($obj->post));


		/*
		 * template set
		 */
		$form->field('zip')
			->set_template("\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} <span>{description}</span> {error_msg} <input type=\"button\" value=\"住所検索\"></td>\n\t\t</tr>\n");

		$form->field('personal_group')
			->set_template("\t\t<tr id=\"personal_group_tab\">\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n");

		$form->field('common_group')
			->set_template("\t\t<tr id=\"common_group_tab\">\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n");

		$form->add(\Config::get('security.csrf_token_key'), '', array('type' => 'hidden'))
			->set_value(\Security::fetch_token());
		$form->add('submit', '', array('type' => 'submit', 'value' => '保存', 'class' => 'button primary'));

		static::$_cache_form_definition = $form;
		return $form;
	}

	public static function plain_definition($factory = 'plain', $obj = null)
	{
		$form = static::form_definition($factory, $obj);
		return $form;
	}



}
