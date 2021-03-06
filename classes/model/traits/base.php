<?php
namespace Locomo;
trait Model_Traits_Base
{
	/*
	 * conditions and options
	 */
	protected static $_conditions = array();
	public static $_options = array();

	/*
	 * default authorize options
	 */
	public static $_authorize_methods = array(
		'auth_expired',
		'auth_created',
		'auth_deleted',
		'auth_visibility',
		'auth_availability',
	);

	/*
	 * _init()
	 * @return void
	 */
	public static function _init()
	{
		// set language to static::$_properties
		$langfile = strtolower(\Inflector::denamespace(get_called_class()));
		$langfile = substr($langfile, strpos($langfile, '_') + 1);
		\Lang::load($langfile);
		foreach (static::$_properties as $k => $v)
		{
			if (is_numeric($k) || is_null(__($k))) continue;
			static::$_properties[$k]['label'] = __($k);
			if (is_null(__($k.'_description'))) continue;
			static::$_properties[$k]['form']['description'] = __($k.'_description');
		}
	}

	/*
	 * find_options()
	 * selectやcheckboxで使うKeyValの状態になった値を返す。
	 * @param string label: field name
	 * @param array() options: for find('all')
	 * @return array()
	 */
	public static function find_options($label = null, $options = array())
	{
		if(is_null($label)) throw new \InvalidArgumentException('optionsのラベル名称をフィールド名で設定してください。');

		$primary_key = reset(self::$_primary_key);
		$items = self::find('all', $options);
		return \Arr::assoc_to_keyval($items, $primary_key, $label);
	}

	/**
	 * set_search_options()
	 * prepare static::$_options
	 * @return void
	 */
	public static function set_search_options()
	{
	}

	/**
	 * set_equal_options()
	 * $_options に $fields の配列を
	 * array($k, '=', $v) の形でセットする
	 * @param  array $white_list
	 * @param  array or value $fields :for examle "\Input::get('searches')"
	 * @return void
	 */
	public static function set_equal_options($white_list = array(), $fields = null)
	{
		if (!$fields) return;
		if (!is_array($fields)) $fields = array($fields);

		if ($fields) {
			foreach ($fields as $k => $v) {
				if (!$v || !in_array($k, $white_list)) continue;
				static::$_options['where'][] = array($k, '=', $v);
			}
		}
	}

	/**
	 * set_like_options()
	 * $_options に $fields の配列を
	 * array($k, 'LIKE', '%'.$v.'%') の形でセットする
	 * @param  array $white_list
	 * @param  array or value $fields :for examle "\Input::get('likes')"
	 * @return void
	 */
	public static function set_like_options($white_list = array(), $fields = 'likes')
	{
		if (!$fields) return;
		if (!is_array($fields)) $fields = array($fields);

		if ($fields) {
			foreach ($fields as $k => $v) {
				if (!$v || !in_array($k, $white_list)) continue;
				static::$_options['where'][] = array($k, 'LIKE', '%'.$v.'%');
			}
		}
	}

