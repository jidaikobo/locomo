<?php
/*
 * Base のコントローラー 直接は呼ばない
 */
namespace Locomo;
class Controller_Bkmk extends \Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => 'ブックマーク', // for human's name
		'explanation'  => 'ブックマークの管理を行います。',
		'main_action'  => 'bulk', // main action
		'main_action_name' => 'ブックマーク管理', // main action's name
		'main_action_explanation' => 'ブックマークの一覧です。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => false, // true: hide from admin bar
		'order'        => 1200, // order of appearance
	);

	public function before()
	{
		parent::before();
		$model = $this->model_name;
		$model::$_options['where'][] = array(
			array('user_id', \Auth::get('id')),
		);
	}
	/*
	 * action_index_admin
	 */
	public function action_index_admin()
	{
		// bulk
		parent::index_admin();
	}

	/**
	 * action_bulk()
	 */
	public function action_bulk($page = 1)
	{
		// bulk
		parent::bulk(array(
			'page' => 1,
			'add' => 3,
			'is_redirect'  => true,
			'is_deletable' => true
			)
		);

		// add_actionset - back to index at edit
		$action['urls'][] = \Html::anchor(static::$main_url, '一覧へ');
		\Actionset::add_actionset(static::$controller, 'ctrl', $action);
	}


	/*
	 * post_bookmarks
	 */
	public function post_bookmarks()
	{
		$model = $this->model_name;

		$objects = $model::find('all', $model::$_options);
		echo $this->response($objects, 200);
		die();
	}

	/*
	 * post_bookmarks
	 */
	public function post_add()
	{
		if (! $id = \Auth::get('id')) throw new \HttpNotFoundException;

		$model = $this->model_name;
		$object = $model::find('last', $model::$_options);

		if ($object)
		{
			$seq = intval($object->seq) + 1;
		}
		else
		{
			$seq = 1;
		}
		$new_object = $model::forge(array(
			'user_id' => $id, // どっちにせよ書き換わる
			'name' => \Input::post('name'),
			'url' => \Input::post('url'),
			'seq' => $seq,
		));

		if ($new_object->save())
		{
			echo true;
		}
		else
		{
			throw new HttpServerErrorException;
		}

		die();
	}

}


