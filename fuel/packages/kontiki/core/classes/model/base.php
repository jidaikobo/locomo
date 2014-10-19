<?php
namespace Kontiki_Core;
class Model_Base extends \Orm\Model_Soft
{
	/*
	 * default field names
	 */
	protected static $_default_created_field_name    = 'created_at';
	protected static $_default_expired_field_name    = 'expired_at';
	protected static $_default_visibility_field_name = 'is_visible';

	/*
	 * default authorize options
	 */
	protected static $_authorize_methods = array(
		'auth_expired',
		'auth_created',
		'auth_deleted',
		'auth_visibility',
	);

	/*
	 * set_authorize_methods()
	 */
	public static function set_authorize_methods($method)
	{
		
	}

	/*
	 * authorized_option()
	 * adjust Model::find(#, $options)
	 */
	public static function authorized_option($options = array())
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
				$options = self::$authorize_method($controller, $userinfo, $options);
			endforeach;

			// worlflow 権限は invisible
			// コントローラで、in_progressだったらstatusをinvisibleに
			if (
				isset(static::properties()['workflow_status']) &&
				!\Acl\Controller_Acl::auth($controller . '/view_invisible', $userinfo)
			) {
				$conditions['where'][] = array('workflow_status', '!=', 'in_progress');
			}
		}

		return $options;
	}

	/*
	 * auth_expired()
	*/
	public static function auth_expired($controller = null, $userinfo = null, $options = array())
	{
		$column = isset(static::$_expired_field_name) ?: static::$_default_expired_field_name;
		if (
			isset(static::properties()[$column]) &&
			! \Acl\Controller_Acl::auth($controller . '/view_expired', $userinfo)
		) {
			$options['where'][] = array(array($column, '<', date('Y-m-d'))
				, 'or' => (array($column, 'is', null)));
		}
		return $options;
	}

	/*
	 * auth_created()
	*/
	public static function auth_created($controller = null, $userinfo = null, $options = array())
	{
		$column = isset(static::$_created_field_name) ?: static::$_default_created_field_name;
		if (
			isset(static::properties()[$column]) &&
			!\Acl\Controller_Acl::auth($controller . '/view_yet', $userinfo)
		) {
			$options['where'][] = array(array($column, '<', date('Y-m-d'))
				, 'or' => (array($column, 'is', null)));
		}
		return $options;
	}

	/*
	 * auth_deleted()
	*/
	public static function auth_deleted($controller = null, $userinfo = null, $options = array())
	{
		if (
			(static::forge() instanceof \Orm\Model_Soft) &&
			!\Acl\Controller_Acl::auth($controller . '/view_deleted', $userinfo)
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
	public static function auth_visibility($controller = null, $userinfo = null, $options = array())
	{
		$column = isset(static::$_visibility_field_name) ?: static::$_default_visibility_field_name;
		if (
			isset(static::properties()[$column]) &&
			!\Acl\Controller_Acl::auth($controller . '/view_invisible', $userinfo)
		) {
			$options['where'][] = array($column, '=', 'false');
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
				isset($input_post[$k]) and $this[$k]->set($input_post[$k]);
				// var_dump($k);
				// var_dump($form->field('receiver'));
				// var_dump(\$input_post($k));
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
					!is_null($form) and $validated[] = $form->field($k)->field($k . '_row_' . $kk)->validation()->run($input_post[$k][$kk]);
					$repopulate and $form->field($k)->field($k . '_row_' . $kk)->populate($input_post[$k][$kk]);
				}



				// hm 新規列
				if (isset($input_post[$k . '_new'])) {
					$hm_model = static::relations()[$k]->model_to;
					if (!is_null($input_post[$k . '_new'])) {
						foreach ($input_post[$k . '_new'] as $kk => $vv) {
							$vv = array_filter($vv);
							if (!empty($vv)) { // array_filter で引数が全て空なら 空の配列が返る -> 新規の保存なし
								$this->{$k}[] = $hm_model::forge()->set($vv);
								!is_null($form) and $validated[] = $form->field($k)->field($k . '_new_' . $kk)->validation()->run($input_post[$k . '_new'][$kk]);
								$repopulate and $form->field($k)->field($k . '_new_' . $kk)->populate($input_post[$k . '_new'][$kk]);
							}
						}
					}
				}

			// many_many
			// また、cascade_save は予期せぬ動作をする事から対応していない為 false のみに対応している true の際は別で設定する
			// 関係テーブルはcascadeに関係なく依存する
			} elseif (!static::relations()[$k]->cascade_save and
				static::relations()[$k] instanceof \Orm\ManyMany and
				isset($input_post[$k])
			) {
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
			}
		}

		if (!is_null($form)) {
			return !in_array(false, $validated);
		} else {
			return true;
		}

	}
}
