<?php
namespace Publication;
class Model_Bookincome extends \Locomo\Model_Base
{
//	use \Workflow\Traits_Model_Workflow;

	protected static $_table_name = 'supportcontributes';
	public static $_subject_field_name = 'SOME_TRAITS_USE_SUBJECT_FIELD_NAME';

	protected static $_properties = array(
		'id',
		'receipt_at',
		'customer_id',
		'support_type',
		'subject_id',
		'support_money',
		'fee',
		'support_article',
		'article_delivery_type',
		'consignee_type',
		'support_aim',
		'memo',
		'is_letter_of_thanks',
		'send_letter_of_thanks_at',
		'classification',
		'entry_at',
		'entry_user',
		'entry_uid',
		'created_at',
		'updated_at',
		'update_user',
		'update_uid',
		//'expired_at',
		'deleted_at',
		'is_contributer' => array(
			'data_type' => 'int',
		),

// 'workflow_status',
	);

	public static $_type_config = array(
		'support_type' => array(
			'役員' => '役員',
			'職員' => '職員',
			'利用者' => '利用者',
			'家族' => '家族',
			'遺族' => '遺族',
			'ボランティア' => 'ボランティア',
			'その他' => 'その他',
		),

		'article_delivery_type' => array(
			'あいあい' => 'あいあい',
			'ふなおか' => 'ふなおか',
			'らくらく' => 'らくらく',
			'管理' => '管理',
			'訓練' => '訓練',
			'出版' => '出版',
			'図書館' => '図書館',
			'船岡寮' => '船岡寮',
			'鳥居寮' => '鳥居寮',
			'本部' => '本部',
		),

		'support_aim' => array(
			'一般' => '一般',
			'船岡寮' => '船岡寮',
		),

		'consignee_type' => array(
			'建設特別寄付' => '建設特別寄付',
			'持参' => '持参',
			'振込（UFJ）' => '振込（UFJ）',
			'振込（京都）' => '振込（京都）',
			'郵便口座' => '郵便口座',
			'郵便振替' => '郵便振替',
			'郵便貯金' => '郵便貯金',
		),
	);



	protected static $_depend_modules = array('customer');

	/**
	 * $_option_options - see sample at \User\Model_Usergroup
	 */
	public static $_option_options = array();

	protected static $_belongs_to = array(
		'customer' => array(
			'key_from' => 'customer_id',
			'model_to' => '\Customer\Model_Customer',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);
	/*
	protected static $_has_many = array(
		'subject' => array(
			'key_from' => 'subject_id',
			'model_to' => '\Supportcontribute\Model_Supportcontributesubject',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false
		),
	);
	 */
	//observers
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


	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory, $obj = null)
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
		$form->add('support_type', '寄付者種別',
			array('type' => 'select', 'options' => static::$_type_config['support_type'])
		)
		->add_rule('required')
		->set_value(@$obj->support_type);

		$form->add('receipt_at', '受付日(入金日)',
			array('type' => 'text', 'size' => 20, 'class' => 'date', 'placeholder' => date('Y-m-d'))
		)
		->add_rule('required')
		->set_value(@$obj->receipt_at);

		$form->add('support_aim', '目的',
			array('type' => 'radio', 'options' => static::$_type_config['support_aim'])
		)
		->add_rule('required')
		->set_value(@$obj->support_aim);

		$subject_model = get_called_class() == 'Support\Model_Support' ? '\Support\Model_Subject' : '\Contribute\Model_Subject';
		$form->add('subject_id', '科目',
			array('type' => 'select', 'options' => $subject_model::get_options(array(), 'name'))
		)
		->add_rule('required')
		->set_value(@$obj->subject_id);

		$form->add('consignee_type', '受取方法',
			array('type' => 'select', 'options' => static::$_type_config['consignee_type'])
		)
		->add_rule('required')
		->set_value(@$obj->consignee_type);

		$form->add('support_money', '寄付金額',
			array('type' => 'number')
		)
		->add_rule('required')
		->set_description('礼状に記載する金額を入力して下さい<br>物品寄付の場合は換価格を入力して下さい')
		->set_value(@$obj->support_money);

		$form->add('fee', '手数料',
			array('type' => 'number')
		)
		->add_rule('required')
		->set_value(@$obj->fee);

		$form->add('support_article', '寄付物品名',
			array('type' => 'text')
		)
		->set_value(@$obj->support_article);

		$form->add('article_delivery_type', '物品受入先',
			array('type' => 'select', 'options' => array_merge(array('' => ''), static::$_type_config['article_delivery_type']))
		)
		->set_value(@$obj->article_delivery_type);

		//customer_kana
		$form->add('memo', '備考',
			array('type' => 'textarea', 'size' => 50)
		)
		->add_rule('max_length', 50)
		->set_value(@$obj->memo);

		$form->add('customer_id', '',
			array('type' => 'hidden')
		);
		if (is_null($obj->customer) and \Input::get('customer_id')) {
			$form->field('customer_id')->set_value(\Input::get('customer_id'));
		} else {
			$form->field('customer_id')->set_value(@$obj->customer->id);
		}

		return $form;
	}




}
