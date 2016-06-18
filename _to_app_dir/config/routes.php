<?php
$ret = array(
	'_root_'  => 'sys/home',
	'_404_'   => function () {
		// set language
		$uri = \Input::uri();
		$ext = \Input::extension();
		$uri.= $ext ? '.'.$ext : '';
		$filename = substr($uri, 1);
		$segments = explode('/', rtrim($uri, '/'));
		$lang = \Lang::get_lang();
		$is_default_lang = true;

		// search and set langage
		if (in_array($segments[1], array_map('basename', glob(LOCOMOPATH.'lang/*'))))
		{
			// default language - redirect
			if ($segments[1] === $lang)
			{
				header('location:/'.join('/', array_splice($segments, 2)));
				exit;
			}
			// not default language
			else
			{
				$is_default_lang = false;
				$lang = $segments[1];
				\Config::set('language', $lang);
				$filename = substr($filename, 3);
			}
		}

		// pg
		if (
			$pgs = \Model_Pg::find('first', array(
				'where' => array(
					array('path', $filename),
					array('lang', $lang),
				)
			))
		)
		{
			return \Request::forge('pg/view/'.$pgs->id, false)->execute();
		}
		// languages
		else if ($is_default_lang === false)
		{
			// home
			if (count($segments) <= 2)
			{
				return \Request::forge('sys/home/', false)->execute();
			}
			// lang
			else
			{
				return \Request::forge(join('/', array_splice($segments, 2)), false)->execute();
			}
		}
		// 404
		return \Request::forge('sys/404', false)->execute();
	}
);

return $ret;

/*
return array(
	'_root_'  => 'sys/home',
	'_404_'   => 'sys/404',

	// pg
	'pg/create'               => 'pg/create',
	'pg/index_(:any)'         => 'pg/index_$1',
	'pg/edit/(:num)'          => 'pg/edit/$1',
	'pg/delete/(:num)'        => 'pg/delete/$1',
	'pg/purge_confirm/(:num)' => 'pg/purge_confirm/$1',
	'pg/purge/(:num)'         => 'pg/purge/$1',
	'pg/(:any)'               => 'pg/view/$1',
);
*/