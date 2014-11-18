<?php
namespace Scaffold;
class Helper
{
	/**
	 * fetch_temlpate()
	 */
	public static function fetch_temlpate($template)
	{
		$default = dirname(__DIR__).'/module_templates/'.$template;
		$override = APPPATH.'locomo/modules/scaffold/module_templates/'.$template;

		if(file_exists($override))
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
		if(preg_match('/(.*?)\[.\d+\]/', $str, $m)){
			return @$m[1];
		}else{
			return $str;
		}
	}
}
