<?php
namespace Locomo_Core;
class Model_Base extends \Orm\Model_Soft
{
	/**
	 * _primary_name
	 * to draw items title
	 */
	protected static $_primary_name = '';

	/*
	 * default field names
	 */
	protected static $_default_created_field_name    = 'created_at';
	protected static $_default_expired_field_name    = 'expired_at';
	protected static $_default_visibility_field_name = 'is_visible';
	protected static $_default_creator_field_name    = 'creator_id';
	protected static $_default_workflow_field_name   = 'workflow_status';

	protected static $_depend_modules = array();

	/*
	 * default authorize options
	 */
	protected static $_authorize_methods = array(
		'auth_expired',
		'auth_created',
		'auth_deleted',
		'auth_visibility',
		'auth_owner',
		'auth_workflow',
	);

	/*
	 * _option_options - see sample at \User\Model_Usergroup
	 */
	protected static $_option_options = array();

	/*
	 * __construct
	 */
	public function __construct(array $data = array(), $new = true, $view = null, $cache = true)
	{
		parent::__construct($data, $new, $view, $cache);
		foreach (self::$_depend_modules as $module) {
			\Module::load($module);
		}
	}

	/**
	 * get_option_options()
	 */
	public static function get_option_options($key = null)
	{
		return isset(static::$_option_options[$key]) ? static::$_option_options[$key] : static::$_option_options;
	}

	/**
	 * get_table_name()
	 */
	public static function get_table_name()
	{
		return static::$_table_name;
	}

	/**
	 * get_original_values()
	 */
	public function get_original_values()
	{
		return $this->_original;
	}

	/**
	 * get_primary_name()
	 */
	public static function get_primary_name()
	{
		return static::$_primary_name;
	}

