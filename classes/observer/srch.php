<?php
namespace Locomo;
class Observer_Srch extends \Orm\Observer
{
	public $_path = false;
	public $_title = false;

	/**
	 * __construct
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_path  = isset($props['path']) ? $props['path'] : false;
		$this->_title  = isset($props['title']) ? $props['title'] : false;
	}

	/**
	 * after_save()
	 */
	public function after_save(\Orm\Model $obj)
	{
		if( ! $this->_path || ! $this->_title) throw new \InvalidArgumentException('\\Locomo\\Ovserver_Srchを使う時には、Modelでpathとtitleを設定してください。');

		// Model_Softで削除時にはbefore_delete()だけ走らせる
		$column = \Arr::get($obj::get_field_by_role('deleted_at'), 'lcm_field', 'deleted_at');
		if ( ! is_null($obj->$column)) return;

		// vals
		$pk = $obj::primary_key()[0];
		$pid = $obj->$pk;

		// 文字列
		$str = '';
		foreach ($obj::properties() as $k => $v)
		{
			// 対象外
			if (\Arr::get($v, 'lcm_srch_index', null) === false) continue;
			if ($k == $pk) continue;

			// 選択肢がある場合
			$opts = \Arr::get($v, 'form.options');
			if ($opts)
			{
				$str.= \Arr::get($v, 'label', ' ').' ';
				$str.= \Arr::get($v, 'form.options.'.$obj->$k, ' ').' ';
			}
			else
			{
				$str.= $obj->$k;
				$str.= ' ';
			}
		}

		// relation
		foreach ($obj::relations() as $k => $v)
		{
			if (
				! $v->cascade_save &&
				get_class($v) != 'Orm\BelongsTo'
			)
			{
				continue;
			}

			try
			{
				$rel = $obj->$k;

				if (is_array($rel))
				{
					foreach ($rel as $r)
					{
						if ( ! is_object($r)) continue;
						$pk = $r::primary_key()[0];

						foreach ($r::properties() as $kk => $vv)
						{
							if (\Arr::get($vv, 'lcm_srch_index', null) === false) continue;
							if ($kk == $pk) continue;

							$str.= $r->$kk;
							$str.= ' ';
						}
					}
				}
				else
				{
					if ( ! is_object($rel)) continue;
					$pk = $rel::primary_key()[0];
					foreach ($rel::properties() as $kk => $vv)
					{
						if (\Arr::get($vv, 'lcm_srch_index', null) === false) continue;
						if ($kk == $pk) continue;

						$str.= $obj->$k->$kk;
						$str.= ' ';
					}
				}

			}
			catch (\Orm\FrozenObject $e)
			{
				\Log::error('Observer Srch parent frozen');
			}
		}

		$str = mb_convert_kana($str, "asKV");
		$str = str_replace(array(' ', "\n", "\r"), '', $str);
		$str = strip_tags(trim($str));

		// url
		$url = '';
		$column = \Arr::get($obj::get_field_by_role('url'), 'lcm_field', 'url');
		if (array_key_exists($column, $obj::properties()))
		{
			$url = $obj->$column;
		}

		// find
		$srch = \Model_Srch::find('first', array(
			'where' => array(
				array('path', $this->_path),
				array('pid', $pid)
			)));

		// 復活時はこちらでとる
		if (is_subclass_of($obj, '\Orm\Model_Soft') && ! $srch)
		{
			$srch = \Model_Srch::find_deleted('first', array(
				'where' => array(
					array('path', $this->_path),
					array('pid', $pid)
				)));
			if ($srch) $srch->undelete($srch->id);
		}

		// 保存
		$title = $this->_title;
		$args = array(
			'title'  => $obj->$title,
			'path'   => $this->_path,
			'pid'    => $pid,
			'search' => $str,
			'url'    => $url,
		);
		if ($srch)
		{
			foreach ($args as $k => $v)
			{
				$srch->$k = $v;
			}
		}
		else
		{
			$srch = \Model_Srch::forge($args);
		}
		$srch->save();
	}

	/**
	 * before_delete()
	 */
	public function before_delete(\Orm\Model $obj)
	{
		// vals
		$pk = $obj::primary_key()[0];
		$pid = $obj->$pk;

		// 削除
		$srch = \Model_Srch::find('first', array(
			'where' => array(
				array('path', $this->_path),
				array('pid', $pid)
			)));

		if ($srch) $srch->delete($pid);
	}
}
