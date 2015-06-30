<?php
class Model_Msgbrd_Attaches extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = 'lcm_msgbrd_usergroups';

	// $_conditions
	protected static $_conditions = array();

	// $_options
	public static $_options = array();

	// $_properties
	protected static $_properties =
	array (
		'id',
		'flr_id' => array(
			'form' => array (
				'type' => 'hidden',
			)
		),
		'attach_id' => array (
			'label' => '添付ファイルid',
			'data_type' => 'int',
			'form' => array (), // override by \Locomo\Model_Msgbrd::form_definition() & Model_Msgbrd::form_definition()
		),
	) ;


/*
	// $_has_many
	protected static $_has_many = array(
		'foo' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Foo',
			'key_to' => 'bar_id',
			'cascade_save' => true,
			'cascade_delete' => false
		)
	);
	// $_belongs_to
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

	// observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_update'),
				'mysql_timestamp' => true,
			),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Expired' => array(
				'events' => array('before_insert', 'before_save'),
				'properties' => array('expired_at'),
			),
			'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
//		't'Locomo\Observer_Workflow' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
//		't'Locomo\Observer_Revision' => array(
//			'events' => array('after_insert', 'after_save', 'before_delete'),
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
	public static function form_definition($factory = 'msgbrd_usergroup', $obj = null)
	{
		if (static::$_cache_form_definition && $obj == null)
		{
			return static::$_cache_form_definition;
		}

		$form = parent::form_definition($factory, $obj);

/*
		//add field
		$options = \Model_Name::get_options(array('where' => array(array('category', 'NAME'))), 'name');
		$form->add_after('objname', 'NAME', array('type' => 'checkbox', 'options' => $options), array(), 'user_type')
			->set_value(array_keys($obj->objname));

		//template set
		$form->field('field_name')
			->set_template("\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} <span>{description}</span> {error_msg} <input type=\"button\" value=\"VALUE\"></td>\n\t\t</tr>\n");
*/
		if ( ! \Auth::is_admin())
		{
			$form->field('is_visible')->set_type('hidden')->set_value($obj->is_visible ?: 1);

		}


		static::$_cache_form_definition = $form;
		return $form;
	}

	/**
	 * plain_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function plain_definition($factory = 'msgbrd_usergroup', $obj = null)
	{
		$form = static::form_definition($factory, $obj);
/*
		$form->field('created_at')
			->set_attribute(array('type' => 'text'));
*/

		return $form;
	}

	/*
	 * search_form
	 */
	public static function search_form()
	{
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('msgbrd_usergroup_search_form', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// wrap
		$parent = parent::search_form_base('');
		$parent->add_after($form, 'msgbrd_usergroup_search_form', array(), array(), 'opener');

		return $parent;
	}
}
