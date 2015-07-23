<?php
namespace Locomo;
class Model_Flr extends \Model_Base
{
	/**
	 * vals
	 */
//	protected static $_table_name = 'lcm_flrs';
	public static $_table_name = 'lcm_flrs';
	public static $is_renamed = false;

	// $_conditions
	protected static $_conditions = array(
		'where' => array(
			array('id', 'is not', null)
		)
	);
	public static $_options = array();

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'name' => array(
			'label' => '名称',
			'form' => array('type' => 'text', 'size' => 20, 'class' => 'text'),
			'validation' => array(
				'required',
				'match_pattern' => array("/^[一-龠ぁ-んァ-ヴa-zA-Z0-9・.ー_ 　-]+$/u"),
				'max_length' => array(255),
			),
		),
		'explanation' => array(
			'label' => '備考',
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
			'default' => 0,
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
		'depth' => array(
			'form' => array('type' => false),
			'default' => 0
		),
		'ext' => array(
			'form' => array('type' => false),
			'default' => 'etc'
		), // variable things
		'mimetype' => array(
			'form' => array('type' => false),
			'default' => 'text/plain'
		), // variable things
		'genre' => array(
			'form' => array('type' => false),
			'default' => 'dir'
		), // enum: dir, file, txt, image, audio, movie, braille, doc, xls, ppt
		'expired_at' => array('form' => array('type' => false), 'default' => null),
		'creator_id' => array('form' => array('type' => false), 'default' => ''),
		'updater_id' => array('form' => array('type' => false), 'default' => ''),
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
			'cascade_delete' => true,
		),
		'permission_user' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Flr_User',
			'key_to' => 'flr_id',
			'cascade_save' => true,
			'cascade_delete' => true,
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
		'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
	);

	/**
	 * _event_before_save()
	 */
	public function _event_before_save()
	{
		// prevent loop at inside of this observer
		$this->disable_event('before_save');

		// modify path
		// パスの確定
		if ( ! $this->path && \Input::post('parent'))
		{
			// action_create_dir
			$this->path = \Input::post('parent', DS).\Input::post('name');
			if ($this->genre == 'dir')
			{
				$this->path = rtrim($this->path, DS).DS;
			}
		}
		$this->path = static::enc_url($this->path);
		$fullpath = LOCOMOFLRUPLOADPATH.$this->path;

		// modify name - currently directory only
		// 名称変更。今のところディレクトリのみ
		if ($this->genre == 'dir'){
			$old_name = \Arr::get($this->_original, 'name', $this->name);
			$new_name = \Input::post('name', $this->name);
			 // rename flag to use at _event_after_update
			if ($old_name != $new_name)
			{
				$this->path = static::enc_url(dirname(rtrim($this->path,DS)).$new_name).DS;
				$this->name = $new_name;
				$fullpath = LOCOMOFLRUPLOADPATH.$this->path;
			}
		}
		$this->name = urldecode($this->name);

		// modify depth
		// 深さ
		$num = $this->genre == 'dir' ? 2 : 1;
		$depth = count(explode('/', $this->path)) - $num;
		$this->depth = $depth;

		// modify fileinfo
		// 拡張子やmimetypeを修正
		if ($this->genre != 'dir')
		{
			$this->ext      = substr($this->name, strrpos($this->name, '.') + 1) ;
			try
			{
				$this->mimetype = \File::file_info($fullpath)['mimetype'] ;
			} catch (\Fuel\Core\InvalidPathException $e) {
				$this->mimetype = 'unknown' ;
			}
			$this->genre    = \Locomo\File::get_file_genre($this->name);
		}
	}

	/**
	 * _event_after_insert()
	*/
	public function _event_after_insert()
	{
		// this observer is depend on \Input::post().
		// Controller_Flr::sync() doesn't have \Input::post() but Controller_Flr::sync() already define $this->path
		$this->path = $this->path ?: \Input::post('parent').\Input::post('name');
		$this->save();
	}

	/**
	 * _event_after_update()
	 * it calls static::embed_hidden_info().
	 * static::embed_hidden_info() は、確実にデータベースをアップデートしたあとに呼びたいので、ここに設置する。しかし、関係テーブルしかアップデートしないaction_permission_dir()は、これを呼び出さないので、処理のためだけにaction_permission_dir()でupdated_atフィールドを改変しているが、要検討。
	*/
	public function _event_after_update()
	{
		// prevent loop at inside of this observer
		$this->disable_event('after_update');

		// embed hidden file
		static::embed_hidden_info($this);
	}

	/**
	 * update_permission()
	*/
	public static function update_permission($obj, $arrs, $relation)
	{
		$relation_name = 'permission_'.$relation;
		$model_name = '\Model_Flr_'.ucfirst($relation);
		foreach ($arrs as $arr)
		{
			$obj->{$relation_name}[] = $model_name::forge()->set($arr);
		}
	}

	/**
	 * enc_url()
	*/
	public static function enc_url($path, $enc_slash = false)
	{
		if (function_exists('normalizer_normalize'))
		{
			$path = normalizer_normalize($path);
		}
		if($enc_slash)
		{
			return urlencode(urldecode($path));
		} else {
			return str_replace('%2F', '/', urlencode(urldecode($path)));
		}
	}

	/**
	 * update_permission_by_intersects()
	*/
	public static function update_permission_by_intersects($obj, $intersects, $relation)
	{
		$relation_name = 'permission_'.$relation;
		$model_name = '\Model_Flr_'.ucfirst($relation);
		$arrs = static::modify_intersects_arr_to_modellike_arr($intersects, $relation);
		static::update_permission($obj, $arrs, $relation);
	}

	/**
	 * modify_intersects_arr_to_modellike_arr()
	*/
	public static function modify_intersects_arr_to_modellike_arr($intersects, $relation)
	{
		$arrs = array();
		foreach ($intersects as $intersect)
		{
			list($id, $right) = explode('/', $intersect);
			$arrs[] = array(
				'flr_id'        => @$obj->id ?: 1, // Controller_Flr::before() doesn't have $obj
				$relation.'_id' => $id,
				'access_level'   => $right,
			);
		}
		return $arrs;
	}

	/**
	 * get_relation_as_array()
	*/
	public static function get_relation_as_array($obj, $relation)
	{
		if ( ! $obj instanceof Model_Flr) return array();
		$relation_name = 'permission_'.$relation;
		$retvals = array();
		if ($obj->$relation_name)
		{
			foreach ($obj->$relation_name as $id => $v)
			{
//				eval('$relations = '.var_export($v->_data, true).';');
				$relations = $v->to_array();
				if ($relations)
				{
					$retvals[$id] = $relations;
					$retvals[$id]['flr_id'] = $obj->id;
					unset($retvals[$id]['id']);
				}
			}
		}
		$retvals = \Arr::multisort($retvals, array($relation.'_id' => SORT_ASC));
		return $retvals;
	}

	/**
	 * get_parent()
	 */
	public static function get_parent($obj)
	{
		if ( ! $obj instanceof Model_Flr) return false;
		$p_path = rtrim(dirname($obj->path), DS).DS;
		$option = array(
			'from_cache' => false,
//			'related' => array('permission_usergroup', 'permission_user'),
			'where' => array(
				array('path', '=', $p_path),
			),
		);
		return static::find('first', $option);
	}

	/**
	 * get_children()
	 */
	public static function get_children($obj)
	{
		if ( ! $obj instanceof Model_Flr) return array();

		$path = str_replace('%', '\%', $obj->path);

		// admin/root condition
		if (\Auth::is_admin())
		{
			$or_conditions = array(
				array('genre', '=', 'dir'),
				array('path', 'like', $path.'%'),
				array('depth', '=', $obj->depth + 1),
				array('id', '<>', $obj->id),
			);
		}
		else
		{
			$or_conditions = array(
				array('genre', '=', 'dir'),
				array('permission_usergroup.usergroup_id', 'in', \Auth::get_groups()),
				array('permission_usergroup.access_level', '>', 1),
				array('path', 'like', $path.'%'),
				array('depth', '=', $obj->depth + 1),
				array('id', '<>', $obj->id),
			);
		}

		// current children
		$option = array(
			'related' => array('permission_usergroup'),
			'where' => array(
				array('genre', '<>', 'dir'),
				array('path', 'like', $path.'%'),
				array('depth', '=', $obj->depth + 1),
				array('id', '<>', $obj->id),
				'or' => $or_conditions,
			),
			'order_by' => array(
				'ext' => 'ASC',
				'created_at' => 'DESC'
			),
		);

		$objs = static::find('all', $option);
		foreach ($objs as $k => $obj)
		{
			if ( ! \Controller_Flr::check_auth($obj->path))
			{
				unset($obj[$k]);
			}
		}

		return $objs;
	}

	/**
	 * embed_hidden_info()
	 */
	public static function embed_hidden_info($obj)
	{
		// current
		if ( ! $obj) return false;
		$current = static::fetch_hidden_info(LOCOMOFLRUPLOADPATH.$obj->path);

		// target
		$path = LOCOMOFLRUPLOADPATH.$obj->path;
		$target = is_dir($path) ? rtrim($path, DS).DS : dirname($path).DS ;

		// get myself and children
		$vals = Model_Flr::find('all', array('from_cache' => false, 'where' => array(array('path', 'like', $obj->path.'%'))));


/*
第二階層のみ、パーミッションをアップデートするようにして、hidden_infoを調整する。
そのためには、ややじかんがかかるんで、今日はここまで。
*/

		// update myself and children 
		if ($vals)
		{
			foreach ($vals as $val)
			{
				if ( ! is_object($val)) continue;
				$key = md5($val->path); // to save dot and ext.

				// var_export() to use Model's __to_string() and eval() it
				eval('$data = '.var_export($val->_data, true).';');
				\Arr::set($current, $key.'.data'     , $data);

				// relations
				$usergroups = static::get_relation_as_array($val, 'usergroup');
				\Arr::set($current, $key.'.permission_usergroup', $usergroups);
				$users = static::get_relation_as_array($val, 'user');
				\Arr::set($current, $key.'.permission_user', $users);
			}
		}

		// tidy up current
		foreach ($current as $k => $v)
		{
			if( ! file_exists(LOCOMOFLRUPLOADPATH.\Arr::get($v, 'data.path')))
			{
				unset($current[$k]);
			}
		}

		// put 
		if (file_exists($target.'.LOCOMO_DIR_INFO'))
		{
			\File::delete($target.'.LOCOMO_DIR_INFO');
		}

		$permissions = File::get_permissions($target);
		if ($permissions !== '0777')
		{
			try
			{
				chmod($target, 0777);
			} catch (\Fuel\Core\PhpErrorException $e) {
				// do nothing
			}
		}

		try
		{
			\File::create($target, '.LOCOMO_DIR_INFO', serialize($current));
		} catch (\Fuel\Core\InvalidPathException $e) {
			// do nothing
			\Session::set_flash('error', array('同期用の補助情報の保存に失敗しています。サーバ管理者にディレクトリのパーミッションを調整するように打診してください。'));
		}
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
			$retval = unserialize($content) ?: array();
		} catch (Exception $e) {
			$retval = array();
		}
		return $retval;
	}
}