	/*
	 * authorized_option()
	 * adjust Model::find(#, $options)
	 */
	public static function authorized_option($options = array(), $mode = null)
	{
		$userinfo = \User\Controller_User::$userinfo;
		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));

		//view_anywayが許されているユーザにはsoft_delete判定を外してすべて返す
		if (\Acl\Controller_Acl::auth($controller.'/view_anyway', $userinfo)) {
			static::disable_filter();
		} else {
			//モデルが持っている判定材料を、適宜$optionsに足す。
			foreach(self::$_authorize_methods as $authorize_method):
				$options = self::$authorize_method($controller, $userinfo, $options, $mode);
			endforeach;
		}

		return $options;
	}

	/*
	 * auth_expired()
	 */
	public static function auth_expired($controller = null, $userinfo = null, $options = array(), $mode = null)
	{
		$column = isset(static::$_expired_field_name) ?
			static::$_expired_field_name :
			static::$_default_expired_field_name;
		if (
			isset(static::properties()[$column]) &&
			! \Acl\Controller_Acl::auth($controller.'/view_expired', $userinfo)
		) {
			$options['where'][] = array(array($column, '>', date('Y-m-d'))
				, 'or' => (array($column, 'is', null)));
		}
		return $options;
	}

	/*
	 * auth_created()
	 */
	public static function auth_created($controller = null, $userinfo = null, $options = array(), $mode = null)
	{
		$column = isset(static::$_created_field_name) ?
			static::$_created_field_name :
			static::$_default_created_field_name;
		if (
			isset(static::properties()[$column]) &&
			! \Acl\Controller_Acl::auth($controller.'/view_yet', $userinfo)
		) {
			$options['where'][] = array(array($column, '<', date('Y-m-d'))
				, 'or' => (array($column, 'is', null)));
		}
		return $options;
	}

	/*
	 * auth_deleted()
	 */
	public static function auth_deleted($controller = null, $userinfo = null, $options = array(), $mode = null)
	{
		if (
			(static::forge() instanceof \Orm\Model_Soft) &&
			! \Acl\Controller_Acl::auth($controller.'/view_deleted', $userinfo)
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
	public static function auth_visibility($controller = null, $userinfo = null, $options = array(), $mode = null)
	{
		$column = isset(static::$_visibility_field_name) ?
			static::$_visibility_field_name :
			static::$_default_visibility_field_name;

		if (
			isset(static::properties()[$column]) &&
			! \Acl\Controller_Acl::auth($controller.'/view_invisible', $userinfo)
		) {
			$options['where'][] = array($column, '=', 'false');
		}
		return $options;
	}

	/*
	 * auth_owner()
	 */
	public static function auth_owner($controller = null, $userinfo = null, $options = array(), $mode = null)
	{
		//グループに許されている場合はオーナ権限は判定する必要がない（管理者もこれで貫通する）
		if(\Acl\Controller_Acl::auth($controller.DS.\Request::main()->action, $userinfo))
			return $options;

		//グループに許されていない場合
		$column = isset(static::$_creator_field_name) ?
			static::$_creator_field_name :
			static::$_default_creator_field_name;

		if (
			isset(static::properties()[$column]) &&
			\Acl\Controller_Acl::is_exists_owner_auth($controller, 'view')
		) {
			$options['where'][] = array($column, '=', $userinfo['user_id']);
		}

		return $options;
	}

	/*
	 * auth_workflow()
	 */
	public static function auth_workflow($controller = null, $userinfo = null, $options = array(), $mode = null)
	{
		$column = isset(static::$_workflow_field_name) ?
			static::$_workflow_field_name :
			static::$_default_workflow_field_name;

		//in_progressの項目は編集できない
		if (isset(static::properties()[$column]) && $mode == 'edit') {
			$options['where'][] = array(array($column, '<>', 'in_progress'));
		}

		//in_progress、before_progressの項目は、権限のない人には閲覧できない
		
/*
echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">' ;
var_dump( $userinfo ) ;
echo '</textarea>' ;
die();
*/
		if (isset(static::properties()[$column]) && $mode != 'edit') {
			$options['where'][] = array(array($column, '<>', 'in_progress'));
		}

		return $options;
	}

	/*
	 * @param   array     $input_post
	 * @param   Fieldset  $form (for validation)
	 * @param   bool      $repopulate populate input value
	 * @return  bool      whether validation succeeded
	 * @important   \Response::redirect() after save() or Regenerate Fieldset instance
	 */
	public function cascade_set($input_post = null, $form = null, $repopulate = false)
	{
		if (!$input_post) $input_post = \Input::post();

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
			!is_null($form) and $validated[] = $form->validation()->run($input_post[$model_name]);
			$repopulate and $form->populate($input_post[$model_name]);

		// テーブル名から
		} elseif (isset($input_post[$table_name])) {
			$this->set($input_post[$table_name]);
			!is_null($form) and $validated[] = $form->validation()->run($input_post[$table_name]);
			$repopulate and $form->populate($input_post($table_name));

		// 何もなければ、生のプロパティを relations になければつっこむ
		} else {
			$this->set(\Arr::filter_keys($input_post, array_keys(static::relations()), true));
			!is_null($form) and $validated[] = $form->validation()->run($input_post);
			$repopulate and $form->populate($input_post);
		}
		// => root の設定ココまで

		// relations に応じて、object($this) にオブジェクトを足していく
		foreach (static::relations() as $k => $v) {

			// has_one or belongs_to
			if (static::relations()[$k]->cascade_save and (static::relations()[$k] instanceof \Orm\HasOne or static::relations()[$k] instanceof \Orm\BelongsTo)) {
				if (!$form->field($k)) continue;

				isset($input_post[$k]) and $this[$k]->set($input_post[$k]);
				!is_null($form) and $validated[] = $form->field($k)->validation()->run($input_post[$k]);
				$repopulate and $form->field($k)->populate($input_post[$k]);

			// has_many
			} elseif (static::relations()[$k]->cascade_save and static::relations()[$k] instanceof \Orm\HasMany ) {

				// hm 既存列
				foreach ($this[$k] as $kk => $vv) {
					if (isset($input_post[$k][$kk]['_delete'])){ // _deleted
						unset($this->{$k}[$kk]);
					} else {
						isset($input_post[$k][$kk]) and $vv->set($input_post[$k][$kk]);
					}
					!is_null($form) and $validated[] = $form->field($k)->field($k.'_row_'.$kk)->validation()->run($input_post[$k][$kk]);
					$repopulate and $form->field($k)->field($k.'_row_'.$kk)->populate($input_post[$k][$kk]);
				}

				// hm 新規列
				if (isset($input_post[$k.'_new'])) {
					$hm_model = static::relations()[$k]->model_to;
					if (!is_null($input_post[$k.'_new'])) {
						foreach ($input_post[$k.'_new'] as $kk => $vv) {
							$vv = array_filter($vv);
							if (!empty($vv)) { // array_filter で引数が全て空なら 空の配列が返る -> 新規の保存なし
								$this->{$k}[] = $hm_model::forge()->set($vv);
								!is_null($form) and $validated[] = $form->field($k)->field($k.'_new_'.$kk)->validation()->run($input_post[$k.'_new'][$kk]);
								$repopulate and $form->field($k)->field($k.'_new_'.$kk)->populate($input_post[$k.'_new'][$kk]);
							}
						}
					}
				}

			// many_many
			// また、cascade_save は予期せぬ動作をする事から対応していない為 false のみに対応している true の際は別で設定する
			// 関係テーブルはcascadeに関係なく依存する
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
	public static function get_options($options, $label)
	{
		$primary_key = reset(self::$_primary_key);
		$items = self::find('all', $options);
		$items = \Arr::assoc_to_keyval($items, $primary_key, $label);
		return $items;
	}
}