	/**
	 * set_paginated_options()
	 * genereate paginated option and links of \Pagination::create_links()
	 * @return void
	 */
	static public function set_paginated_options()
	{
		// クエリ文字列の場合
		if (\Input::get('paged')) \Pagination::set('uri_segment', 'paged');

		// order
		if (\Input::get('orders'))
		{
			$orders = array();
			foreach (\Input::get('orders') as $k => $v)
			{
				// invalid argument
				if ( ! in_array(strtolower($v), array('asc', 'desc'))) continue;

				// リレーションを見る
				if (($dot_pos = strpos($k, '.')) > 0)
				{
					$model = static::relations(substr($k, 0, $dot_pos))->model_to;
					$relate = substr($k, 0, $dot_pos);
					$k = substr($k, $dot_pos+1);
					if ( ! in_array($k, array_keys($model::properties()))) continue;
					static::$_options['related'][$relate]['where'][] = array('id', '!=', 0);
					static::$_options['related'][$relate]['order_by'][$k] = $v;
					// primary key を追加
					$primary_key = reset(static::$_primary_key);
					$table_name = static::table();
					static::$_options['related'][$relate]['order_by']['t0.'.$primary_key] = 'ASC';
					// 既存の order_by を キャンセル
					static::$_options['order_by'] = $orders;
				}
				else
				{
					if ( ! in_array($k, array_keys(static::properties()))) continue;
					$orders[$k] = $v;
					static::$_options['order_by'] = $orders;
					if (count(\Input::get('orders')) == 1 and $k != 'id') static::$_options['order_by']['id'] = 'asc';
				}
			}
			// reset $_conditions
			static::$_conditions['order_by'] = array();
		}

		// set total before use offset
		$cnt_opt = static::$_options;
		if (isset($cnt_opt['group_by'])) unset($cnt_opt['group_by']);
		$count = static::count($cnt_opt);
		\Pagination::set('total_items', $count);

		// limit
		if (\Input::get('limit')) \Pagination::set('per_page', \Input::get('limit'));
		static::$_options['rows_limit'] = \Pagination::get('per_page');
		static::$_options['rows_offset'] = \Pagination::get('offset');
	}

	/**
	 * set_public_options()
	 * @param array() $exception
	 * @return array()
	 */
	public static function set_public_options($exception = array())
	{
		$options = array();

		// created_at
		if (empty($exception) || ! in_array('created_at', $exception))
		{
			$column = \Arr::get(static::get_field_by_role('created_at'), 'lcm_field', 'created_at');
			if (isset(static::properties()[$column]))
			{
				$options['where'][][] = array($column, '<=', date('Y-m-d H:i:s'));
			}
		}

		// expired_at
		if (empty($exception) || ! in_array('expired_at', $exception))
		{
			$column = \Arr::get(static::get_field_by_role('expired_at'), 'lcm_field', 'expired_at');
			if (isset(static::properties()[$column]))
			{
				$options['where'][][] = array($column, '>', date('Y-m-d H:i:s'));
				$max = max(array_keys($options['where']));
				$options['where'][$max]['or'] = array($column, 'is', null);
			}
		}

		// deleted_at
		if (empty($exception) || ! in_array('deleted_at', $exception))
		{
			$column = \Arr::get(static::get_field_by_role('deleted_at'), 'lcm_field', 'deleted_at');
			if (
				is_subclass_of(get_called_class(), '\Orm\Model_Soft') &&
				isset(static::properties()[$column])
			)
			{
				$options['where'][][] = array($column, 'is', null);
			}
		}

		// is_visible
		if (empty($exception) || ! in_array('is_visible', $exception))
		{
			$column = \Arr::get(static::get_field_by_role('is_visible'), 'lcm_field', 'is_visible');
			if (isset(static::properties()[$column]))
			{
				$options['where'][][] = array($column, '=', true);
			}
		}

		// is_available
		if (empty($exception) || ! in_array('is_available', $exception))
		{
			$column = \Arr::get(static::get_field_by_role('is_available'), 'lcm_field', 'is_available');
			if (isset(static::properties()[$column]))
			{
				$options['where'][][] = array($column, '=', true);
			}
		}

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

		//return
		return $options;
	}

	/**
	 * set_deleted_options()
	 * @return array()
	 */
	public static function set_deleted_options()
	{
		$options = array();

		// deleted_at
		$column = \Arr::get(static::get_field_by_role('deleted_at'), 'lcm_field', 'deleted_at');
		if (
			is_subclass_of(get_called_class(), '\Orm\Model_Soft') &&
			isset(static::properties()[$column])
		)
		{
			$options['where'][] = array($column, 'is not', null);
		}

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

		//return
		return $options;
	}

