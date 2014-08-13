<?php
namespace Kontiki;
abstract class Controller_Options_Abstract extends \Kontiki\Controller
{
	/**
	 * action_generate()
	 */
	public function action_generate($id = null)
	{
/*
■オプションの作り方
●前提
原則、中間テーブルを用いることで、レコードにalter tableをかけずにテーブルを増やせるようにする。
オプションテーブルは、コントローラ名をprefixにする。（post_magazinesendways）
中間テーブルは_rのsuffixを持つ（post_magazinesendways_r）が自動生成される
アクションセットも必要
モジュールのoptionsディレクトリにmigrationファイルとアクションセットを持っておく

migrateを作る
migrateの例
\DBUtil::create_table('post_magazinesendways', array(
	'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
	'name'         => array('constraint' => 50, 'type' => 'varchar'),
	'order'        => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
	'is_available' => array('constraint' => 1, 'type' => 'tinyint'),
), array('id'));

\DBUtil::create_table('post_magazinesendways_r', array(
	'item_id'   => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
	'option_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
), array('item_id','option_id'));


*/







		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		if ( ! $data['item'] = $model::find_item($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], self::$nicename, $id)
			);
			\Response::redirect($this->request->module);
		endif;

		//view
		$view = \View::forge('view');
		$view->set_global('item', $data['item']);
		$view->set_global('title', sprintf($this->titles['view'], self::$nicename));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}
}
