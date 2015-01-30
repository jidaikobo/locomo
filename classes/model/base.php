<?php
namespace Locomo;
class Model_Base extends \Orm\Model_Soft
{
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	public static $_conditions = array();
	/*
	 * default field names
	 */
	protected static $_default_subject_field_name    = 'subject';
	protected static $_default_created_field_name    = 'created_at';
	protected static $_default_expired_field_name    = 'expired_at';
	protected static $_default_visibility_field_name = 'is_visible';
	protected static $_default_creator_field_name    = 'creator_id';

	/*
	 * default field names
	 */
	protected static $_cache_form_definition;


	/*
	 * default authorize options
	 */
	protected static $_authorize_methods = array(
		'auth_expired',
		'auth_created',
		'auth_deleted',
		'auth_visibility',
	);

	// todo 不要
	/*
	 * _option_options - see sample at \User\Model_Usrgrp
	 */
	protected static $_option_options = array();

	// todo 不要
	public function __construct(array $data = array(), $new = true, $view = null, $cache = true)
	{
		//depend_modules
		parent::__construct($data, $new, $view, $cache);

		//add_authorize_methods
		static::add_authorize_methods();
	}

	// todo 不要
	/**
	 * add_authorize_methods()
	 */
	public static function add_authorize_methods()
	{
// see sample at \Model_Traits_Wrkflw -マージでもいいか？
//		if ( ! in_array('auth_sample', static::$_authorize_methods)):
//			static::$_authorize_methods[] = 'auth_sample';
//		endif;
	}

	/**
	 * get_default_field_name($str)
	 */
	public static function get_default_field_name($str = null)
	{
		switch($str):
			case 'subject':
				return isset(static::$_subject_field_name) ?
					static::$_subject_field_name :
					static::$_default_subject_field_name;
			case 'created':
				return isset(static::$_created_field_name) ?
					static::$_created_field_name :
					static::$_default_created_field_name;
			case 'expired':
				return isset(static::$_expired_field_name) ?
					static::$_expired_field_name :
					static::$_default_expired_field_name;
			case 'visibility':
				return isset(static::$_visibility_field_name) ?
					static::$_visibility_field_name :
					static::$_default_visibility_field_name;
			case 'creator':
				return isset(static::$_creator_field_name) ?
					static::$_creator_field_name :
					static::$_default_creator_field_name;
		endswitch;
		return false;
	}

	// todo 不要
	/*
	 * get_table_name()
	 */
	public static function get_table_name()
	{
		return static::$_table_name;
	}

	// todo 不要? get_pk かどちらか一方で良い
	/**
	 * get_primary_keys()
	 */
	public static function get_primary_keys($mode = '')
	{
		if ($mode == 'first'):
			return reset(static::$_primary_key);
		endif;
		return static::$_primary_key;
	}


	// todo 不要 ./packages/locomo/classes/actionset.php:
	/**
	 * get_pk()
	 */
	public function get_pk()
	{
		$pk = reset(static::$_primary_key);
		return $this->$pk ?: false;
	}

	/**
	 * get_original_values()
	 */
	public function get_original_values()
	{
		return $this->_original;
	}

	/*
	 * authorized_option()
	 * adjust Model::find(#, $options)
	 */
	public static function authorized_option($options = array(), $mode = null)
	{
		$module     = \Request::main()->module;
		$controller = \Request::main()->controller;

		//add_authorize_methods
		static::add_authorize_methods();

		//view_anywayが許されているユーザにはsoft_delete判定を外してすべて返す
		if (\Auth::instance()->has_access($controller.DS.'view_anyway')) {
			static::disable_filter();
		} else {
			//モデルが持っている判定材料を、適宜$optionsに足す。
			foreach(self::$_authorize_methods as $authorize_method):
				$options = static::$authorize_method($module, $controller, $options, $mode);
			endforeach;
		}
		return $options;
	}