	/**
	 * set_yet_options()
	 * @return array()
	 */
	public static function set_yet_options()
	{
		// set public options
		$options = static::set_public_options(array('created_at', 'expired_at'));

		// created_at
		$column = \Arr::get(static::get_field_by_role('created_at'), 'lcm_field', 'created_at');
		if (isset(static::properties()[$column]))
		{
			$options['where'][] = array($column, '>', date('Y-m-d H:i:s'));
		}

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

		//return
		return $options;
	}

	/**
	 * set_expired_options()
	 * @return array()
	 */
	public static function set_expired_options()
	{
		// set public options
		$options = static::set_public_options(array('expired_at', 'created_at'));

		// $_options - expired_at
		$column = \Arr::get(static::get_field_by_role('expired_at'), 'lcm_field', 'expired_at');
		if (isset(static::properties()[$column]))
		{
			$options['where'][] = array($column, 'is not', null);
			$options['where'][] = array($column, '<', date('Y-m-d H:i:s'));
		}

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

		//return
		return $options;
	}

	/**
	 * set_invisible_options()
	 * @return array()
	 */
	public static function set_invisible_options()
	{
		// set public options
		$options = static::set_public_options(array('is_visible'));

		// $_options - created_at
		$column = \Arr::get(static::get_field_by_role('is_visible'), 'lcm_field', 'is_visible');
		if (isset(static::properties()[$column]))
		{
			$options['where'][] = array($column, '=', false);
		}

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

		//return
		return $options;
	}

	/**
	 * set_unavailable_options()
	 * @return array()
	 */
	public static function set_unavailable_options()
	{
		// set public options
		$options = static::set_public_options(array('is_available'));

		// $_options - created_at
		$column = \Arr::get(static::get_field_by_role('is_available'), 'lcm_field', 'is_available');
		if (isset(static::properties()[$column]))
		{
			$options['where'][] = array($column, '=', false);
		}

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

		//return
		return $options;
	}

	/*
	 * set_authorized_options()
	 */
	public static function set_authorized_options($controller = NULL)
	{
		// $controller - to know access rights - overridable
		$controller = $controller ?: \Request::main()->controller;

		// モデルが持っている判定材料を、適宜$_optionsに足す。
		foreach(self::$_authorize_methods as $authorize_method)
		{
			if ( ! method_exists(get_called_class(), $authorize_method)) continue;
			static::$authorize_method($controller);
		}
	}

	/*
	 * auth_expired()
	 */
	public static function auth_expired($controller)
	{
		// editが許されているユーザなので、期限切れ項目も編集できるように許す
		if(\Auth::has_access($controller.'/edit')) return;

		// column name
		$column = \Arr::get(static::get_field_by_role('expired_at'), 'lcm_field', 'expired_at');
		if( ! isset(static::properties()[$column])) return;

		// condition
		if ( ! \Auth::has_access($controller.'/view_expired'))
		{
			static::$_options['where'][][] = array($column, '>', date('Y-m-d H:i:s'));
			$max = max(array_keys(static::$_options['where']));
			static::$_options['where'][$max]['or'] = array($column, 'is', null);
		}
	}

	/*
	 * auth_created()
	 */
	public static function auth_created($controller)
	{
		// editが許されているユーザなので、予約項目も編集できるように許す
		if(\Auth::has_access($controller.'/edit')) return;

		// column name
		$column = \Arr::get(static::get_field_by_role('created_at'), 'lcm_field', 'created_at');
		if( ! isset(static::properties()[$column])) return;

		// condition
		if ( ! \Auth::has_access($controller.'/index_yet'))
		{
			static::$_options['where'][][] = array($column, '<=', date('Y-m-d H:i:s'));
		}
	}

