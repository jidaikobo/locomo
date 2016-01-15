<?php
namespace Locomo;
class Model_Revision extends \Model_Base
{
	protected static $_table_name = 'lcm_revisions';

	protected static $_properties = array(
		'id',
		'model',
		'pk_id',
		'data',
		'comment',
		'operation',
		'created_at',
		'deleted_at',
		'user_id' => array('default' => 0),
		'then_displayname',
	);

	protected static $_belongs_to = array(
		'user' => array(
			'key_from' => 'user_id',
			'model_to' => '\Model_Usr',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * insert_revision()
	*/
	public function insert_revision()
	{
		//当該コンテンツの最新データを取得
		$q = \DB::select('created_at','operation');
		$q->from('lcm_revisions');
		$q->where('model', $this->model);
		$q->where('pk_id', $this->pk_id);
		$q->order_by('created_at', 'DESC');
		$result = $q->execute()->current();
		$created_at = $result['created_at'];
		$operation = $result['operation'];

		//configからrevision間隔を取得
		$config_path = APPPATH.'config/revision.php';
		$config_path_default = LOCOMOPATH.'config/revision.php';
		$config = file_exists($config_path) ? \Config::load($config_path) : \Config::load($config_path_default);

		//operationが異なる場合は、絶対に保存する
		$force_save = $this->operation != $operation ? true : false;

		//最新データと規定時間との比較 - $created_at がゼロのときは初めて
		//コメントがあるときにも保存する
		if (
			! $force_save &&
			$created_at && strtotime($created_at) >= time() - intval($config['revision_interval']) &&
			empty($this->comment)
		):
			return;
		endif;

	//保存
		$this->save();

		return;
	}

	/**
	 * search_form()
	*/
	public static function search_form()
	{
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('user', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// wrap
		$parent = \Presenter_Base::search_form('編集履歴一覧');
		$parent->add_after($form, 'customer', array(), array(), 'opener');

		return $parent;
	}
}
