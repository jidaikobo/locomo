<?php
namespace Locomo;

class Auth_Acl_Locomoacl extends \Auth_Acl_Driver
{
	public static $_item;
	protected static $_valid_roles = array();

	public static function _init()
	{
		static::$_valid_roles = array_keys(\Config::get('locomoauth.roles'));
	}

	public function roles()
	{
		return static::$_valid_roles;
	}

	/**
	 * Parses a conditions string into it's array equivalent
	 *
	 * @rights	mixed	conditions array or string
	 * @return	array	conditions array formatted as array(module, controller, action)
	 *
	 */
	public static function _parse_conditions($rights)
	{
		if(! is_array($rights))
		{
			if ( ! is_string($rights) or strpos($rights, '/') === false)
			{
				throw new \InvalidArgumentException('Given rights where not formatted proppery. Formatting should be like module/controller/action. Received: '.$rights);
			}
			$rights = explode('/', $rights);
		}

		//single dot means controller.action
		if (count($rights) == 2)
		{
			$conditions = array(
				'module'     => '',
				'controller' => $rights[0],
				'action'     => $rights[1],
				'condition'  => \Arr::get($rights, 2, ''),
			);
		}else{
			$conditions = array(
				'module'     => strtolower($rights[0]),
				'controller' => $rights[1],
				'action'     => $rights[2],
				'condition'  => \Arr::get($rights, 3, ''),
			);
		}

		return $conditions;
	}

	/*
	 * has_access()
	 * @return bool
	 */
	public function has_access($condition, Array $entity)
	{
		//admins are all allowed
		if(in_array(\Auth::get('id'), array(-1, -2))) return true;

		//$condition_s = $condition;
		//parse condition to serialize
		$conditions = static::_parse_conditions($condition);
		$condition = serialize($conditions);

		//まずグループ権限を確認する
		$is_allow = in_array($condition, \Auth::get('allowed'));

		//グループが不許可なら、\Auth::get('allowed')の中にcondition付きの配列がないか確認する
		if( ! $is_allow && static::$_item):
			$allows = array_map('unserialize', \Auth::get('allowed'));

			//condition付きの配列を見つけたら確保し単純比較
			$conditioned_alowed = array();
			$n = 0;
			foreach($allows as $allow):
				if(empty($allow['condition'])) continue;
				$conditioned_alowed[$n]['condition'] = array_pop($allow);//conditionを取り除く
				$allow['condition'] = '';
				$conditioned_alowed[$n]['str'] = serialize($allow);
				$n++;
			endforeach;

			$key = \Arr::search($conditioned_alowed, $condition);

			//単純比較で存在しなかったらfalse
			if( ! $key) return false;

			//条件をパース
			list($p, $c) = explode('.',$key);
			$condition_str = \Arr::get($conditioned_alowed, "{$p}.condition");
			$condition_strs = explode(',', trim($condition_str, '[]'));
			$condition_strs = array_map('trim', $condition_strs);

			$target_column = isset(static::$_item->template->get_active_request('content')->$condition_strs[2]) ?
				static::$_item->template->get_active_request('content')->$condition_strs[2] :
				false;
			//そもそも条件を満たすフィールドを持たないのだったらfalse
			if( ! $target_column) return false;

			//条件を照合
			$id = $condition_strs[0] == 'user_id' ? \Auth::get('id') : '';
			switch($condition_strs[1]):
				case '=':
					return ($id == $target_column); break;
				case '<':
					return ($id < $target_column); break;
				case '>':
					return ($id > $target_column); break;
				case '<=':
					return ($id <= $target_column); break;
				case '>=':
					return ($id >= $target_column); break;
				case '=':
					return ($id <> $target_column); break;
				default:
					return false;
			endswitch;
		endif;

		return $is_allow;
	}

	/*
	 * set_item()
	 * used by $this->has_access()
	 * @return bool
	 */
	public static function set_item($obejct = null)
	{
		static::$_item = $obejct;
	}
}