	/*
	 * auth_deleted()
	 */
	public static function auth_deleted($controller)
	{
		// editが許されているユーザなので、削除済み項目も編集できるように許す
		if(\Auth::has_access($controller.'/edit')) return;

		// column name
		if (isset(static::$_soft_delete))
		{
			$column = \Arr::get(static::$_soft_delete, 'deleted_field');
		} else {
			$column = \Arr::get(static::get_field_by_role('deleted_at'), 'lcm_field', 'deleted_at');
		}
		if( ! isset(static::properties()[$column])) return;

		// in case \Orm\Model_Soft
		if (is_subclass_of(get_class(), '\Orm\Model_Soft'))
		{
			if ( ! \Auth::has_access($controller.'/view_deleted'))
			{
				static::enable_filter();
			} else {
				static::disable_filter();
			}
		// other model
		} elseif (isset(static::properties()[$column])) {
			if ( ! \Auth::has_access($controller.'/view_deleted'))
			{
				static::$_options['where'][][] = array($column, 'IS NOT', null);
			} else {
				static::$_options['where'][][] = array($column, 'IS', null);
			}
		}
	}

	/*
	 * auth_visibility()
	 */
	public static function auth_visibility($controller)
	{
		// editが許されているユーザなので、不可視項目も編集できるように許す
		if(\Auth::has_access($controller.'/edit')) return;

		// column
		$column = \Arr::get(static::get_field_by_role('is_visible'), 'lcm_field', 'is_visible');
		if( ! isset(static::properties()[$column])) return;

		// condition
		if ( ! \Auth::has_access($controller.'/view_invisible'))
		{
			static::$_options['where'][][] = array($column, '=', true);
		}
	}

	/*
	 * auth_availability()
	 */
	public static function auth_availability($controller)
	{
		// editが許されているユーザなので、無効項目も編集できるように許す
		if(\Auth::has_access($controller.'/edit')) return;

		// column
		$column = \Arr::get(static::get_field_by_role('is_available'), 'lcm_field', 'is_available');
		if( ! isset(static::properties()[$column])) return;

		// condition
		if ( ! \Auth::has_access($controller.'/view_unavailable'))
		{
			static::$_options['where'][][] = array($column, '=', true);
		}
	}

	/**
	 * properties_cached()
	 * \Orm\Modelは、いったんrelationなどにアクセスすると、その内容をキャッシュする。キャッシュされたpropaertiesでformを生成すると、内容が追いつかないことがあり、いまのところflrでこのメソッドを使用している。将来的にはなくしたい。
	 * \Orm\Model caches properties.
	 */
	public static function properties_cached()
	{
		return static::$_properties_cached;
	}

	/**
	 * set_properties_cached()
	 * flrで使用
	 */
	public static function set_properties_cached($args = array())
	{
		static::$_properties_cached = \Arr::merge(static::$_properties_cached, $args);
	}

	public static function clear_cache()
	{
		static::$_cached_objects[get_called_class()] = array();
	}

	/**
	 * get_field_by_role()
	 * some properties expected that it has 'lcm_role'.
	 * @return array()
	 */
	public static function get_field_by_role($role)
	{
		$field = '';
		foreach (static::properties() as $k => $v)
		{
			$target = \Arr::get($v, 'lcm_role');
			if ($target === $role)
			{
				$field = $k;
				break;
			}
		}
		if (empty($field)) return array();
		return array('lcm_field' => $field) + static::properties()[$field];
	}

	/**
	 * add_or()
	 * @param array $whr original where
	 * @param array $arr additional where
	 */
	public static function add_or($whr = array(), $arr = array())
	{
		return empty($whr) ? $arr : array($arr, 'or' => $whr);
	}

