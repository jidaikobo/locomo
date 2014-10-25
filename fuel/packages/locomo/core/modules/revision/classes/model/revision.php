<?php
namespace Revision;
class Model_Revision extends \Locomo\Model_Base
{
	protected static $_table_name = 'revisions';

	protected static $_properties = array(
		'id',
		'model',
		'pk_id',
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
		$q->where('model', $controller);
		$q->where('pk_id', $controller_id);
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
	 * find_options_revisions()
	*/
	public static function find_options_revisions($optname = null)
	{
		if(is_null($optname)) \Response::redirect(\Uri::base());

		//リビジョンの一覧を取得
		$q = \DB::select('*');
		$q->from('revisions');
		$q->where('model', $optname);
		return $q->as_object()->execute()->as_array();
	}

	/**
	 * find_options_revision()
	*/
	public static function find_options_revision($optname = null, $datetime = null)
	{
		if(is_null($optname) || is_null($datetime)) \Response::redirect(\Uri::base());
		$datetime = date('Y-m-d H:i:s', $datetime);

		//リビジョンを取得
		$q = \DB::select('*');
		$q->from('revisions');
		$q->where('model', $optname);
		$q->where('created_at', $datetime);
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
		$q->where('model', $this->model);
		$q->where('pk_id', $this->pk_id);
		$q->order_by('created_at', 'DESC');
		$result = $q->execute()->current();
		$created_at = $result['created_at'];

		//configからrevision間隔を取得
		$config_path = PKGCOREPATH.'modules/revision/config/revision.php';
		$config_path_default = PKGPROJPATH.'modules/revision/config/revision.php';
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