<?php
namespace Locomo;
class Observer_Srch extends \Orm\Observer
{
	public $_path = false;

	/**
	 * __construct
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_path  = isset($props['path']) ? $props['path'] : false;
	}

	/**
	 * after_save()
	 */
	public function after_save(\Orm\Model $obj)
	{
		if( ! $this->_path) throw new \InvalidArgumentException('\\Locomo\\Ovserver_Srchを使う時には、Modelでpathを設定してください。');

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
			if ($k == $pk) continue;
			$str.= $obj->$k;
		}

		// 関係テーブル
		foreach ($obj::relations() as $k => $v)
		{
			$rel = $obj->$k;
			if ( ! is_object($rel)) continue;
			foreach ($rel::properties() as $kk => $vv)
			{
				$str.= $obj->$k->$kk;
			}
		}

		$str = mb_convert_kana($str, "asKV");
		$str = str_replace(array(' ', "\n", "\r"), '', $str);
		$str = trim($str);

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
		$args = array(
			'path'       => $this->_path,
			'pid'        => $pid,
			'search'     => $str,
			'url'        => $url,
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