	/**
	 * mm_and_pk()
	 */
	public static function mm_and_pk($many_many, $model_to_keys)
	{
		// err
		if (
			! is_array($model_to_keys) ||
			count($model_to_keys) < 1
		)
		{
			throw new \Exception('Invaild parameter');
		}
		if (! isset(static::$_many_many[$many_many]) ) throw new \Exception('リレーション'.$manymany.'は有効ではありません');

		$relation = static::$_many_many[$many_many];

		$results = \DB::query('SELECT count(sub.'.$relation['key_through_from'].') cnt, '.$relation['key_through_from'].'
			FROM (
				SELECT * FROM '.$relation['table_through'].'
					WHERE '.$relation['key_through_to'].' IN ('.implode(', ', $model_to_keys).')
			) AS sub
			GROUP BY '.$relation['key_through_from'].'
		HAVING cnt >='.count($model_to_keys).';')->execute();

		$return_array = array();
		foreach ($results as $result)
		{
			$return_array[] = $result[$relation['key_through_from']];
		}

		return $return_array;
	}

	/*
	 * cascade_set()
	 * @param   array     $input_post
	 * @param   Fieldset  $form (for validation)
	 * @param   bool      $repopulate populate input value
	 * @param   bool      $validation validate
	 * @param   bool      $delete_else unset value delete all in has_many relation
	 * @return  bool      whether validation succeeded
	 *
	 * @important   \Response::redirect() after save() or Regenerate Fieldset instance
	 */
	public static $_hm_delete_else = false; // \Locomo\Fieldset::set_tabular_form_blank で自動的に true になり、全入れ全消しの状態になる
	public static $_mm_delete_else = true;
	public function cascade_set($input_post = null, $form = null, $repopulate = false, $validation = true, $hm_delete_else = null, $mm_delete_else = null)
	{
		is_null($hm_delete_else) and $hm_delete_else = static::$_hm_delete_else;
		is_null($mm_delete_else) and $mm_delete_else = static::$_mm_delete_else;

		if (!$input_post) $input_post = \Input::post();
		$validated = array();

		if (!is_null($form)) {
			if ($form instanceof \Fieldset) {
				$valid = array();
			} else {
				throw new \InvalidArgumentException('invalid class second param. needs Fieldset instance.'); // todo
			}
		}

		// root のモデル
		$model_name = strtolower(str_replace('Model_', '',  get_class($this)));
		$table_name = static::table();

		// モデル名から
		if (isset($input_post[$model_name])) {
			$this->set($input_post[$model_name]);
			!is_null($form) and $validation and $validated[] = $form->validation()->run($input_post[$model_name]);
			$repopulate and $form->populate($input_post[$model_name]);

		// テーブル名から
		} elseif (isset($input_post[$table_name])) {
			$this->set($input_post[$table_name]);
			!is_null($form) and $validation and $validated[] = $form->validation()->run($input_post[$table_name]);
			$repopulate and $form->populate($input_post($table_name));

		// 何もなければ、生のプロパティを relations になければつっこむ
		} else {
			$this->set(\Arr::filter_keys($input_post, array_keys(static::relations()), true));
			!is_null($form) and $validation and $validated[] = $form->validation()->run($input_post);
			$repopulate and $form->populate($input_post);
		}
		// => root の設定ココまで

		// relations に応じて、object($this) にオブジェクトを足していく
		foreach (static::relations() as $k => $v) {

			// has_one or belongs_to
			if (static::relations()[$k]->cascade_save and (static::relations()[$k] instanceof \Orm\HasOne or static::relations()[$k] instanceof \Orm\BelongsTo)) {
				if (!$form->field($k)) continue;

				isset($input_post[$k]) and $this[$k]->set($input_post[$k]);
				!is_null($form) and $validation and $validated[] = $form->field($k)->validation()->run($input_post[$k]);
				$repopulate and $form->field($k)->populate($input_post[$k]);

			// has_many
			} elseif (/* static::relations()[$k]->cascade_save and */static::relations()[$k] instanceof \Orm\HasMany ) {
				if (!$form->field($k)) continue;

				// hm 既存列
				foreach ($this[$k] as $kk => $vv) {
					if ($hm_delete_else and !isset($input_post[$k][$kk])) { // $hm_delete_else = true なら セットされていないものは全て消去
						unset($this->{$k}[$kk]);
					} elseif (isset($input_post[$k][$kk]['_delete'])){ // _deleted
						unset($this->{$k}[$kk]);
					} else {
//						isset($input_post[$k][$kk]) and $vv->set($input_post[$k][$kk]);
						if (isset($input_post[$k][$kk])) {
							$vv->set($input_post[$k][$kk]);
							!is_null($form) and $validation and $validated[] = $form->field($k)->field($k.'_row_'.$kk)->validation()->run($input_post[$k][$kk]);
							$repopulate and $form->field($k)->field($k.'_row_'.$kk)->populate($input_post[$k][$kk]);
						} else {
							// observerでの追加など、何らかの理由で$thisに新規列がきている場合は無視して何もしない。
							// たぶん下の新規列で処理されているが、忘れそうなので、明示的にここにコメントを残す。
						}
					}
				}

				// hm 新規列
				if (isset($input_post[$k.'_new'])) {
					$hm_model = static::relations()[$k]->model_to;
					if (!is_null($input_post[$k.'_new'])) {
						foreach ($input_post[$k.'_new'] as $kk => $vv) {
//							$vv = array_filter($vv);
							// array_filter()だと配列の値がゼロと空白で構成された妥当なデータをfalseと見なすので、明示的空白のみで構成された配列をfilterする。
							$vv = array_filter($vv, function($k) {return ! ($k === '');});

							if (!empty($vv)) { // array_filter で引数が全て空なら 空の配列が返る -> 新規の保存なし
								$this->{$k}[] = $hm_model::forge()->set($vv);
								!is_null($form) and $validation and $validated[] = $form->field($k)->field($k.'_new_'.$kk)->validation()->run($input_post[$k.'_new'][$kk]);
								$repopulate and $form->field($k)->field($k.'_new_'.$kk)->populate($input_post[$k.'_new'][$kk]);
							}
						}
					}
				}

			// many_many
			// また、cascade_save は予期せぬ動作をする事から対応していない為 false のみに対応している true の際は別で設定する
			// 関係テーブルはcascadeに関係なく依存する
			// todo cascade_save,cascade_delete が true のとき throw error
			} elseif (!static::relations()[$k]->cascade_save and static::relations()[$k] instanceof \Orm\ManyMany) {

				if (isset($input_post[$k])) {
					$mm_model = static::relations()[$k]->model_to;

					// セットされているフィールドで来ていないもの
					$setted_unset_objs = \Arr::filter_keys($this[$k], $input_post[$k], true);
					foreach ($setted_unset_objs as $unset_key => $vv) {
						unset($this->{$k}[$unset_key]);
					}

					// セットされているもので来ているもの
					$unseted_ids = array_flip(\Arr::filter_keys(array_flip($input_post[$k]), array_keys($this[$k]), true));
					if (!empty($unseted_ids)) {
						foreach ($unseted_ids as $unseted_id) {
							$this->{$k}[$unseted_id] = $mm_model::find($unseted_id);
						}
					}

					// Fieldset_Field なので populate じゃなく set_value
					if ($form->field($k))
					{
						$repopulate and $form->field($k)->set_value(array_keys($this[$k]));
					} else {
//						$form->add($k)->set_value(array_keys($this[$k]));
					}

				// 何も飛んでこなかったとき、form に存在していれば 全て unset する
				} else {
					if ($mm_delete_else) {
						if ($form->field($k) instanceof \Fieldset_Field) $this->{$k} = array();
					}
				}
			}
		}

		if (!is_null($form)) {
			return !in_array(false, $validated);
		} else {
			return true;
		}

	}


	/*
	 * _relation_reset()
	 * @param   array     $property
	 * 検索時に related で絞り込んだ物に -> でアクセスすると
	 * 絞り込んだ状態で取得するので、コイツでリセットかける
	 */
	public function _reset_related($property)
	{
		$conditons = array();
		if ($rel = static::relations($property))
		{
			if (array_key_exists($property, $this->_data_relations))
			{
				$this->_data_relations[$property] = $rel->get($this, $conditons);
				$this->_update_original_relations(array($property));
			}
		}
		return $this->{$property};
	}

	/*
	 * csv 用関数
	 * @param $options     find の時に使った options
	 * @param $glue       string length 1~2
	 * @param $paren
	 */
/*
	public function to_csv(
		$options = array(),
		$rel_names = array(),
		$field_joins = array(),
		$glue = ',',
		$paren = '()',
		$glue_key_val = ':',
		$glue_many = "\n",
		$implode = false
	) {

		$options = array_merge(static::$_options, $options);
		$properties = static::properties();

		$o_arr = array(); // return

		foreach($this->_data as $kk => $vv) {
			if ($options['select'] and !in_array($kk, $options['select'])) continue;

			// if (array_key_exists($kk, $properties) and isset($properties[$kk]['label'])) var_dump($properties[$kk]['label']); die();
			if(isset($properties[$kk]['form']['options'][$vv])) {
				$o_arr[$kk] = $properties[$kk]['form']['options'][$vv];
			} else {
				$o_arr[$kk] = $vv;
			}
		}

		$r_arr = isset($o_arr['id']) ? array('id' => $o_arr['id'],) : array();;
		$skip_keys = array();
		foreach ($field_joins as $k => $v) {
			foreach ($v as $vv) {
				$skip_keys[] = $vv;
			}
		}

		// 並べ替えと join
		foreach ($options['select'] as $v) {
			if ($v == 'id') continue;
			if (in_array($v, $skip_keys)) continue;

			$value = $o_arr[$v];
			// label の設定
			array_key_exists($v, $properties) and isset($properties[$v]['label']) ? $key = $properties[$v]['label'] : $key = $v;

			if(isset($properties[$v]['form']['options'][$value])) {
				$v = $properties[$v]['form']['options'][$value];
			}
			if (array_key_exists($v, $field_joins)) {
				foreach ($field_joins[$v] as $vv){
					if (array_key_exists($vv, $properties)) {
						$value .= $o_arr[$vv];
					} else {
						$value .= $vv;
					}
				}
			}
			$r_arr[$key] = $value;
		}
*/
		/*
		if ($this->_data_relations) {
			foreach ($this->_data_relations as $rel_name => $dr) {
		 */
/*
		if (isset($options['related'])) {
			foreach ($options['related'] as $rel_name => $rel_options) {

				$dr = $this->{$rel_name};

				$rel_options = isset($options['related'][$rel_name]) ? $options['related'][$rel_name] : array();
				$rel_field_joins = (array_key_exists($rel_name, $field_joins)) ? $field_joins[$rel_name] : array();
				if (array_key_exists($rel_name, $rel_names)) $rel_name = $rel_names[$rel_name];
				if (is_array($dr)) {
					$relate_arr = array();
					foreach($dr as $dr_val) {
						$relate_arr[] = $dr_val->to_csv($rel_options, $rel_names, $rel_field_joins, $glue, $paren, $glue_key_val, $glue_many, true);
					}
					$r_arr[$rel_name] = implode($glue_many,$relate_arr);
					// $r_arr = array_merge($r_arr, $r_arr[$rel_name]);
				} else {
					//$r_arr[$rel_name] = $dr->to_csv($rel_options, $rel_names, $glue, $paren, $glue_key_val, true);

					// var_dump($dr->to_csv($rel_options, $rel_names, $glue, $paren, $glue_key_val, false));

					if ($dr) $r_arr = array_merge($r_arr, $dr->to_csv($rel_options, $rel_names, $rel_field_joins, $glue, $paren, $glue_key_val, $glue_many, false));
				}
			}
		}

		if ($implode) {
			$str = substr($paren, 0 ,1);
			if (count($r_arr) == 1) {
				return reset($r_arr);
			} else {
				foreach($r_arr as $k => $v) {
					$r_arr[$k] = $k . $glue_key_val . $v;
				}
			}
			$str .= implode($glue, $r_arr);
			$str = $str . substr($paren, 1 ,2) ?: substr($paren, 0 ,1);
			return $str;
		}

		return $r_arr;
	}
*/
}
