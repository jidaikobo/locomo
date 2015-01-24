<?php
namespace Locomo;
class Model_Flr extends \Model_Base
{
	/**
	 * vals
	 */
//	protected static $_table_name = 'lcm_flrs';
	public static $_table_name = 'lcm_flrs';

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'name' => array(
			'label' => 'ディレクトリ名',
			'form' => array('type' => 'text', 'size' => 20, 'class' => 'text'),
			'validation' => array(
				'required',
				'max_length' => array(255),
				'valid_string' => array(array('alpha','numeric','dots','dashes')),
			),
		),
		'explanation' => array(
			'label' => 'メモ',
			'form' => array('type' => 'textarea', 'rows' => '3', 'cols' => '50', 'class' => 'textarea'),
		),
		'path' => array(
			'label' => '物理パス',
			'form' => array('type' => 'hidden'),
		),
		'is_sticky' => array(
			'label' => 'ダッシュボードへの表示',
			'form' => array(
				'type' => 'select',
				'options' => array('0' => '表示しない', '1' => '表示する')
			),
			'default' => 1,
			'validation' => array(
				'required',
			),
		),
		'is_visible' => array(
			'label' => '可視属性',
			'form' => array(
				'type' => 'hidden',
				'options' => array('0' => '不可視', '1' => '可視')
			),
			'default' => 1,
			'validation' => array(
				'required',
			),
		),
		'depth' => array('form' => array('type' => false), 'default' => 0),
		'ext' => array('form' => array('type' => false), 'default' => 'etc'), // variable things
		'genre' => array('form' => array('type' => false), 'default' => 'dir'), // enum: dir, file, txt, image, audio, movie, braille, doc, xls, ppt
		'expired_at' => array('form' => array('type' => false), 'default' => null),
		'creator_id' => array('form' => array('type' => false), 'default' => -1),
		'updater_id' => array('form' => array('type' => false), 'default' => -1),
		'created_at' => array('form' => array('type' => false), 'default' => null),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
	);

	/**
	 * $_has_many
	 */
	protected static $_has_many = array(
		'permission_usergroup' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Flr_Usergroup',
			'key_to' => 'flr_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'permission_user' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Flr_User',
			'key_to' => 'flr_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);

	/**
	 * $_soft_delete
	 */
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	/**
	 * $_observers
	 */
	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
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
	);

	// $_conditions
	public static $_conditions = array(
		'where' => array(
			array('id', 'is not', null)
		)
	);

	/**
	 * _event_after_insert()
	*/
	public function _event_after_insert()
	{
		// it is depend on \Input::post().
		// sync doesn't have \Input::post() but sync already define $this->path
		$this->path = $this->path ?: \Input::post('parent').\Input::post('name');
		$this->save();
		static::embed_hidden_info($this);
	}

	/**
	 * _event_after_update()
	*/
	public function _event_after_update()
	{
		// prevent loop at inside of this observer
		$this->disable_event('after_update');

		// put .LOCOMO_DIR_INFO
		if ( ! file_exists($new_path.'.LOCOMO_DIR_INFO'))
		{
			static::embed_hidden_info($this);
		}

		// permission - put it in .LOCOMO_DIR_INFO
		if ($this->permission_usergroup)
		{
// \Fileでappendする？ なければput
// あとで、unserializeする？
		}

		// children
		$children = \Model_Flr::find('all', array('where' => array(array('path', 'like', $this->path.'%'))));

		if ($children)
		{
			foreach ($children as $child)
			{
				// don't run at itself.
				if ($child->path == $this->path) continue;

				// permission
/*
				// オブザーバでの他モデルの書き換え事例として面白いので削除しないが、子供のディレクトリを全部親と同じにしてしまうので、見直し。
				$child->permission_usergroup = array();
				foreach ($this->permission_usergroup as $k => $v)
				{
					$new = \Model_Flr_Usergroup::forge();
					$arr = $v->to_array();
					unset($arr['id']);
					$new->set($arr);
					$child->permission_usergroup[] = $new;
				}
*/

				// rename
				if (in_array(\Request::active()->action, array('rename_dir')))
				{
					$old_path_str = LOCOMOUPLOADPATH.DS.trim($this->path, DS).DS;
					$new_path_str = LOCOMOUPLOADPATH.DS.trim($new_path, DS).DS;
					$new_child_str = LOCOMOUPLOADPATH.DS.trim($child->path, DS).DS;
	
					$new_child_str = str_replace($old_path_str, $new_path_str, $new_child_str);
					$child->path = substr($new_child_str, strlen(LOCOMOUPLOADPATH));
				}

				$child->save();

				// embed($path)
				static::embed_hidden_info($child);
			}
		}

		// itself
		$this->path = $new_path;
		$this->save();
		static::embed_hidden_info($this);
	}

	/**
	 * embed_hidden_info()
	 */
	public static function embed_hidden_info($obj)
	{
		// current
		$current = static::fetch_hidden_info(LOCOMOUPLOADPATH.$obj->path);

		// target
		$path = LOCOMOUPLOADPATH.$obj->path;
		$target = is_dir($path) ? $path : dirname($path).DS ;

		// get myself and children
		$vals = Model_Flr::find('all', array('where' => array(array('path', 'like', $obj->path.'%'))));

		// update current
		if ($vals)
		{
			foreach ($vals as $val)
			{
				if ( ! is_object($val)) continue;
				$key = md5($val->path); // to save dot and ext.

				// var_export() to use Model's __to_string() and eval() it
				eval('$data = '.var_export($val->_data, true).';');
				eval('$relations = '.var_export($val->_data_relations, true).';');
				\Arr::set($current, $key.'.data'     , $data);
				\Arr::set($current, $key.'.relations', $relations);
			}
		}

		// tidy up current
		foreach ($current as $k => $v)
		{
			if( ! file_exists(LOCOMOUPLOADPATH.\Arr::get($v, 'data.path')))
			{
				unset($current[$k]);
			}
		}

		// put 
		if (file_exists($target.'.LOCOMO_DIR_INFO'))
		{
			\File::delete($target.'.LOCOMO_DIR_INFO');
		}
		\File::create($target, '.LOCOMO_DIR_INFO', serialize($current));
	}

	/**
	 * fetch_hidden_info()
	 */
	public static function fetch_hidden_info($path)
	{
		$target = is_dir($path) ? $path.'.LOCOMO_DIR_INFO' : dirname($path).DS.'.LOCOMO_DIR_INFO' ;
		if ( ! file_exists($target)) return array();
		$content = \File::read($target, $as_string = true);

		try
		{
			$retval = unserialize($content);
		} catch (Exception $e) {
			$retval = array();
		}
		return $retval;
	}

	/**
	 * form_definition()
	 */
	public static function form_definition($factory = 'form', $obj = null)
	{
		$id = isset($obj->id) ? $obj->id : '';

		// forge
		$form = parent::form_definition($factory, $obj);

		// create or move
		if (in_array(\Request::active()->action, array('create_dir', 'move_dir')))
		{
			$form = static::directory_list($form, $obj);
		}

		// move or delete
		if (in_array(\Request::active()->action, array('move_dir', 'purge_dir', 'permission_dir')))
		{
			$form = static::hide_current_name($form, $obj);
		}

		// rename
		if (in_array(\Request::active()->action, array('rename_dir')))
		{
			$form = static::parent_dir($form, $obj);
		}

		// permission
		if (in_array(\Request::active()->action, array('permission_dir')))
		{
			$form = static::permission($form, $obj);
		}

		// purge_dir
		if (in_array(\Request::active()->action, array('purge_dir')))
		{
			$form = static::purge_dir($form, $obj);
		}

		// upload
		if (in_array(\Request::active()->action, array('upload')))
		{
			$form = static::upload($form, $obj);
		}

		// modify_name
		if (in_array(\Request::active()->action, array('view_file', 'edit_file')))
		{
			$form = static::modify_name($form, $obj);
		}

		// purge_file
		if (in_array(\Request::active()->action, array('purge_file')))
		{
			$form = static::purge_file($form, $obj);
		}

		return $form;
	}

	/**
	 * modify_name()
	 */
	public static function modify_name($form, $obj)
	{
		$form->field('name')->set_label('ファイル名');
		return $form;
	}

	/**
	 * hide_current_name()
	 */
	public static function hide_current_name($form, $obj)
	{
		$form->field('name')->set_type('hidden');
		$form->add_after('display_name', 'ディレクトリ名', array('type' => 'text', 'disabled' => 'disabled'),array(), 'name')->set_value(@$obj->name);
		return $form;
	}

	/**
	 * directory_list()
	 */
	public static function directory_list($form, $obj)
	{
		// list of upload directories - for choose parent dir.
		$selected_id = \Request::main()->id;
		$selected_path = '';
		if ($selected_id)
		{
			$selected_obj = static::find($selected_id);
			$selected_path = $selected_obj ? $selected_obj->path : $selected_path;
		}

		$current_dir = @$obj->path ?: '';
		$selected = $selected_path ?: $current_dir ;
		$dirs = \Util::get_file_list(LOCOMOUPLOADPATH, $type = 'dir');
		$options = array();

		foreach ($dirs as $dir)
		{
			$dir = substr($dir, strlen(LOCOMOUPLOADPATH));

			// is exist on database
			if( ! \Model_Flr::find('first', array('where' => array(array('path', $dir))))) continue;

			// cannot choose myself and children
			if ($current_dir && substr($dir, 0, strlen($current_dir)) == $current_dir) continue;

			// check auth
			if ( ! \Controller_Flr::check_auth($dir)) continue;

			$options[$dir] = $dir;
		}

		$form->add_after(
				'parent',
				'親ディレクトリ',
				array('type' => 'select', 'options' => $options, 'style' => 'width: 10em;'),
				array(),
				'name'
			)
			->set_value($selected);

		return $form;
	}

	/**
	 * parent_dir()
	 */
	public static function parent_dir($form, $obj)
	{
		$current_dir = @$obj->path ?: '';
		$current_dir = $current_dir ? rtrim(dirname($current_dir), '/').DS : '';
		$current_dir = $current_dir ? substr($current_dir, strlen(LOCOMOUPLOADPATH) - 1) : '';

		$form->add_after(
				'parent',
				'親ディレクトリ',
				array('type' => 'text', 'disabled' => 'disabled', 'style' => 'width:100%;'),
				array(),
				'name'
			)
			->set_value($current_dir);

		return $form;
	}

	/**
	 * permission()
	 */
	public static function permission($form, $obj)
	{
		$form->field('explanation')->set_type('hidden');
		$form->field('is_sticky')->set_type('hidden');

		// usergroup_id
		$options = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true))), 'name');
		$options = array(''=>'選択してください', '0' => 'ゲスト') + $options;
		\Model_Flr_Usergroup::$_properties['usergroup_id']['form'] = array(
			'type' => 'select',
			'options' => $options,
			'class' => 'varchar usergroup',
		);
		$usergroup_id = \Fieldset::forge('permission_usergroup')->set_tabular_form('\Model_Flr_Usergroup', 'permission_usergroup', $obj, 2);
		$form->add_after($usergroup_id, 'ユーザグループ権限', array(), array(), 'is_sticky');

		// user_id
		$options = array(''=>'選択してください') + \Model_Usr::get_options(array(), 'display_name');
		\Model_Flr_User::$_properties['user_id']['form'] = array(
			'type' => 'select',
			'options' => $options,
			'class' => 'varchar user',
		);
		$user_id = \Fieldset::forge('permission_user')->set_tabular_form('\Model_Flr_User', 'permission_user', $obj, 2);
		$form->add_after($user_id, 'ユーザ権限', array(), array(), 'is_sticky');

		return $form;
	}

	/**
	 * purge_dir()
	 */
	public static function purge_dir($form, $obj)
	{
		\Session::set_flash('error', 'ディレクトリを削除すると、そのディレクトリの中に含まれるものもすべて削除されます。この削除は取り消しができません。注意してください。');
		$form->field('name')->set_type('hidden');
		$form->field('explanation')->set_type('hidden');
		$form->field('is_sticky')->set_type('hidden');

		$back = \Html::anchor(\Uri::create('flr/index_files/'.$obj->id), '戻る', array('class' => 'button'));
		$form->field('submit')->set_value('完全に削除する')->set_template('<div class="submit_button">'.$back.'{field}</div>');
		return $form;
	}

	/**
	 * upload()
	 */
	public static function upload($form, $obj)
	{
		$form->field('name')->set_type('hidden');
		$form->add_after('display_name', 'ディレクトリ名', array('type' => 'text', 'disabled' => 'disabled'),array(), 'name')->set_value(@$obj->name);
		$form->add_after(
			'upload',
			'アップロード',
			array('type' => 'file'),
			array(),
			'is_visible'
		)
		->add_rule(array('valid_string' => array('alpha','numeric','dot','dashes')));

//		$form->field('submit')->set_label('完全に削除する');
		$form->field('submit')->set_value('アップロード');
		return $form;
	}

	/**
	 * purge_file()
	 */
	public static function purge_file($form, $obj)
	{
		\Session::set_flash('error', 'ファイルの削除は取り消しできませんので、ご注意ください。');
		$form->field('name')->set_type('hidden');
		$form->field('explanation')->set_type('hidden');
		$form->field('is_sticky')->set_type('hidden');
		$form->add_after('display_name', 'ファイル名', array('type' => 'text', 'disabled' => 'disabled'),array(), 'name')->set_value(@$obj->name);

		$back = \Html::anchor(\Uri::create('flr/view_file/'.$obj->id), '戻る', array('class' => 'button'));
		$form->field('submit')->set_value('完全に削除する')->set_template('<div class="submit_button">'.$back.'{field}</div>');
		return $form;
	}
}
