<?php
namespace Locomo;
class Controller_Scffld_Helper
{
	/**
	 * fetch_temlpate()
	 */
	public static function fetch_temlpate($template)
	{
		$default = LOCOMOPATH.'config/scffld_templates/'.$template;
		$override = APPPATH.'config/scffld_templates/'.$template;

		if (file_exists($override))
		{
			return file_get_contents($override);
		}
		return file_get_contents($default);
	}

	/**
	 * replaces()
	 */
	public static function replaces($name, $tpl)
	{
		$tpl = str_replace ('XXX', ucfirst($name), $tpl);
		$tpl = str_replace ('xxx', strtolower($name), $tpl);
		$tpl = str_replace ('YYY', $name, $tpl);
		return $tpl;
	}

	/**
	 * get_nicename()
	 */
	public static function get_nicename($str)
	{
		preg_match('/\((.*?)\)/', $str, $m);
		return @$m[1];
	}

	/**
	 * remove_nicename()
	 */
	public static function remove_nicename($str)
	{
		return preg_replace('/\(.*?\)/', '', $str);
	}

	/**
	 * remove_length()
	 */
	public static function remove_length($str)
	{
		if (preg_match('/(.*?)\[\d+\]/', $str, $m))
		{
			return @$m[1];
		}else{
			return $str;
		}
	}

	/**
	 * modify_default()
	 */
	public static function modify_default($str)
	{
		// null
		if (strtolower($str) == 'null')
		{
			return 'null';
		}

		// other value
		if (preg_match('/default\[(.*?)\]/', $str, $m))
		{
//			if ( ! $m[1]) throw new \Exception('invalid default value');
			if ($m[1] == '')
			{
				return '';
			} elseif (is_numeric($m[1])) {
				return intval($m[1]);
			} else {
				return $m[1];
			}
		}
	}
}