	/*
	 * auth_expired()
	 */
	public static function auth_expired($module = null, $controller = null, $options = array(), $mode = null)
	{
		$column = isset(static::$_expired_field_name) ?
			static::$_expired_field_name :
			static::$_default_expired_field_name;
		if (
			isset(static::properties()[$column]) &&
			! \Auth::instance()->has_access($controller.'/view_expired')
		)
		{
			$options['where'][] = array(array($column, '>', date('Y-m-d H:i:s'))
				, 'or' => (array($column, 'is', null)));
		}
		return $options;
	}

	/*
	 * auth_created()
	 */
	public static function auth_created($module = null, $controller = null, $options = array(), $mode = null)
	{
		$column = isset(static::$_created_field_name) ?
			static::$_created_field_name :
			static::$_default_created_field_name;
		if (
			isset(static::properties()[$column]) &&
			! \Auth::instance()->has_access($controller.'/view_yet')
		) {
			$options['where'][] = array(array($column, '<=', date('Y-m-d H:i:s'))
				, 'or' => (array($column, 'is', null)));
		}
		return $options;
	}

	/*
	 * auth_deleted()
	 */
	public static function auth_deleted($module = null, $controller = null, $options = array(), $mode = null)
	{
		if (
			(static::forge() instanceof \Orm\Model_Soft) &&
			! \Auth::instance()->has_access($controller.'/view_deleted')
		) {
			static::enable_filter();
		} else {
			static::disable_filter();
		}
		return $options;
	}

	/*
	 * auth_visibility()
	 */
	public static function auth_visibility($module = null, $controller = null, $options = array(), $mode = null)
	{
		$column = isset(static::$_visibility_field_name) ?
			static::$_visibility_field_name :
			static::$_default_visibility_field_name;

		if (
			isset(static::properties()[$column]) &&
			! \Auth::instance()->has_access($controller.'/view_invisible')
		) {
			$options['where'][] = array($column, '=', true);
		}
		return $options;
	}


