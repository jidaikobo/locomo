<?php
namespace Locomo;
class Presenter_Scdl_Edit extends \Presenter_Base
{
	/**
	 * form()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

//		$usergroups = \Model_Usrgrp::find_options('name', array('where' => array(array('is_available', true),array('is_for_acl', false))));
		$usergroups = \Model_Usrgrp_Custom::find_options();

		$form->field('group_detail')->set_options($usergroups);

		$model = \Request::main()->controller == 'Controller_Scdl' ? '\Model_Scdl' : '\Reserve\Model_Reserve' ;
		$form->field('kind_flg')->set_value($model::$_kind_flg);

		// 作成者
		// テンポラル対応
		if (isset(\Model_Usr::properties()['pronunciation']))
		{
			$users = Model_Usr::find_options('display_name', array('order_by' => 'pronunciation'));
		} else {
			$users = Model_Usr::find_options('display_name');
		}
		// 管理者を足す
		if (\Auth::is_admin())
		{
			$users = array('-1' => '管理者') + $users;
		}
		if (\Auth::is_root())
		{
			$users = array('-2' => 'root管理者') + $users;
		}
		$form->field('user_id')->set_options($users);

		//$form->field('user_id')->set_value(\Auth::get('id'));
		$form->field('is_visible')->set_value(1);

		// 初期値
		if (\Request::main()->action == 'create') {
			// 自分を選択する
			$form->field('user_id')->set_value(\Auth::get('id'));
			// 重要度
//			$form->field('title_importance_kb')->set_value("→中");
			if (\Input::get("ymd", "") == "") {
				$form->field('start_date')->set_value(date('Y-m-d'));
				$form->field('end_date')->set_value(date('Y-m-d'));
			}
			$form->field('start_time')->set_value('09:00');
			$form->field('end_time')->set_value('21:00');
		}

		if (\Input::get("ymd")) {
			$form->field('start_date')->set_value(\Input::get("ymd"));
			$form->field('end_date')->set_value(\Input::get("ymd"));
		}
		if (\Input::post()) {
			// 日付の自動判断
			if (\Input::post("end_date", "") == "" || \Input::post("end_date", "") == "0000-00-00") {
				if (\Input::post("repeat_kb") == 0) {
					$_POST['end_date'] = \Input::post("start_date");
				} else {
					$_POST['end_date'] = "2100-01-01";
				}
			}
		}

		return $form;
	}


}
