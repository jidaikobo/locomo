<?php
namespace XXX;
class Model_XXX extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = '###TABLE_NAME###';

	// $_conditions
	protected static $_conditions = array();

	// $_options
	public static $_options = array();

	// $_properties
	protected static $_properties =
###FIELD_STR### ;


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
###DLT_FLD###
	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
###OBSRVR###
	);

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory = 'xxx', $obj = null)
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
###FRMDFN###

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
	public static function plain_definition($factory = 'xxx', $obj = null)
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
		$form = \Fieldset::forge('xxx_search_form', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// wrap
		$parent = parent::search_form_base('###NICENAME###');
		$parent->add_after($form, 'xxx_search_form', array(), array(), 'opener');

		return $parent;
	}
}