	/*
	 * @param   array     $input_post
	 * @param   Fieldset  $form (for validation)
	 * @param   bool      $repopulate populate input value
	 * @param   bool      $validation validate
	 * @param   bool      $delete_else unset value delete all in has_many relation
	 * @return  bool      whether validation succeeded
	 *
	 * @important   \Response::redirect() after save() or Regenerate Fieldset instance
	 */
	public function cascade_set($input_post = null, $form = null, $repopulate = false, $validation = true, $delete_else = false)
	{
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
					if ($delete_else and !isset($input_post[$k][$kk])) { // $delete_else = true なら セットされていないものは全て消去
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
					$repopulate and $form->field($k)->set_value(array_keys($this[$k]));

				// 何も飛んでこなかったとき、form に存在していれば 全て unset する
				} else {
					if ($form->field($k) instanceof \Fieldset_Field) unset($this->{$k});
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
	 * get_options()
	 */
	public static function get_options($options = array(), $label = 'name')
	{
		$primary_key = reset(self::$_primary_key);
		$items = self::find('all', $options);
		$items = \Arr::assoc_to_keyval($items, $primary_key, $label);
		return $items;
	}


	/*
	 * @param array    $options conditions for find. limit は pagination_config['perpage'] を使うため無視される
	 * @param str      $model model class name
	 * @param bool     $use_get_query use get query paramaters
	 * @param array    $pagination_config overwrite $this->pagination_config
	 *
	 * @return Model finded
	 */
	public static function paginated_find($options = array(), $use_get_query = true)
	{
		if (\Input::get('paged')) \Pagination::set_config('uri_segment', 'paged');
		if ($use_get_query) {
			$input_get = \Input::get();
		} else {
			$input_get = array();
		}
		if ($use_get_query and \Input::get()) {
			if (\Input::get('orders')) {

				$orders = array();
				foreach (\Input::get('orders') as $k => $v) {
					if (($dot_pos = strpos($k, '.')) > 0) { // リレーションを見る
						$model = static::relations( substr($k, 0, $dot_pos) )->model_to;
						$relate = substr($k, 0, $dot_pos);
						$k = substr($k, $dot_pos+1);
						if ( ! in_array($k, array_keys($model::properties()))) continue;
						$options['related'][$relate]['where'][] = array('id', '!=', 0);
						$options['related'][$relate]['order_by'][$k] = $v;
						// 既存の conditions の order_by を キャンセル
						$options['order_by'] = $orders;
					} else {
						if ( ! in_array($k, array_keys(static::properties()))) continue;
						$orders[$k] = $v;
						$options['order_by'] = $orders;
					}
				}
			}
			if (\Input::get('searches')) {
				foreach (\Input::get('searches') as $k => $v) {
					if ($v == false) continue;
					if ( ! in_array($k, array_keys(static::properties()))) continue;
					$options['where'][] = array($k, '=', $v);
				}
			}
			if (\Input::get('likes')) {
				$likes = array();
				foreach (\Input::get('likes') as $k => $v) {
					if ($v == false) continue;
					if ( ! in_array($k, array_keys(static::properties()))) continue;
					$options['where'][] = array($k, 'LIKE', '%'.$v.'%');
				}
			}
			if (\Input::get('all')) {
				foreach (static::$_properties as $k => $v) {
					if (in_array($v, static::$_primary_key)) continue;
					$field = is_array($v) ? $k : $v;// properties sometimes only has key without value
					$options['or_where'][] = array($field, 'LIKE', '%'.\Input::get('all').'%');
				}
			}

		}

		$count_all = static::count();
		$count = static::count($options);

		\Pagination::set('total_items', $count);


		if (\Input::get('limit')) \Pagination::set('per_page', \Input::get('limit'));
		$options['rows_limit'] = \Pagination::get('per_page');
		$options['rows_offset'] = \Pagination::get('offset');

/* 不具合有りました
		foreach (static::relations() as $ref => $v) {
			// var_dump($ref);
			// レイジーロードしない
			$options['related'][] = $ref;
		}
 */
		$objs = static::find('all', $options);
		\Pagination::$refined_items = count($objs);

		return $objs;
	}


	/*
	 * csv 用関数
	 * @param $options     find の時に使った options
	 * @param $glue       string length 1~2
	 * @param $paren
	 */
	public function to_csv(
		$options = array(),
		$rel_names = array(),
		$field_joins = array(),
		$glue = ',',
		$paren = '()',
		$glue_key_val = ':',
		$implode = false
	) {

		$options = array_merge(static::condition(), $options);
		$properties = static::properties();

		$o_arr = array(); // return

		foreach($this->_data as $kk => $vv) {
			if ($options['select'] and !in_array($kk, $options['select'])) continue;

			// if (array_key_exists($kk, $properties) and isset($properties[$kk]['label'])) var_dump($properties[$kk]['label']); die();
			if(isset($properties[$kk]['form']['options'][$vv])) {
				$vv = $properties[$kk]['form']['options'][$vv];
			}
			$o_arr[$kk] = $vv;
		}

		$r_arr = isset($o_arr['id']) ? array('id' => $o_arr['id'],) : array();;
		$skip_keys = array();
		// var_dump($field_joins); die();
		foreach ($field_joins as $k => $v) {
			foreach ($v as $vv) {
				$skip_keys[] = $vv;
			}
		}



		// 並べ替えと join
		foreach ($o_arr as $k => $v) {
			if ($k == 'id') continue;
			if (in_array($k, $skip_keys)) continue;
			array_key_exists($k, $properties) and isset($properties[$k]['label']) ? $key = $properties[$k]['label'] : $key = $k;
			if(isset($properties[$k]['form']['options'][$v])) {
				$v .= $properties[$k]['form']['options'][$v];
			}
			if (array_key_exists($k, $field_joins)) {
				foreach ($field_joins[$k] as $vv){
					if (array_key_exists($vv, $properties)) {
						$v .= $o_arr[$vv];
					} else {
						$v .= $vv;
					}
				}
			}
			$r_arr[$key] = $v;
		}

		if ($this->_data_relations) {
			foreach ($this->_data_relations as $rel_name => $dr) {
				$rel_options = isset($options['related'][$rel_name]) ? $options['related'][$rel_name] : array();
				$rel_field_joins = (array_key_exists($rel_name, $field_joins)) ? $field_joins[$rel_name] : array();;
				if (array_key_exists($rel_name, $rel_names)) $rel_name = $rel_names[$rel_name];
				if (is_array($dr)) {
				   foreach($dr as $dr_val) {
						$r_arr[$rel_name] = $dr_val->to_csv($rel_options, $rel_names, $rel_field_joins, $glue, $paren, $glue_key_val, true);
				   }
				} else {
					//$r_arr[$rel_name] = $dr->to_csv($rel_options, $rel_names, $glue, $paren, $glue_key_val, true);

					// var_dump($dr->to_csv($rel_options, $rel_names, $glue, $paren, $glue_key_val, false));
					$r_arr = array_merge($r_arr, $dr->to_csv($rel_options, $rel_names, $rel_field_joins, $glue, $paren, $glue_key_val, false));
				}
			}
		}

		if ($implode) {
			$str = substr($paren, 0 ,1);
			foreach($r_arr as $k => $v) {
				$r_arr[$k] = $k . $glue_key_val . $v;
			}
			$str .= implode($glue, $r_arr);
			$str = $str . substr($paren, 1 ,2) ?: substr($paren, 0 ,1);
			return $str;
		}
		return $r_arr;
	}






	/**
	 * form_definition()
	 */
	public static function form_definition($factory = 'form', $obj = null) {

		$form = \Fieldset::forge($factory);

		$form->add_model($obj)->populate($obj, true);

		$form->add('submit', '', array('type' => 'submit', 'value' => '保存', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}</div>');;

		return $form;
	}

	/**
	 * search_form_base()
	 */
	public static function search_form_base($title = '項目一覧')
	{
		// forge
		$form = \Fieldset::forge('search_form_base');

		// add opener before unrefine
		$sortinfo     = \Pagination::sort_info(get_called_class());
		$total        = \Pagination::get("total_items");
		$current_page = \Pagination::get("current_page");
		$per_page     = \Pagination::get("per_page");
		$refined      = \Pagination::$refined_items;
		$from         = $current_page == 1 ? 1 : ($current_page - 1) * $per_page + 1;
		$to           = $refined <= $per_page ? $from + $refined - 1 : $from + $per_page - 1;

		$sortinfo_txt = "{$sortinfo} <span class=\"nowrap\">{$from}から{$to}件 / 全{$total}件</span>";
		$sortinfo = $total ? $sortinfo_txt : '項目がありません' ;

		$form
			->add('opener','',array('type' => 'text'))
			->set_template('
				<h1 id="page_title" class="clearfix">
					'.$title.'
					<span class="sort_info">'.$sortinfo.'</span>
					<span class="icon fr">
						<a href="javascript: void(0);" class="toggle_item disclosure">
							<img src="'.\Uri::base().'sys/fetch_view/img/system/mark_search.png" alt="">
							<span class="hide_if_smalldisplay" aria-hidden="true" role="presentation">検索</span>
							<span class="skip"> エンターで検索条件を開きます</span>
						</a>
					</span>
				</h1>
				<div class="hidden_item">
				<section class="form_group">
					<h1 class="skip">検索</h1>
					<form class="search">
			');

		// submit	
		$form
			->add_after('submit', '', array('type' => 'submit', 'value' => '検索', 'class' => 'button primary'), array(), 'opener')
			->set_template('
				<div class="submit_button">'.
				\Html::anchor(\Uri::current(), '絞り込みを解除', ['class' => 'button']).'
				{field}
				</div><!--/.submit_button-->
				</form>
			</section><!-- /.form_group -->
			</div><!-- /.hidden_item -->'
			);

		return $form;
	}

	/**
	 * plain_definition()
	 */
	public static function plain_definition($factory = 'plain', $obj = null) {
		return static::form_definition($factory, $obj);
	}

}
