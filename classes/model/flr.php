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
			'label' => 'ディレクトリ名',
			'form' => array('type' => 'text', 'size' => 20, 'class' => 'text'),
			'validation' => array(
				'required',
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
		$fullpath = LOCOMOUPLOADPATH.$this->path;

		// modify name - currently directory only
		// 名称変更。今のところディレクトリのみ
		if ($this->genre == 'dir'){
			$old_name = \Arr::get($this->_original, 'name', $this->name);
			$new_name = \Input::post('name', $this->name);
			 // rename flag to use at _event_after_update
			if ($old_name != $new_name)
			{
				$this->path = static::enc_url(dirname(rtrim($this->path,DS)).DS.$new_name).DS;
				$this->name = $new_name;
				$fullpath = LOCOMOUPLOADPATH.$this->path;
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
			$this->mimetype = \File::file_info($fullpath)['mimetype'] ;
			$this->genre    = \Locomo\File::get_file_genre($this->name);
		}

		// permission
		// ディレクトリパーミッションの修正
		if ($this->genre == 'dir')
		{
			// to solve contradiction compare permission between parent and current dir.
			// パーミッションの矛盾を直情の親を見て解決。親以下にする。
			if ($this->path != '/') // not for root dir.
			{
				$p_obj = static::get_parent($this);
	
				// usergroup
				$parent_g  = static::get_relation_as_array($p_obj, 'usergroup');
				$parent_g  = static::transform_permission_to_intersect_arr($parent_g, 'usergroup');
				$current_g = static::get_relation_as_array($this, 'usergroup');
				$current_g = static::transform_permission_to_intersect_arr($current_g, 'usergroup');
				$group_intersects = array_intersect($parent_g, $current_g);
				$parent_g = array_unique($parent_g);
				$group_intersects = array_unique($group_intersects);
	
				// user
				$parent_u  = static::get_relation_as_array($p_obj, 'user');
				$parent_u  = static::transform_permission_to_intersect_arr($parent_u, 'user');
				$current_u = static::get_relation_as_array($this, 'user');
				$current_u = static::transform_permission_to_intersect_arr($current_u, 'user');
				$user_intersects = array_intersect($parent_u, $current_u);
				$parent_u = array_unique($parent_u);
				$user_intersects = array_unique($user_intersects);

				// initialize permission - overhead but for test purpose, it must be place here
				// データベースを初期化。
				\DB::delete(\Model_Flr_Usergroup::table())->where('flr_id', $this->id)->execute();
				unset($this->permission_usergroup);
				unset($this->permission_user);

				// update permissions - group
				if ( ! $current_g)
				{
					// default permission is same as parent permission
					// 現在のパーミッションが空だったら親のパーミッションと同じにする
					static::update_permission_by_intersects($this, $parent_g, 'usergroup');
				} else {
					// update permission by intersects
					// 現在のパーミッションを親以下にする
					static::update_permission_by_intersects($this, $group_intersects, 'usergroup');
				}

				// update permissions - user
				if ( ! $current_u)
				{
					static::update_permission_by_intersects($this, $parent_u, 'user');
				} else {
					static::update_permission_by_intersects($this, $user_intersects, 'user');
				}
			}
	
			// tidy up order of permissions at root dir
			if ($this->path == '/')
			{
				// preserve and sort
				$usergroup = static::get_relation_as_array($this, 'usergroup');
				$user = static::get_relation_as_array($this, 'user');
	
				// initialize permission - overhead but for test purpose, it must be place here
				\DB::delete(\Model_Flr_Usergroup::table())->where('flr_id', $this->id)->execute();
				unset($this->permission_usergroup);
				unset($this->permission_user);
	
				// update permissions
				static::update_permission($this, $usergroup, 'usergroup');
				static::update_permission($this, $user, 'user');
			}
		} // is_dir until here.

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
	 * 
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
	 * transform_permission_to_intersect_arr()
	*/
	public static function transform_permission_to_intersect_arr($permissions, $relation)
	{
		$tmps = array();
		foreach ($permissions as $v)
		{
			for ($n = 1; $n <= $v['access_level']; $n++)
			{
				$tmps[] = $v[$relation.'_id'].DS.$n;
			}
		}
		return $tmps;
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
		return static::find('first');
	}

	/**
	 * get_children()
	 */
	public static function get_children($obj)
	{
		if ( ! $obj instanceof Model_Flr) return array();

		// current children
		$option = array(
			'where' => array(
				array('path', 'like', $obj->path.'%'),
				array('depth', '=', $obj->depth + 1),
				array('id', '<>', $obj->id),
			),
			'order_by' => array(
				'genre' => 'ASC',
				'created_at' => 'DESC'
			),
		);
		return static::find('all');
	}

	/**
	 * embed_hidden_info()
	 */
	public static function embed_hidden_info($obj)
	{
		// current
		if ( ! $obj) return false;
		$current = static::fetch_hidden_info(LOCOMOUPLOADPATH.$obj->path);

		// target
		$path = LOCOMOUPLOADPATH.$obj->path;
		$target = is_dir($path) ? rtrim($path, DS).DS : dirname($path).DS ;

		// get myself and children
		$vals = Model_Flr::find('all', array('where' => array(array('path', 'like', $obj->path.'%'))));

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

	/**
	 * form_definition()
	 */
	public static function form_definition($factory = 'form', $obj = null)
	{
		$id = isset($obj->id) ? $obj->id : '';

		// forge
		$form = parent::form_definition($factory, $obj);

		// create or move
		if (in_array(\Request::active()->action, array('create', 'move')))
		{
		}

		// move or delete
		if (\Request::active()->controller == 'Controller_Flr_Dir' && in_array(\Request::active()->action, array('move', 'purge', 'permission')))
		{
			$form = static::hide_current_name($form, $obj);
			$form->delete('is_sticky');
		}

		// edit
		if (\Request::active()->controller == 'Controller_Flr_Dir' && in_array(\Request::active()->action, array('edit')))
		{
			$form = static::hide_current_name($form, $obj);
			$form->delete('is_sticky');
		}

		// rename
		if (in_array(\Request::active()->action, array('rename')))
		{
			$form = static::rename_dir($form, $obj);
		}

		// permission_dir
		if (in_array(\Request::active()->action, array('permission')))
		{
			$form = static::permission_dir($form, $obj);
		}

		// purge_dir
		if (in_array(\Request::active()->action, array('purge')))
		{
			$form = static::purge_dir($form, $obj);
		}

		// upload
		if (in_array(\Request::active()->action, array('upload')))
		{
			$form = static::upload($form, $obj);
		}

		// purge_file
		if (\Request::active()->controller == 'Controller_Flr_File' && in_array(\Request::active()->action, array('purge')))
		{
			$form = static::purge_file($form, $obj);
		}

		return $form;
	}

	/**
	 * sync_definition()
	 */
	public static function sync_definition($factory = 'form', $obj = null)
	{
		$form = \Fieldset::forge($factory);

		$form->add(\Config::get('security.csrf_token_key'), '', array('type' => 'hidden'))
			->set_value(\Security::fetch_token());

		$form->add('submit', '', array('type' => 'submit', 'value' => '同期する', 'class' => 'button primary'))
			->set_template('<div class="submit_button">{field}</div>');

		$messages = array(
			'ファイルやディレクトリの実際の状況とデータベースの内容に矛盾が生じているようでしたら、これを実行してください。',
			'また、この同期によって、ファイルシステム上にある不正なファイル名（全角文字等）が修正されます。',
			'ファイルやディレクトリの数によっては時間がかかることがあります。',
			'この処理は、時々自動的に行われますので、原則、明示的な実行は不要です。',
		);
		\Session::set_flash('message', $messages);

		return $form;
	}

	/**
	 * modify_name()
	 */
/*
	public static function modify_name($form, $obj)
	{
		$form->field('name')->set_type('hidden');
		$form->add_after('display_file_name', 'ファイル名', array('type' => 'text', 'disabled' => 'disabled'),array(), 'name')->set_value(@$obj->name)->set_description('ファイル名を変更したい場合はアップし直してください。');
//		$form->field('name')->set_label('ファイル名');
		return $form;
	}
*/
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
	 * rename_dir()
	 */
	public static function rename_dir($form, $obj)
	{
		//$tpl = \Config::get('form')['field_template'];
		$form->field('name')->set_description('現在の名前：'.$obj->name);
		$form->delete('explanation');
		$form->delete('is_sticky');

		// parent dir
		$current_dir = @$obj->path ?: '';
		$current_dir = $current_dir ? rtrim(dirname($current_dir), '/').DS : '';

		$form->add_after(
				'parent',
				'親ディレクトリ',
				array('type' => 'text', 'disabled' => 'disabled', 'style' => 'width:100%;'),
				array(),
				'name'
			)
			->set_value(urldecode($current_dir));

		return $form;
	}

	/**
	 * permission_dir()
	 */
	public static function permission_dir($form, $obj)
	{
		$form->field('explanation')->set_type('hidden');

		\Session::set_flash('message', [
			'親以上の権限は選択しても有効になりません。',
			'親以上の権限を設定しようとすると、自動的に親以下の権限に調整されます。',
			'親ディレクトリでユーザがいっさい指定されていなければ、ユーザの権限設定は表示されません。',
		]);

		$form->add_before('div_opener', '', array('type' => 'text'),array(), 'display_name')->set_template('<div class="input_group">');
		$form->add_after('div_closer', '', array('type' => 'text'),array(), 'display_name')->set_template('</div>');

		// === usergroup_id ===
		$options = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true), array('customgroup_uid', 'is', null))), 'name');
		$options = array('-10' => 'ログインユーザすべて', '0' => 'ゲスト') + $options;

		// ルートディレクトリであれば、上記で取得した全項目をoptionsとしてよい
		if ($obj->path !== '/')
		{
			$parent = static::get_parent($obj);
			$g_permissions = array();
			foreach ($parent->permission_usergroup as $k => $v)
			{
				// logged in users - non object value
				if ($v->usergroup_id === '-10')
				{
					$g_permissions[-10] = 'ログインユーザすべて';
				}
				// guest - non object value
				elseif ($v->usergroup_id === '0')
				{
					$g_permissions[0] = 'ゲスト';
				}
				elseif (is_object($v->usrgrp))
				{
					$g_permissions[$v->usergroup_id] = $v->usrgrp->name;
				}
			}
			$options = array_intersect($g_permissions, $options);
		}

		// $formset
		$options = array(''=>'選択してください') + $options;
		$formset = array('type' => 'select', 'options' => $options, 'class' => 'varchar usergroup',);

		// static::$_properties_cachedに値を足すのは少々奇怪だが、static::get_parent()で関係テーブルにアクセスすると、モデルの初期状態でキャッシュされるため、これを上書きしないと、Model::properties()がキャッシュしか返さないので。
		\Model_Flr_Usergroup::$_properties['usergroup_id']['form'] = $formset;
		if (isset(static::$_properties_cached['Locomo\Model_Flr_Usergroup']['usergroup_id']['form'])){
			static::$_properties_cached['Locomo\Model_Flr_Usergroup']['usergroup_id']['form'] = $formset;
		}
		$usergroup_id = \Fieldset::forge('permission_usergroup')->set_tabular_form('\Model_Flr_Usergroup', 'permission_usergroup', $obj, 2);
		$form->add_after($usergroup_id, 'ユーザグループ権限', array(), array(), 'explanation');

		// === user_id ===
		$options = \Model_Usr::get_options(array(), 'display_name');
		if ($obj->path !== '/')
		{
			$u_permissions = array();
			foreach ($parent->permission_user as $k => $v)
			{
				if ( ! is_object($v->usr)) continue;
				$g_permissions[$v->user_id] = $v->usr->display_name;
			}
			$options = array_intersect($u_permissions, $options);
		}

		// $formset
		$options = array(''=>'選択してください') + $options;
		$formset = array('type' => 'select', 'options' => $options, 'class' => 'varchar usergroup',);

		\Model_Flr_User::$_properties['user_id']['form'] = $formset;
		if (isset(static::$_properties_cached['Locomo\Model_Flr_User']['user_id']['form'])){
			static::$_properties_cached['Locomo\Model_Flr_User']['user_id']['form'] = $formset;
		}
		$user_id = \Fieldset::forge('permission_user')->set_tabular_form('\Model_Flr_User', 'permission_user', $obj, 2);
		$form->add_after($user_id, 'ユーザ権限', array(), array(), 'permission_usergroup');

		return $form;
	}

	/**
	 * purge_dir()
	 */
	public static function purge_dir($form, $obj)
	{
		\Session::set_flash('message', ['ディレクトリの完全削除です。','ディレクトリを削除すると、そのディレクトリの中に含まれるものもすべて削除されます。','この削除は取り消しができません。注意してください。']);
		$form->field('name')->set_type('hidden');
		$form->field('explanation')->set_type('hidden');

		$back = \Html::anchor(\Uri::create('flr/index_files/'.$obj->id), '戻る', array('class' => 'button'));
		$form->field('submit')->set_value('完全に削除する')->set_template('<div class="submit_button">'.$back.'{field}</div>');
		return $form;
	}


	/**
	 * purge_file()
	 */
	public static function purge_file($form, $obj)
	{
		\Session::set_flash('message', ['ファイルの削除は取り消しできませんので、ご注意ください。']);
		$form->field('name')->set_type('hidden');
		$form->field('explanation')->set_type('hidden');
		$form->field('is_sticky')->set_type('hidden');
		$form->add_after('display_name', 'ファイル名', array('type' => 'text', 'disabled' => 'disabled'),array(), 'name')->set_value(@$obj->name);

		$back = \Html::anchor(\Uri::create('flr/view_file/'.$obj->id), '戻る', array('class' => 'button'));
		$form->field('submit')->set_value('完全に削除する')->set_template('<div class="submit_button">'.$back.'{field}</div>');
		return $form;
	}
}
