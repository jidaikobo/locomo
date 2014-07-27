<?php
namespace Kontiki;
abstract class Model_Revision extends \Kontiki\Model
{
	protected static $_table_name = 'revisions';

	protected static $_properties = array(
		'id',
		'controller',
		'controller_id',
		'data',
		'comment',
		'created_at',
	);

	/**
	 * find_revisions()
	*/
	public static function find_revisions($controller = null, $controller_id = null)
	{
		if(is_null($controller) || is_null($controller_id)) \Response::redirect($this->request->module);

		//リビジョンの一覧を取得
		$q = \DB::select('*');
		$q->from('revisions');
		$q->where('controller', $controller);
		$q->where('controller_id', $controller_id);
		return $q->as_object()->execute()->as_array();
	}

	/**
	 * find_revision()
	*/
	public static function find_revision($id = null)
	{
		is_null($id) and \Response::redirect($this->request->module);

		//リビジョンを取得
		$q = \DB::select('*');
		$q->from('revisions');
		$q->where('id', $id);
		return $q->as_object()->execute()->current();
	}

	/**
	 * insert_revision()
	*/
	public function insert_revision()
	{
		//当該コンテンツの最新データを取得
		$q = \DB::select('created_at');
		$q->from('revisions');
		$q->where('controller', $this->controller);
		$q->where('controller_id', $this->controller_id);
		$result = $q->execute()->current();
		$created_at = $result['created_at'];

		//configからrevision間隔を取得
		$config_path = PKGPATH.'kontiki/modules/revision/config/revision.php';
		$config_path_default = PKGPATH.'kontiki/modules_default/revision/config/revision.php';
		$config = file_exists($config_path) ? \Config::load($config_path) : \Config::load($config_path_default);

		//最新データと規定時間との比較 - $created_at がゼロのときは初めて
		if(
			$created_at && strtotime($created_at) >= time() - intval($config['revision_interval']) &&
			empty($this->comment)
		):
			return;
		endif;
	
		//保存
		$this->save();

		return;
	}
}