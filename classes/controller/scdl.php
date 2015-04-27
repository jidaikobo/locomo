<?php
namespace Locomo;
class Controller_Scdl extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Revision;
	//use \Controller_Traits_Crud;

	// locomo
	public static $locomo = array(
		'nicename'     => 'スケジューラ', // for human's name
		'explanation'  => 'ユーザ毎のスケジュール管理をします。',
		'main_action'  => 'calendar', // main action
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'is_for_admin' => false, // true: hide from admin bar
		'order'        => 900,   // order of appearance
		'widgets' =>array(
			array('name' => 'カレンダ', 'uri' => '\\Controller_Scdl/calendar'),
			array('name' => '今日のスケジュール', 'uri' => '\\Controller_Scdl/dashboard_today'),
		),
	);

	private $_scdl_errors = array();	// エラー項目

	private $_someedit_id = 0;	// someeditで利用します
	private $_someedit_date = "";	// someeditで利用します。

	/**
	 * [action_create description]
	 * @return [type]
	 */
	public function action_create() {
		$this->action_edit();
	}

	/**
	 * [action_delete description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function action_delete($id) {

		$model = $this->model_name ;
		if ($obj = $model::find($id))
		{
			// event
			if (\Event::instance()->has_events('locomo_delete'))
			{
				\Event::instance()->trigger('locomo_delete', $obj, 'none');
			}

			// try to delete
			try {
				$obj->delete(null, true);
			}
			catch (\Exception $e) {
				\Session::set_flash('error', '項目の削除中にエラーが発生しました。');
			}

			\Session::set_flash(
				'success',
				sprintf('%1$sの #%2$d を削除しました', self::$nicename, $id)
			);

			\Response::redirect(\Uri::create($model::$_kind_name . '/calendar'));
		}

		\Session::set_flash('error', '項目の削除中にエラーが発生しました。');
		\Response::redirect(\Uri::create($model::$_kind_name . '/calendar'));
	}

	/**
	 * [action_delete_others description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 * just for acl.
	 */
	public function action_delete_others($id) {
		return $this->action_delete($id);
	}

	/**
	 * [action_edit description]
	 * @param  [type] $id
	 * @return [type]
	 */
	public function action_edit($id = null, $year = null, $mon = null, $day = null)
	{
		$model = $this->model_name ;

		// --------------------- parent ---------------------
		$content = \View::forge($model::$_kind_name . "/edit");

		if ($id) {
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));

			if ( ! $obj)
			{
				$page = \Request::forge('sys/403')->execute();
				return new \Response($page, 403);
			}
			$title = '#' . $id . ' ' . self::$nicename . '編集';
		} else {
			$obj = $model::forge();
			$title = self::$nicename . '新規作成';
		}
		$form = $model::form_definition('edit', $obj);

		$overlap_result = array();

		/*
		 * save
		 */
		if (\Input::post()) :
			// 重複チェックをここでセット
			$syear = date('Y', strtotime($this->_someedit_date ? $this->_someedit_date : \Input::post("start_date")));
			$smon  = date('m', strtotime($this->_someedit_date ? $this->_someedit_date : \Input::post("start_date")));
			$sday  = date('d', strtotime($this->_someedit_date ? $this->_someedit_date : \Input::post("start_date")));
			$eyear = date('Y', strtotime($this->_someedit_date ? $this->_someedit_date : \Input::post("end_date")));
			$emon  = date('m', strtotime($this->_someedit_date ? $this->_someedit_date : \Input::post("end_date")));
			$eday  = date('d', strtotime($this->_someedit_date ? $this->_someedit_date : \Input::post("end_date")));
			$shour = $smin = $ehour = $emin = 0;

			if (preg_match("/:/", \Input::post("start_time"))) {
				$shour = explode(":", \Input::post("start_time"))[0];
				$smin  = explode(":", \Input::post("start_time"))[1];
			}
			if (preg_match("/:/", \Input::post("end_time"))) {
				$ehour = explode(":", \Input::post("end_time"))[0];
				$emin  = explode(":", \Input::post("end_time"))[1];
			}
			$overlap_result = array();
			if (\Input::post("overlap_kb")) {
				$overlap_result = $this->checkOverlap(
								($this->_someedit_id ? $this->_someedit_id : $id)
								, $syear
								, $smon
								, $sday
								, $shour
								, $smin
								, $eyear
								, $emon
								, $eday
								, $ehour
								, $emin
								, \Input::post("repeat_kb")
								, \Input::post("week_kb")
								, \Input::post("target_day")
								, \Input::post("target_month")
								, \Input::post("week_index")
								, \Input::post("week_kb_option1")
								, \Input::post("week_index_option1")
								, \Input::post("week_kb_option2")
								, \Input::post("week_index_option2")
								);
			}

			if (
				$obj->cascade_set(\Input::post(), $form, $repopulate = true) &&
				 \Security::check_token() &&
				 $this->check_error_scdl() &&
				 !(\Input::post("overlap_kb") && count($overlap_result))
			):

				// オブザーバーの処理をここへ移動(仮から本登録の処理でも発動してしまうため)
				// checkbox値
				$columns = array('provisional_kb', 'unspecified_kb', 'allday_kb', 'private_kb', 'overlap_kb', 'attend_flg', 'group_kb');
				foreach ($columns as $v) {
					if (!\Input::post($v)) {
						$obj->__set($v, 0);
					}
				}

				//save
				if ($obj->save(null, true)):

					//success
					\Session::set_flash(
						'success',
						sprintf('%1$sの #%2$d を更新しました', self::$nicename, $obj->id)
					);

					// 部分編集の場合はリダイレクトしない
					if ($this->_someedit_id):
						return $obj;
					else:
						return \Response::redirect(\Uri::create(\Inflector::ctrl_to_dir(get_called_class()).'/edit/'.$obj->id));
					endif;
				else:

					//save failed
					\Session::set_flash(
						'error',
						sprintf('%1$sの #%2$d を更新できませんでした', self::$nicename, $id)
					);
				endif;
			else:
				//edit view or validation failed of CSRF suspected
				if (\Input::method() == 'POST'):

					$errors = $form->error();
					$errors = array_merge($errors, $this->_scdl_errors);
					if (count($overlap_result)) {
						// 重複チェック
						$errors[] = "同じ日時で重複しているデータが存在します。";
					}
					if ( ! \Security::check_token()) $errors[] = 'ワンタイムトークンが失効しています。送信し直してみてください。';// いつか、エラー番号を与えて詳細を説明する。そのときに二重送信でもこのエラーが出ることを忘れず言う。

					\Session::set_flash('error', $errors);
				endif;
			endif;
		endif;

		//add_actionset - back to index at edit
		$action['urls'][] = \Html::anchor(static::$main_url,'一覧へ');
		\Actionset::add_actionset(static::$controller, 'ctrl', $action);

		//view
		$this->template->set_global('title', $title);
		$content->set_global('item', $obj, false);
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		static::set_object($obj);

		// --------------------- end parent ---------------------

		if (\Input::get("ymd")) {
			if (\Session::get($model::$_kind_name . "narrow_uid") > 0 && $model::$_kind_name == "scdl") {
				$this->template->content->item->user = \Model_Usr::find("all", array("where" => array(array('id', \Session::get($model::$_kind_name . "narrow_uid")))));
			}
			if (\Session::get($model::$_kind_name . "narrow_bid") > 0 && $model::$_kind_name == "reserve") {
				$this->template->content->item->building = \Model_Scdl_Item::find("all", array("where" => array(array('item_group', 'building'), array('item_id', \Session::get($model::$_kind_name . "narrow_bid")))));
			}
		} else if (\Input::get("from")) {
			$from_data = $model::find(\Input::get("from"));
			if ($from_data) {
				$this->template->content->item->user = $from_data->user;
				$this->template->content->item->building = $from_data->building;
			}
		}
		if (\Input::post()) {
			$select_user_list = \Model_Usr::find("all", array("where" => array(array('id', 'in', explode("/", \Input::post("hidden_members"))))));
			$select_building_list = \Model_Scdl_Item::find("all", array("where" => array(array('item_group', 'building'), array('item_id', 'in', explode("/", \Input::post("hidden_buildings"))))));
		} else {
			$select_user_list = $this->template->content->item->user;
			$select_building_list = $this->template->content->item->building;
		}

		// $select_user_listがあれば編集と見なす
		if ($model::$_kind_name=='scdl' && empty($select_user_list)){
			$default_uid = \Input::get('member_id');
			if ($default_uid){//$default_uidがあれば初期値として追加
				if(\Model_Usr::find(intval($default_uid))){
					$select_user_list[$default_uid] = \Model_Usr::find($default_uid);
				}
			}
			
			// $default_uidが$cuurent_uidと異なる場合初期値として足す（通常のユーザか判定）
			$cuurent_uid = \Auth::get('id');
			
			if ($cuurent_uid >= 1 && $default_uid != $cuurent_uid){
				{
					// $select_user_listの配列に追加
					$select_user_list[$cuurent_uid] = \Model_Usr::find($cuurent_uid);
				}
			}
		}

		


		
		$this->template->content->set("select_user_list", $select_user_list);
		if (!$id && \Session::get($model::$_kind_name . "narrow_ugid") && $model::$_kind_name=="scdl" && count($select_user_list) == 0) {
			$non_selected_user_list = \Model_Usr::find('all',
			array(
				'related'   => array('usergroup'),
				'where'=> array(array('usergroup.id', '=', \Session::get($model::$_kind_name . "narrow_ugid")))
				)
			);
		} else if (count($select_user_list)) {
			$non_selected_user_list = \Model_Usr::find('all',
			array(
				'where'=> array(array('id', 'not in', array_keys($select_user_list)))
				)
			);
		} else {
			$non_selected_user_list = \Model_Usr::find('all');
		}
		
		$this->template->content->set("non_selected_user_list", $non_selected_user_list);


		// $select_building_listがない場合、$default_bidがあれば初期値として足す
		$default_bid = \Input::get('building_id');
		if (empty($select_building_list) && $default_bid)
		{
			// $select_building_listの配列に追加
			if(\Model_Scdl_Item::find(intval($default_bid))):
				$select_building_list[$default_bid] = \Model_Scdl_Item::find($default_bid);
			endif;
		}
		
		if (!$id && \Session::get($model::$_kind_name . "narrow_bgid") && $model::$_kind_name=="reserve" && count($select_building_list) == 0) {
			$non_selected_building_list = \Model_Scdl_Item::find('all',
			array(
				'where'=> array(array('item_group2', '=', \Session::get($model::$_kind_name . "narrow_bgid")))
				)
			);
		} else if (count($select_building_list)) {
			$non_selected_building_list = \Model_Scdl_Item::find('all',
			array(
				'where'=> array(array(array('item_group', 'building'), array('id', 'not in', array_keys($select_building_list))))
				)
			);
		} else {
			$non_selected_building_list = \Model_Scdl_Item::find('all',
				array('where' => array(array('item_group', 'building')))
			);
		}

		
		
		$this->template->content->set("building_group_list", \DB::select(\DB::expr("DISTINCT item_group2"))->from("lcm_scdls_items")->where("item_group", "building")->execute()->as_array());
		$this->template->content->set("select_building_list", $select_building_list);
		$this->template->content->set("non_select_building_list", $non_selected_building_list);
		$usergroups = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true), array('is_for_acl', false))), 'name');
		$this->template->content->set("group_list", $usergroups);
		$this->template->content->set("kind_name", $model::$_kind_name);

		// 重複チェック
		$this->template->content->set("overlap_result", $overlap_result);

	}

	public function action_copy() {
		$model = $this->model_name ;

		$this->action_edit();

		if (\Input::get("from")) {
			// 直接メンバ変数にアクセスしてよいか
			$from_data = $model::find(\Input::get("from"));
			if ($from_data) {
				$setcolumns = array('start_date', 'start_time', 'end_date', 'end_time', 'title_text', 'title_importance_kb'
									, 'title_kb', 'provisional_kb', 'private_kb', 'allday_kb', 'unspecified_kb', 'overlap_kb'
									, 'message', 'group_kb', 'group_detail', 'purpose_kb'
									, 'purpose_text', 'user_num', 'repeat_kb', "week_kb", "target_day", "target_month", "week_index"
									, 'week_kb_option1', 'week_kb_option2', 'week_index_option1', 'week_index_option2');
				foreach ($setcolumns as $v) {
					$this->template->content->form->field($v)->set_value($from_data->$v);
				}
			}
		}
	}

	/**
	 * [action_someedit description]
	 * @param  [type] $id   [description]
	 * @param  [type] $year [description]
	 * @param  [type] $mon  [description]
	 * @param  [type] $day  [description]
	 * @return [type]       [description]
	 */
	public function action_someedit($id, $year, $mon, $day) {
		$this->_someedit_id = $id;
		$this->_someedit_date = $year . "/" . $mon . "/" . $day;
		$model = $this->model_name;


		if (\Input::post()) {

			\DB::start_transaction();

			// 新規登録処理
			$obj_edit = $this->action_edit();

			// 変更不可項目を上書き
			if ($obj_edit) {
				$obj_edit->repeat_kb	 = 0;	// 繰り返しなしとする
				$obj_edit->target_month	 = 0;
				$obj_edit->target_day	 = 0;
				$obj_edit->start_date	 = $year . "-" . $mon . "-" . $day;
				$obj_edit->end_date		 = $year . "-" . $mon . "-" . $day;
				$obj_edit->delete_day	 = "";
				$obj_edit->week_kb		 = 0;
				$obj_edit->week_index 	 = 0;
				$obj_edit->week_kb_option1 = 0;
				$obj_edit->week_kb_option2 = 0;
				$obj_edit->week_index_option1 = 0;
				$obj_edit->week_index_option2 = 0;
				$obj_edit->parent_id	 = $id;
				$obj_edit->save();

				// 部分削除の処理をいれる
				$obj = $model::find($id);
				
				// 部分削除処理
				$obj->delete_day = $obj->delete_day . sprintf("[%04d/%02d/%02d]", $year, $mon, $day);

				if ($obj->save())
				{
					\DB::commit_transaction();
					\Session::set_flash('success', "部分編集をおこないました。");
				} else {
					\Session::set_flash('error', "部分編集に失敗しました。");
				}

				// カレンダー表示
		//		$this->action_calendar();
				\Response::redirect(static::$main_url);
			}

		} else {
			$this->action_edit($id);
		}

		$this->template->content->set("is_someedit", 1);
	}

	/**
	 * [action_detail]
	 * 詳細を表示
	 * 
	 * @param  [type] $id
	 * @return [type]
	 */
	public function action_viewdetail($id, $year = null, $mon = null, $day = null) {
		// 日付を覚えておく
		if ($year)
			\Session::set("calendar_year", $year);
		if ($mon)
			\Session::set("calendar_mon", $mon);
		if ($day)
			\Session::set("calendar_day", $day);
		if (!\Session::get("calendar_year"))
			\Session::set("calendar_year", date('Y'));
		if (!\Session::get("calendar_mon"))
			\Session::set("calendar_mon", date('m'));
		if (!\Session::get("calendar_day"))
			\Session::set("calendar_day", date('d'));

		// actionsetを正常に動かすために実行
		parent::view($id);

		$model = $this->model_name;
		$detail = $model::find($id);

		// 見つからなければカレンダーへ
		if ($detail == null) {
			return $this->action_calendar();
		}

		$uid = \Auth::get('id');


		$schedule_members = \Locomo\Model_Scdl_Member::find('all', array(
					    'where' => array(
					        array('schedule_id', $id),
					        array('user_id', $uid)
					    ),
					));

		$attend_members = \Locomo\Model_Scdl_Attend::find('all', array(
					    'where' => array(
					        array('schedule_id', $id)
					    ),
					));

		$usergroups = \Auth::get_groups();
		
		// グループ判定のため$detail->private_kbが上書きされるので、個票でそもそもこれが非公開項目だったのかどうかを判定するために追加。
		$detail->private_kb_check = $detail->private_kb;
		
		if ($detail->group_kb == 2) {
			$allow = false;
			foreach ($usergroups as $gid) {
				if ($detail->group_detail == $gid) {
					$allow = true;
				}
			}
			$detail->private_kb = $allow ? 0 : 1;
		}
		$detail->target_year = $year;
		$detail->target_mon = $mon;
		$detail->target_day = $day;

		$view = \View::forge($model::$_kind_name . "/view");
		$view->set_global('title', self::$nicename);
		$view->set("year", $year);
		$view->set("mon", $mon);
		$view->set("day", $day);
		$view->set("model_name", $model);
		$view->set("detail", $detail);
		$view->set("schedule_attend_members", $attend_members);
		$view->set("schedule_members_me", $schedule_members);
		$this->template->content = $view;
	}

	/**
	 * 出席確認
	 * 
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function action_attend($id, $year = null, $mon = null, $day = null) {
		if (!$year)
			$year = \Session::get("calendar_year");
		if (!$mon)
			$mon = \Session::get("calendar_mon");
		if (!$day)
			$day = \Session::get("calendar_day");

		// 日付
		if ($year)
			\Session::set("calendar_year", $year);
		if ($mon)
			\Session::set("calendar_mon", $mon);
		if ($day)
			\Session::set("calendar_day", $day);
		if (!\Session::get("calendar_year"))
			\Session::set("calendar_year", date('Y'));
		if (!\Session::get("calendar_mon"))
			\Session::set("calendar_mon", date('m'));
		if (!\Session::get("calendar_day"))
			\Session::set("calendar_day", date('d'));

		$model = $this->model_name;
		$detail = $model::find($id);

		// actionsetを正常に動かすために
		parent::edit($id);

		$uid = \Auth::get('id');

		$schedule_members = \Locomo\Model_Scdl_Member::find('all', array(
					    'where' => array(
					        array('schedule_id', $id),
					        array('user_id', $uid)
					    ),
					));
		
		// 更新
		if (\Input::post()) {
			$obj = Model_Scdl_Attend::find('all', array(
				    'where' => array(
				        array('user_id', $uid),
				        array('schedule_id', $id)
				    ),
				));
			if (!$obj) {
				$obj = Model_Scdl_Attend::forge();
			} else {
				$obj = array_shift($obj);
			}
			$obj->user_id = $uid;
			$obj->schedule_id = $id;
			$obj->attend_kb = \Input::post("attend_kb");
			$obj->save();
			// リダイレクト
			\Response::redirect($model::$_kind_name . "/calendar");
		}

		$view = \View::forge($model::$_kind_name . "/attend");
		$view->set_global('title', self::$nicename);
		$view->set("detail", $detail);
		$view->set("schedule_members_me", $schedule_members);
		$view->set("year", $year);
		$view->set("mon", $mon);
		$view->set("day", $day);
		$this->template->content = $view;

		$this->template->content->set("items", Model_Scdl_Item::get_items_array('attend_kb'));
		$myattend = Model_Scdl_Attend::find('all', array(
					    'where' => array(
					        array('schedule_id', $id),
					        array('user_id', $uid)
					    ),
					));

		$this->template->content->set("attend", count($myattend) ? array_shift($myattend) : array('attend_kb' => ''));
	}

	/**
	 * 仮登録から本登録へ
	 * 
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function action_regchange($id, $year = null, $mon = null, $day = null) {
		if (!$year)
			$year = \Session::get("calendar_year");
		if (!$mon)
			$mon = \Session::get("calendar_mon");
		if (!$day)
			$day = \Session::get("calendar_day");
		
		// 日付
		if ($year)
			\Session::set("calendar_year", $year);
		if ($mon)
			\Session::set("calendar_mon", $mon);
		if ($day)
			\Session::set("calendar_day", $day);
		if (!\Session::get("calendar_year"))
			\Session::set("calendar_year", date('Y'));
		if (!\Session::get("calendar_mon"))
			\Session::set("calendar_mon", date('m'));
		if (!\Session::get("calendar_day"))
			\Session::set("calendar_day", date('d'));

		$model = $this->model_name;
		$obj = $model::find($id);
		// 仮予定フラグを抜く
		$obj->provisional_kb = 0;
		$obj->save();

		$this->action_viewdetail($id, $year, $mon, $day);
	}

	/**
	 * 部分削除
	 * 
	 * @param  [type] $id   [description]
	 * @param  [type] $year [description]
	 * @param  [type] $mon  [description]
	 * @param  [type] $day  [description]
	 * @return [type]       [description]
	 */
	public function action_somedelete($id = null, $year = null, $mon = null, $day = null)
	{
		// 一つでも欠けていたらエラー
		if (is_null($id) || is_null($year) || is_null($mon) || is_null($day))
		{
			\Session::set_flash('error', "部分削除に失敗しました。");
			\Response::redirect(static::$main_url);
		}

		$model = $this->model_name;
		$obj = $model::find($id);
		$obj->delete_day = $obj->delete_day . sprintf("[%04d/%02d/%02d]", $year, $mon, $day);
		if ($obj->save())
		{
			\Session::set_flash('success', "部分削除しました。");
		} else {
			\Session::set_flash('error', "部分削除に失敗しました。");
		}

		// カレンダー表示
//		$this->action_calendar();
		\Response::redirect(static::$main_url);
	}

	/**
	 * [action_calendar]
	 * カレンダーを表示
	 * 一ヶ月表示：$yearと$mon
	 * 週ごと：$yearと$monと$day、$mode='week'
	 * 一日表示：$year,$mon,$day
	 * 
	 * @param  [type] $year
	 * @param  [type] $mon
	 * @param  [type] $day
	 * @param  [type] $mode
	 * @return [type]
	 */
	public function action_calendar($year = null, $mon = null, $day = null, $mode = null, $week_option = null) {
		$model = $this->model_name;

		// 絞り込みをセッションへ保存
		if (\Input::get("ugid", "not") != "not") {
			\Session::set($model::$_kind_name . "narrow_ugid", \Input::get("ugid"));
			\Session::set($model::$_kind_name . "narrow_uid", "");

			\Session::set($model::$_kind_name . "narrow_bgid", "");
			\Session::set($model::$_kind_name . "narrow_bid", "");

		}
		if (\Input::get("uid", "not") != "not")
			\Session::set($model::$_kind_name . "narrow_uid", \Input::get("uid"));

		if (\Input::get("bgid", "not") != "not") {
			\Session::set($model::$_kind_name . "narrow_bgid", \Input::get("bgid"));
			\Session::set($model::$_kind_name . "narrow_bid", "");

			\Session::set($model::$_kind_name . "narrow_ugid", "");
			\Session::set($model::$_kind_name . "narrow_uid", "");
		}
		if (\Input::get("bid", "not") != "not")
			\Session::set($model::$_kind_name . "narrow_bid", \Input::get("bid"));

		if (\Input::get("scdl_display_time", "") != "") {
			\Session::set("scdl_display_time", \Input::get("scdl_display_time"));
		}

		// 初期表示
		if ($year == null || $year == "") {
			// 指定がない場合
			// 自分の代表グループIDを取得
			$mydata = \Model_Usr::find(\Auth::get('id'));
			if (\Input::get("ugid", "not") == "not" && $mydata && $mydata->main_usergroup_id) {
				\Session::set($model::$_kind_name . "narrow_ugid", $mydata->main_usergroup_id);
				\Session::set($model::$_kind_name . "narrow_uid", "");

				\Session::set($model::$_kind_name . "narrow_bgid", "");
				\Session::set($model::$_kind_name . "narrow_bid", "");
			}
		}
		if ($year == null || $year == "" || $year < 1000)
			$year = date('Y');
		if ($mon == null || $mon == "" || $mon < 0 || $mon > 12)
			$mon = date('m');

		// テンプレート切り分け
		$tmpl_sub = "";
		if ($mode == "week")
			$tmpl_sub = "_week";
		if ($mode == null && $day)
			$tmpl_sub = "_day";
		// 週表示用にテンプレートを分ける
		if ($week_option == "member") {
			$tmpl_sub = "_week_" . $week_option;
		} else if ($week_option == "building") {
			$tmpl_sub = "_week_" . $week_option;
		}

		$year = (int)$year;
		$mon = (int)$mon;
		$day = (int)$day;



		// 各モードにより処理分け
		$calendar = array();
		$next_url = "";
		$prev_url = "";
		$mini_next_url = date('Y/m/', strtotime(sprintf("%04d/%02d/15", $year, $mon) . " + 1month"));
		$mini_prev_url = date('Y/m/', strtotime(sprintf("%04d/%02d/15", $year, $mon) . " - 1month"));
		$next_year = date('Y/m/', strtotime(sprintf("%04d/%02d/15", $year, $mon) . " + 1year"));
		$prev_year = date('Y/m/', strtotime(sprintf("%04d/%02d/15", $year, $mon) . " - 1year"));
		$mini_next_url = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $mini_next_url), '次の月',  array('class' => 'next_month'));
		$mini_prev_url = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $mini_prev_url), '前の月',  array('class' => 'prev_month'));

		$next_year_url = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $next_year), '次の年',  array('class' => 'next_year'));
		$prev_year_url = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $prev_year), '前の年',  array('class' => 'prev_year'));

		if ($mode == "week") {
			// 週表示
			$calendar = $this->make_week_calendar($year, $mon, $day);
			// 次の週
			$next_url = date('Y/m/d', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $day) . " + 7days")) . "/week";
			$prev_url = date('Y/m/d', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $day) . " - 7days")) . "/week";
			if ($week_option) {
				$next_url .= "/" . $week_option;
				$prev_url .= "/" . $week_option;
			}
			$next_url = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $next_url), '次の週',  array('class' => 'next_week'));
			$prev_url = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $prev_url), '前の週',  array('class' => 'prev_week'));
		} else if ($day && $mode == null) {
			// 1日表示
			$calendar = $this->make_day_calendar($year , $mon, $day);
			$next_url = date('Y/m/d', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $day) . " + 1days"));
			$prev_url = date('Y/m/d', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $day) . " - 1days"));
			$next_url = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $next_url), '次の日');
			$prev_url = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $prev_url), '前の日');
		} else {
			// １ヶ月表示
			$calendar = $this->make_month_calendar($year, $mon);
			$next_url = $mini_next_url;
			$prev_url = $mini_prev_url;
		}

		// 週表示用
		list($weekY, $weekM, $weekD) = $this->week_first_date($year, $mon, $day);
		$view = \View::forge($model::$_kind_name . "/calendar" . $tmpl_sub);

		$view->set_global('title', self::$nicename);
		$view->set_global("detail_pop_data", array());

		// ユーザーグループ一覧作成
		$narrow_user_group_list = array();
		// カスタムグループを先に作成
//		$narrow_user_group_list = \Model_Usrgrp_Custom::get_options(array('where' => array(array('is_available', true), array('is_for_acl', false))), 'name');
//		// 一番上に見せるようにする
		$narrow_user_group_list_custom = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true), array('is_for_acl', false), array('customgroup_uid', 'is not', null))), 'name');
		$narrow_user_group_list_normal = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true), array('is_for_acl', false), array('customgroup_uid', null))), 'name');
		foreach ($narrow_user_group_list_normal as $row) {
			// array_mergeだとIDがおかしくなるためひとつずついれる
			$narrow_user_group_list_custom[] = $row;
		}
		$view->set('narrow_user_group_list', $narrow_user_group_list_custom);

		$where = \Session::get($model::$_kind_name . "narrow_ugid") > 0 ? array(array('usergroup.id', '=', \Session::get($model::$_kind_name . "narrow_ugid"))) : array();

		$view->set('narrow_user_list', \Model_Usr::find('all',
			array(
				'related'   => count($where) ? array('usergroup') : array(),
				'where'=> $where,
				'order_by' => 'pronunciation'
				)
			));


		// 施設一覧作成
		$view->set('narrow_building_group_list', \DB::select(\DB::expr("DISTINCT item_group2"))->from("lcm_scdls_items")->where("item_group", "building")->execute()->as_array());
		
		if (\Session::get($model::$_kind_name . "narrow_bgid") > 0) {
			$where = array(
				array('item_group2', '=', \Session::get($model::$_kind_name . "narrow_bgid"))
				,array('item_group' , '=', 'building')
				);
		} else {
			$where = array(array('item_group' , '=', 'building'));
		}
		$view->set('narrow_building_list', \Model_Scdl_Item::find('all',
			array(
				'where'=> $where
				)
			));

		$view->set("model_name", $model);
		$view->set("mode", $mode);
		$view->set('year', $year);
		$view->set('mon', $mon);
		$view->set("day", $day);
		$view->set("schedule_data", $calendar);
		$view->set("week_name", array('日', '月', '火', '水', '木', '金', '土'));
		$view->set("mini_calendar", $this->make_mini_calendar($year, $mon));
		$view->set("next_url", $next_url);
		$view->set("prev_url", $prev_url);
		$view->set("mini_next_url", $mini_next_url);
		$view->set("mini_prev_url", $mini_prev_url);
		$view->set("next_year_url", $next_year_url);
		$view->set("prev_year_url", $prev_year_url);
		$view->set("kind_name", $model::$_kind_name);
		$view->set("display_month", \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $year . '/' . $mon), '月表示'));
		$view->set("display_week", \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $weekY . '/' . $weekM . '/' . $weekD . '/week'), '週表示'));
		$this->template->content = $view;
	}

	/**
	 * 日の詳細予定を表示
	 *
	 * 
	 * @param  [type] $year
	 * @param  [type] $mon
	 * @param  [type] $day
	 * @return [type]
	 */
	private function make_day_calendar($year, $mon, $day) {
		$model = $this->model_name;

		$target_start = sprintf("%04d-%02d-%02d", $year, $mon, $day);
		$target_end = sprintf("%04d-%02d-%02d", $year, $mon, $day);
		
		// 他のクラスから利用される事を前提でメンバ変数を使わないように
		$schedules = array();

		$schedule_data = \Locomo\Model_Scdl::query()
							->where_open()
							->or_where_open()
								->where("start_date", "<=", $target_start)
								->where("end_date", ">=", $target_end)
							->or_where_close()
							->or_where_open()
								->where("start_date", "<=", $target_start)
								->where("end_date", ">=", $target_start)
							->or_where_close()
							->or_where_open()
								->where("start_date", "<=", $target_end)
								->where("end_date", ">=", $target_end)
							->or_where_close()
							->or_where_open()
								->where("start_date", ">=", $target_start)
								->where("end_date", "<=", $target_end)
							->or_where_close()
							->where_close()
							->where("deleted_at", "is", null)
							->where("kind_flg", $model::$_kind_flg)
							->order_by("start_time")
							->get();
						
		$user_exist = array();
		$building_exist = array();
		$unique_schedule_data = array();

		$unique_index = array();
		for ($i = 0; $i <= 23; $i++) {
			
			$row = array();
			$row['hour'] = $i;
			$row['data'] = array();
			foreach ($schedule_data as $r) {
				if ($this->is_target_day($year, $mon, $day, $r)) {
					// 対象日を挿入
					$r['target_year'] = (int)$year;
					$r['target_mon'] = (int)$mon;
					$r['target_day'] = (int)$day;
					if (!isset($unique_index['schedule_' . $r['id']])) {
						$r['scdlid'] = $r['id'];
						// クローンする前にアクセスしないと取得しないため
						$r['building'] = $r['building'];
						$r['user'] = $r['user'];
						$unique_schedule_data[] = clone $r;
						$unique_index['schedule_' . $r['id']] = 1;
					}
					$starttime = date('Ymd', strtotime($r['start_date'])) . date('Hi', strtotime($r['start_time']));
					$endtime = date('Ymd', strtotime($r['end_date'])) . date('Hi', strtotime($r['end_time']));
					$target_1 = sprintf("%04d%02d%02d%02d", $year, $mon, $day, $row['hour']) . "00";
					$target_2 = sprintf("%04d%02d%02d%02d", $year, $mon, $day, $row['hour']) . "15";
					$target_3 = sprintf("%04d%02d%02d%02d", $year, $mon, $day, $row['hour']) . "30";
					$target_4 = sprintf("%04d%02d%02d%02d", $year, $mon, $day, $row['hour']) . "45";

					if ($r['repeat_kb'] == 1 || $r['repeat_kb'] == 2 || $r['repeat_kb'] == 3 || $r['repeat_kb'] == 4 || $r['repeat_kb'] == 5 || $r['repeat_kb'] == 6) {
						$starttime = date('Hi', strtotime($r['start_time']));
						$endtime = date('Hi', strtotime($r['end_time']));
						$target_1 = sprintf("%02d", $row['hour']) . "00";
						$target_2 = sprintf("%02d", $row['hour']) . "15";
						$target_3 = sprintf("%02d", $row['hour']) . "30";
						$target_4 = sprintf("%02d", $row['hour']) . "45";

						$r['primary'] = (!($target_1 < $starttime || $target_1 >= $endtime)) ? 1 : 0;
						$r['secondary'] = (!($target_2 < $starttime || $target_2 >= $endtime)) ? 1 : 0;
						$r['third'] = (!($target_3 < $starttime || $target_3 >= $endtime)) ? 1 : 0;
						$r['fourth'] = ($target_4 < $endtime && $starttime <= $target_4) ? 1 : 0;
						//$r['fourth'] = (!($target_4 < $starttime || $target_4 >= $endtime)) ? 1 : 0;
					} else {
						$starttime = date('Ymd', strtotime($r['start_date'])) . date('Hi', strtotime($r['start_time']));
						$endtime = date('Ymd', strtotime($r['end_date'])) . date('Hi', strtotime($r['end_time']));
						$target_1 = sprintf("%04d%02d%02d%02d", $year, $mon, $day, $row['hour']) . "00";
						$target_2 = sprintf("%04d%02d%02d%02d", $year, $mon, $day, $row['hour']) . "15";
						$target_3 = sprintf("%04d%02d%02d%02d", $year, $mon, $day, $row['hour']) . "30";
						$target_4 = sprintf("%04d%02d%02d%02d", $year, $mon, $day, $row['hour']) . "45";

						$r['primary'] = (!($target_1 < $starttime || $target_1 >= $endtime)) ? 1 : 0;
						$r['secondary'] = (!($target_2 < $starttime || $target_2 >= $endtime)) ? 1 : 0;
						$r['third'] = (!($target_3 < $starttime || $target_3 >= $endtime)) ? 1 : 0;
						$r['fourth'] = ($target_4 < $endtime && $starttime <= $target_4) ? 1 : 0;
						//$r['fourth'] = (!($target_4 < $starttime || $target_4 >= $endtime)) ? 1 : 0;

					}
					if (isset($r['primary']) || isset($r['secondary']) || isset($r['third']) || isset($r['fourth'])) {
						// 詳細へのリンク
						$r['link_detail'] = \Html::anchor(\Uri::create($model::$_kind_name . '/viewdetail/' . $r['id'] . sprintf("/%04d/%d/%d", $year, $mon, $day)), $r['title_text']);
						// 30分前かどうか
						$r['schedule_id'] = $r['id'];	// cloneすると消えるため
						$r['user'] = $r['user'];	// クローンすると消える（クエリが発行されない）
						$r['building'] = $r['building'];	// クローンすると消える（クエリが発行されない）
						// 追加
						$row['data'][] = clone $r;
//						$schedules[$r['id']] = clone $r;
					}
					// メンバー
					foreach ($r->user as $d) {
						if (!isset($user_exist[$d->id][$r->schedule_id])) {
							$user_exist[$d->id]['model'] = $d;
							$user_exist[$d->id][$r->schedule_id]['data'][] = $r;
						} else {
							$flg_push = true;
							foreach ($user_exist[$d->id][$r->schedule_id]['data'] as $row_data) {
								if ($row_data->id == $r->schedule_id) {
									$flg_push = false;
								}
							}
							if ($flg_push) {
								$user_exist[$d->id][$r->schedule_id]['data'][] = $r;
							}
						}
					}
					// 施設
					foreach ($r->building as $d) {
						if (!isset($building_exist[$d->item_id])) {
							$building_exist[$d->item_id]['model'] = $d;
							$building_exist[$d->item_id]['data'][] = $r;
						} else {
							$flg_push = true;
							foreach ($building_exist[$d->item_id]['data'] as $row_data) {
								if ($row_data->id == $r->schedule_id) {
									$flg_push = false;
								}
							}
							if ($flg_push) {
								$building_exist[$d->item_id]['data'][] = $r;
							}
						}
					}
				}
			}
			$schedules['schedules_list'][] = $row;
		}

		$schedules['member_list'] = $user_exist;
		$schedules['building_list'] = $building_exist;
		$schedules['unique_schedule_data'] = $unique_schedule_data;

		return $schedules;
	}

	/**
	 * [make_week_calendar 一週間ごとに表示]
	 * @param  [type] $year
	 * @param  [type] $mon
	 * @param  [type] $day
	 * @return [type]
	 */
	private function make_week_calendar($year, $mon, $day) {
		$model = $this->model_name;

		$target_start = sprintf("%04d-%02d-%02d", $year, $mon, $day);
		$target_end = date('Y-m-d', strtotime(sprintf("%04d-%02d-%02d 00:00:00", $year, $mon, $day) . " + 7days"));

		// 他のクラスから利用される事を前提でメンバ変数を使わないように
		$schedules = array();
		$schedules['schedules_list'] = array();

		$schedule_data = \Locomo\Model_Scdl::query()
							->where_open()
							->or_where_open()
								->where("start_date", "<=", $target_start)
								->where("end_date", ">=", $target_end)
							->or_where_close()
							->or_where_open()
								->where("start_date", "<=", $target_end)
								->where("end_date", ">=", $target_end)
							->or_where_close()
							->or_where_open()
								->where("start_date", "<=", $target_start)
								->where("end_date", ">=", $target_start)
							->or_where_close()
							->or_where_open()
								->where("start_date", ">=", $target_start)
								->where("end_date", "<=", $target_end)
							->or_where_close()
							->where_close()
							->where("deleted_at", "is", null)
							->where("kind_flg", $model::$_kind_flg)
							->order_by("start_time")
							->get();

		
		$user_exist = array();
		$building_exist = array();

		// 祝日を取得
		$holidays = \Model_Scdl_Item::query()
							->where("item_group", "holiday")
							->get();
		$holidays_hash = array();
		foreach ($holidays as $v) {
			$holidays_hash["d" . $v['item_name']] = $v['item_name'];
		}

		for ($i = 0; $i < 7; $i++) {
			$row = array();
			$row['year']	 = (int)date('Y', strtotime(sprintf("%04d-%02d-%02d 00:00:00", $year, $mon, $day) . " + " . $i . "days"));
			$row['mon']		 = (int)date('m', strtotime(sprintf("%04d-%02d-%02d 00:00:00", $year, $mon, $day) . " + " . $i . "days"));
			$row['day']		 = (int)date('d', strtotime(sprintf("%04d-%02d-%02d 00:00:00", $year, $mon, $day) . " + " . $i . "days"));
			$row['week']	 = date('w', strtotime(sprintf("%04d/%02d/%02d", $row['year'], $row['mon'], $row['day'])));
			$row['data']	 = array();
			$row['is_holiday'] = isset($holidays_hash["d" . sprintf("%04d/%02d/%02d", $row['year'], $row['mon'], $row['day'])]);

			foreach ($schedule_data as $r) {
				// 対象の日付のデータか判断
				if ($this->is_target_day($row['year'], $row['mon'], $row['day'], $r)) {

					// 詳細へのリンク
					$r['link_detail'] = \Html::anchor(\Uri::create($model::$_kind_name . '/viewdetail/' . $r['id'] . sprintf("/%04d/%d/%d", $year, $mon, $row['day'])), $r['title_text']);
					$r['target_year'] = $row['year'];
					$r['target_mon'] = $row['mon'];
					$r['target_day'] = $row['day'];
					$r['scdlid'] = $r['id'];	// クローンするとIDが消えるため
					$r['user'] = $r['user'];	// クローンすると消える（クエリが発行されない）
					$r['building'] = $r['building'];	// クローンすると消える（クエリが発行されない）
					// 追加
					$row['data'][] = clone $r;

					// メンバー
					foreach ($r->user as $d) {
						if (!isset($user_exist[$d->id][$r->id])) {
							$user_exist[$d->id]['model'] = $d;
							$user_exist[$d->id][$r->id]['data'][] = $r;
						} else {
							$flg_push = true;
							foreach ($user_exist[$d->id][$r->id]['data'] as $row_data) {
								if ($row_data->id == $r->id) {
									$flg_push = false;
								}
							}
							if ($flg_push) {
								$user_exist[$d->id][$r->id]['data'][] = $r;
							}
						}
					}
					// 施設
					foreach ($r->building as $d) {
						if (!isset($building_exist[$d->item_id])) {
							$building_exist[$d->item_id]['model'] = $d;
							$building_exist[$d->item_id]['data'][] = $r;
						} else {
							$flg_push = true;
							foreach ($building_exist[$d->item_id]['data'] as $row_data) {
								if ($row_data->id == $r->id) {
									$flg_push = false;
								}
							}
							if ($flg_push) {
								$building_exist[$d->item_id]['data'][] = $r;
							}
						}
					}
				}


			}
			$schedules['schedules_list'][] = $row;
		}

		$schedules['member_list'] = $user_exist;
		$schedules['building_list'] = $building_exist;
		return $schedules;
	}


	/**
	 * [make_month_calendar]
	 * 一ヶ月表示用
	 * 
	 * @param  [type] $year
	 * @param  [type] $mon
	 * @return [type]
	 */
	private function make_month_calendar($year, $mon) {
		$model = $this->model_name;

		// 他のクラスから利用される事を前提でメンバ変数を使わないように
		$schedules = array();
		// 月末
		$last_day = date('t', mktime(0, 0, 0, $mon, 1, $year));

		// 開始日時
		$target_start = sprintf("%04d-%02d-%02d", $year, $mon, 1);
		$target_end = sprintf("%04d-%02d-%02d", $year, $mon, $last_day);

		// モデル
		$model = $this->model_name;
		// 後でmodelを使うように
		//$obj = $model::find(1);
		$query = \Locomo\Model_Scdl::query()
							//->where_open()
							//	// 開始日時と終了日時が範囲内のもの
							//	->where(\DB::expr("DATE_FORMAT(start_date, '%Y%m')"), sprintf("%04d%02d", $year, $mon))
							//	->or_where(\DB::expr("DATE_FORMAT(end_date, '%Y%m')"), sprintf("%04d%02d", $year, $mon))
							//->where_close()
							->where_open()
							->or_where_open() // <|   |>
								->where("start_date", "<=", $target_start)
								->where("end_date", ">=", $target_end)
							->or_where_close()
							->or_where_open() // |   <|  >
								->where("start_date", "<=", $target_end)
								->where("end_date", ">=", $target_end)
							->or_where_close()
							->or_where_open() // <|  >    |
								->where("start_date", "<=", $target_start)
								->where("end_date", ">=", $target_start)
							->or_where_close()
							->or_where_open()// |  <>  |
								->where("start_date", ">=", $target_start)
								->where("end_date", "<=", $target_end)
							->or_where_close()
							->where_close()
							->where("deleted_at", "is", null)
							->where("kind_flg", $model::$_kind_flg)
							->order_by("start_time");
		$schedules_data = $query->get();
							

		// 月曜日からはじまるため、空白のデータを入れる
		$week = date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, 1))) == 0 ? 7 : date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, 1)));
		for ($i = 1; $i < $week; $i++) {
			$row = array();
			$row['week'] = ($i == 7) ? 0 : $i;
//			$row['week'] = ($i == 1) ? 1 : 3; // 月曜日という事にする
			$schedules[] = $row;
		}

		// 祝日を取得
		$holidays = \Model_Scdl_Item::query()
							->where("item_group", "holiday")
							->get();
		$holidays_hash = array();
		foreach ($holidays as $v) {
			$holidays_hash["d" . $v['item_name']] = $v['item_name'];
		}


		for ($i = 1; $i <= $last_day; $i++) {
			$row = array();
			// 日付
			$row['year']	 = (int)$year;
			$row['mon']		 = (int)$mon;
			$row['day']		 = $i;
			$row['week']	 = date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $i)));
			$row['data']	 = array();
			$row['is_holiday'] = isset($holidays_hash["d" . sprintf("%04d/%02d/%02d", $year, $mon, $i)]);

//			$row['link_day'] = \Html::anchor('schedules/calendar/' . $row['year'] . "/" . $row['mon'] . "/" . $row['day'], $row['day']);
			foreach ($schedules_data as $r) {
				// 対象の日付のデータか判断
				if ($this->is_target_day($year, $mon, $i, $r)) {
					// 詳細へのリンク
					$r['link_detail'] = \Html::anchor(\Uri::create($model::$_kind_name . '/viewdetail/' . $r['id'] . sprintf("/%04d/%d/%d", $year, $mon, $i)), $r['title_text']);
					$r['target_year'] = $row['year'];
					$r['target_mon'] = $row['mon'];
					$r['target_day'] = $row['day'];
					// 追加
					$r['scdlid'] = $r['id'];	// クローンするとIDが消えるため
					$r['user'] = $r['user'];	// クローンすると消える（クエリが発行されない）
					$r['building'] = $r['building'];	// クローンすると消える（クエリが発行されない）
					$row['data'][] = clone $r;
				}
			}
			$schedules[] = $row;
		}
		// 後ろを埋める
		$week = date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $last_day))) == 0 ? 7 : date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $last_day)));
		for ($i = 0; $i < 7 - $week; $i++) {
			$row = array();
			$row['week'] = date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, ($i + 1)) . " + 1month"));
			$schedules[] = $row;
		}

		return $schedules;
	}

	private function make_mini_calendar($year, $mon) {
		$model = $this->model_name;

		$schedules = array();

		// 月末
		$last_day = date('t', mktime(0, 0, 0, $mon, 1, $year));
		// 前の月の情報
		$prev_year = date('Y', strtotime(sprintf("%04d/%02d/%02d - 1 day", $year, $mon, 1)));
		$prev_mon = date('m', strtotime(sprintf("%04d/%02d/%02d - 1 day", $year, $mon, 1)));
		$prev_last_day = date('t', strtotime(sprintf("%04d/%02d/%02d - 1 day", $year, $mon, 1)));
		// 次の月の情報
		$next_year = date('Y', strtotime(sprintf("%04d/%02d/%02d +1 day", $year, $mon, $last_day)));
		$next_mon = date('m', strtotime(sprintf("%04d/%02d/%02d +1 days", $year, $mon, $last_day)));
		$next_last_day = date('t', strtotime(sprintf("%04d/%02d/%02d +1 day", $year, $mon, $last_day)));
		//print $last_day;exit;

		// 月曜日からはじまるため、空白のデータを入れる
		$week = date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, 1))) == 0 ? 7 : date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, 1)));
		for ($i = 1; $i < $week; $i++) {
			$row = array();
			$row['year'] = $prev_year;
			$row['mon']	 = $prev_mon;
			$row['day']	 = $prev_last_day - ($week - $i - 1);
			$row['week'] = date('w', strtotime(sprintf("%04d/%02d/%02d", $row['year'], $row['mon'], $row['day'])));
			$row['mode'] = "prev";
			$row['link_detail'] = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $row['year'] . '/' . $row['mon'] . '/' . $row['day']), 'GO');
			$schedules[] = $row;
		}
		for ($i = 1; $i <= $last_day; $i++) {
			$row = array();
			// 日付
			$row['year']	 = $year;
			$row['mon']		 = $mon;
			$row['day']		 = $i;
			$row['week']	 = date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $i)));
			$row['mode']	 = "now";
			$row['link_detail'] = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $row['year'] . '/' . $row['mon'] . '/' . $row['day']), 'GO');
			$schedules[] = $row;
		}
		// 後ろを埋める
		$week = date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $last_day))) == 0 ? 7 : date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $last_day)));
		for ($i = 0; $i < 7 - $week; $i++) {
			$row = array();
			$row['year'] = $next_year;
			$row['mon']	 = $next_mon;
			$row['day']	 = $i + 1;
			$row['week'] = date('w', strtotime(sprintf("%04d/%02d/%02d", $row['year'], $row['mon'], $row['day'])));
			$row['mode'] = "next";
			$row['link_detail'] = \Html::anchor(\Uri::create($model::$_kind_name . '/calendar/' . $row['year'] . '/' . $row['mon'] . '/' . $row['day']), 'GO');
			$schedules[] = $row;
		}
		return $schedules;
	}

	/**
	 * [is_target_day]
	 * @param  [type]  $target_year
	 * @param  [type]  $target_mon
	 * @param  [type]  $target_day
	 * @param  [type]  $row
	 * @return boolean
	 */
	private function is_target_day($target_year, $target_mon, $target_day, $row, $opt = null) {
		$result = false;
		$target_unixtime		 = strtotime(sprintf("%04d/%02d/%02d 00:00:00", $target_year, $target_mon, $target_day));
		$start_unixtime			 = strtotime(date('Y/m/d 00:00:00', strtotime($row['start_date'])));
		$end_unixtime			 = strtotime(date('Y/m/d 23:59:59', strtotime($row['end_date'])));
		$target_week			 = date('w', $target_unixtime);

		$model = $this->model_name;

		// 絞り込まれているかどうか
		// DBからクエリを流すと重いのでここで判断する
		if ($opt == null) {
			$is_member = false;
			if ((\Session::get($model::$_kind_name . "narrow_uid") > 0 || \Session::get($model::$_kind_name . "narrow_ugid") > 0) && $model::$_kind_name == "scdl") {

				foreach ($row['user'] as $v) {
					if (\Session::get($model::$_kind_name . "narrow_uid") > 0
							&& \Session::get($model::$_kind_name . "narrow_uid") == $v['id']) {
						$is_member = true;
						break;
					} else if (!\Session::get($model::$_kind_name . "narrow_uid")
						&& \Session::get($model::$_kind_name . "narrow_ugid") > 0) {
						foreach ($v->usergroup as $v2) {
							if ($v2->id == \Session::get($model::$_kind_name . "narrow_ugid")) {
								$is_member = true;
								break;
							}
						}
					}
				}
				if (!$is_member) { return false; }
			}
			if ((\Session::get($model::$_kind_name . "narrow_bid") > 0 || \Session::get($model::$_kind_name . "narrow_bgid") > 0) && $model::$_kind_name == "reserve") {
				$is_building = false;
				foreach ($row['building'] as $v) {
					if (\Session::get($model::$_kind_name . "narrow_bid") > 0
						&& \Session::get($model::$_kind_name . "narrow_bid") == $v['item_id']) {
						$is_building = true;
						break;
					} else if (\Session::get($model::$_kind_name . "narrow_bgid") > 0
						&& !\Session::get($model::$_kind_name . "narrow_bid")) {
						// グループの場合
						if (\Session::get($model::$_kind_name . "narrow_bgid") == $v['item_group2']) {
							$is_building = true;
							break;
						}
					}
					
				}
				if (!$is_building) { return false; }
			}
		}
		switch ($row['repeat_kb']) {
			case 0:
				// なし
				// 単純に開始日付と終了日付から判定
				$result = ($start_unixtime <= $target_unixtime && $end_unixtime >= $target_unixtime);
				break;

			case 1:
				// 毎日
				// 繰り返し終了日時より前であればtrue
				if ($start_unixtime <= $target_unixtime && $end_unixtime >= $target_unixtime) {
					$result = !$this->checkDeleteDay($row['delete_day'], $target_year, $target_mon, $target_day);
				}
				break;

			case 2:
				// 毎日(土日除く)
				if ($target_week != 0 && $target_week != 6) {
					// 土日以外かつ繰り返し終了日時より前の場合
					if ($start_unixtime <= $target_unixtime && $end_unixtime >= $target_unixtime) {
						$result = !$this->checkDeleteDay($row['delete_day'], $target_year, $target_mon, $target_day);
					}
				}
				break;

			case 3:
				// 毎週
				// 繰り返し終了日時より前で対象の曜日以外
				if ($start_unixtime <= $target_unixtime && $end_unixtime >= $target_unixtime) {
					if ($target_week == $row['week_kb']) {
						$result = !$this->checkDeleteDay($row['delete_day'], $target_year, $target_mon, $target_day);
					}
				}
				break;

			case 4:
				// 毎月
				// 繰り返し終了日時より前で開始日時と終了日時の間であれば
				if ($start_unixtime <= $target_unixtime && $end_unixtime >= $target_unixtime) {
					if ($target_day == $row['target_day']) {
						$result = !$this->checkDeleteDay($row['delete_day'], $target_year, $target_mon, $target_day);
					}
				}
				break;
			case 5:
				// 毎年
				if ($start_unixtime <= $target_unixtime && $end_unixtime >= $target_unixtime) {
					if ($target_day == $row['target_day'] && $target_mon == $row['target_month']) {
						$result = !$this->checkDeleteDay($row['delete_day'], $target_year, $target_mon, $target_day);
					}
				}
				break;

			case 6:
				// 週指定
				// 繰り返し終了日時より前で対象の曜日以外
				if ($start_unixtime <= $target_unixtime && $end_unixtime >= $target_unixtime) {
					if ($target_week == $row['week_kb']) {
						$result = !$this->checkDeleteDay($row['delete_day'], $target_year, $target_mon, $target_day);
						if ($result && $row['week_index']) {
							// 第何週指定がある場合
							$result = (ceil($target_day / 7) == $row['week_index']);
						}
					}

					if ($target_week == $row['week_kb_option1'] && $result == false) {
						$result = !$this->checkDeleteDay($row['delete_day'], $target_year, $target_mon, $target_day);
						if ($result && $row['week_index_option1']) {
							// 第何週指定がある場合
							$result = (ceil($target_day / 7) == $row['week_index_option1']);
						}
					}
					if ($target_week == $row['week_kb_option2'] && $result == false) {
						$result = !$this->checkDeleteDay($row['delete_day'], $target_year, $target_mon, $target_day);
						if ($result && $row['week_index_option2']) {
							// 第何週指定がある場合
							$result = (ceil($target_day / 7) == $row['week_index_option2']);
						}
					}
				}
				break;
		}

		return $result;
	}

	/**
	 * [checkDeleteDay description]
	 * @param  [type] $v    [description]
	 * @param  [type] $year [description]
	 * @param  [type] $mon  [description]
	 * @param  [type] $day  [description]
	 * @return [type]       [description]
	 */
	private function checkDeleteDay($v, $year, $mon, $day) {
		$d = sprintf("\[%04d\/%02d\/%02d\]", $year, $mon, $day);
		return preg_match("/$d/", $v);
	}

	/**
	 * 週ごとに表示用に週の最初の日付を取得する
	 * 
	 * @param  [type]  $year
	 * @param  [type]  $mon
	 * @param  integer $day
	 * @return [type]
	 */
	private function week_first_date($year, $mon, $day = 1) {

		if (!$day) { $day = 1; }
		$week = date('w', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $day)));
		if ($week == 0) { $week = 7; }
		$target = $day - ($week - 1);
		if ($target <= 0) {
			// 2週目に
			$year = date('Y', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $day) . '-1 month'));
			$mon = date('m', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $day) . '-1 month'));
			$lastday = date('t', strtotime(sprintf("%04d/%02d/%02d", $year, $mon, $day)));
			$target = $lastday + ($target );
		}
		return array($year, $mon, $target);
	}

	/**
	 * 重複データがあるかどうかをチェック
	 * 
	 * @param  [type] $syear        [description]
	 * @param  [type] $smon         [description]
	 * @param  [type] $sday         [description]
	 * @param  [type] $shour        [description]
	 * @param  [type] $smin         [description]
	 * @param  [type] $eyear        [description]
	 * @param  [type] $emon         [description]
	 * @param  [type] $eday         [description]
	 * @param  [type] $ehour        [description]
	 * @param  [type] $min          [description]
	 * @param  [type] $repeat_kb    [description]
	 * @param  [type] $week_kb      [description]
	 * @param  [type] $target_day   [description]
	 * @param  [type] $target_month [description]
	 * @return [type]               [description]
	 */
	private function checkOverlap($id, $syear, $smon, $sday, $shour, $smin, $eyear, $emon, $eday, $ehour, $emin, $repeat_kb, $week_kb = null, $target_day = null, $target_month = null, $week_index = null, $week_kb_option1 = null, $week_index_option1 = null, $week_kb_option2 = null, $week_index_option2 = null) {
		$model = $this->model_name;

		$arrUsers = explode("/", \Input::post("hidden_members"));
		$arrBuildings = explode("/", \Input::post("hidden_buildings"));
		$targetMember = array();
		// 連想配列へ
		if ($model::$_kind_name == "scdl") {
			foreach ($arrUsers as $v) {
				if ($v)
					$targetMember[$v] = $v;
			}
		}
		if ($model::$_kind_name == "reserve") {
			foreach ($arrBuildings as $v) {
				if ($v)
					$targetMember[$v] = $v;
			}
		}

		// 最大チェック数
		$maxCount = 10;
		// 最大調査数
		$maxSearchCount = 1000;

		// 全てチェックするかどうか
		$start_day  = sprintf("%04d/%02d/%02d", $syear, $smon, $sday);
		$end_day    = sprintf("%04d/%02d/%02d", $eyear, $emon, $eday);
		$start_time = sprintf("%02d:%02d:00", $shour, $smin);
		$end_time   = sprintf("%02d:%02d:00", $ehour, $emin);
		
	    $start_day_timestamp = strtotime($start_day);
	    $end_day_timestamp = strtotime($end_day);
	 
	    $start_datetime_timestamp = strtotime($start_day . " " . $start_time);
	    $end_datetime_timestamp = strtotime($end_day . " " . $end_time);

	    // 何秒離れているかを計算
	    $seconddiff = abs($end_day_timestamp - $start_day_timestamp);

	    // 日数に変換
	    $daydiff = $seconddiff / (60 * 60 * 24);
	    $result = array();
		// 単純に開始日付と終了日付から判定

	 	for ($i = 0; $i <= $daydiff; $i++) {
	 		if ($maxSearchCount < $i) { return $result; }
	 		$target_year_from = date('Y', strtotime($start_day . ' +' . $i . "days"));
	 		$target_month_from = date('m', strtotime($start_day . ' +' . $i . "days"));
	 		$target_day_from = date('d', strtotime($start_day . ' +' . $i . "days"));
	 		$target_date = date('Y/m/d', strtotime($start_day . ' +' . $i . "days"));
	 		$target_week = date('w', strtotime($start_day . ' +' . $i . "days"));
	 		// 対象の日データを取得
	 		if (strtotime(date('Y/m/d') . " 00:00:00") > strtotime($start_day . '00:00:00 +' . $i . "days")) {
	 			continue;
	 		}
	 		$flgContinue = false;
	 		switch ($repeat_kb) {
	 			case 0:
	 				// 指定なし

	 				break;
	 			case 1:
	 				// 毎日
	 				break;
	 			case 2:
	 				// 毎日（土日除く）

					if ($target_week == 0 || $target_week == 6) {
				 		$flgContinue = true;
					}
		 			break;
		 		case 3:
					// 毎週
					if ($target_week != $week_kb) {
						$flgContinue = true;
						continue;
					}
					break;
				case 4:
					// 毎月
					// 繰り返し終了日時より前で開始日時と終了日時の間であれば
					if ($target_day_from != $target_day) {
				 		$flgContinue = true;
					}
				
					break;
				case 5:
					// 毎年
					if (!($target_day_from == $target_day && $target_month_from == $target_month)) {
				 		$flgContinue = true;
					}
					break;
				case 6:
					if (
						(($target_week == $week_kb) && (ceil($target_day_from / 7) == $week_index))
						|| (($target_week == $week_kb_option1) && (ceil($target_day_from / 7) == $week_index_option1))
						|| (($target_week == $week_kb_option2) && (ceil($target_day_from / 7) == $week_index_option2))
						) {
					} else {
				 		$flgContinue = true;
					}
					break;
	 		}
	 		if ($flgContinue) { continue; }

	 		$schedule_data = \Locomo\Model_Scdl::query()
	 					->where_open()
						->or_where_open()
							->where("start_date", "<=", $target_date)
							->where("end_date", ">=", $target_date)
						->or_where_close()
						->or_where_open()
							->where("start_date", "<=", $target_date)
							->where("end_date", ">=", $target_date)
						->or_where_close()
						->or_where_open()
							->where("start_date", "<=", $target_date)
							->where("end_date", ">=", $target_date)
						->or_where_close()
						->or_where_open()
							->where("start_date", ">=", $target_date)
							->where("end_date", "<=", $target_date)
						->or_where_close()
						->where_close()
						->where("deleted_at", "is", null)
						->where("kind_flg", $model::$_kind_flg)
						->where("id", "<>", (!$id) ? 0 : $id)
						->get();

			foreach ($schedule_data as $r) {
				if ($this->is_target_day($target_year_from, $target_month_from, $target_day_from, $r, "all")) {
					if ($repeat_kb == 0) {
						if ($r['repeat_kb'] == 0) {
							// 繰り返しじゃない場合
							$target_start_timestamp = strtotime($r['start_date'] . " " . $r['start_time']);
							$target_end_timestamp = strtotime($r['end_date'] . " " . $r['end_time']);
						} else {
							$target_start_timestamp = strtotime($target_date . " " . $r['start_time']);
							$target_end_timestamp = strtotime($target_date . " " . $r['end_time']);
						}

						if ( !(($target_end_timestamp <= $start_datetime_timestamp && $target_end_timestamp < $end_datetime_timestamp)
							|| ($target_start_timestamp > $start_datetime_timestamp && $target_start_timestamp >= $end_datetime_timestamp))
							) {

							$target_user = \Model_Usr::find($r['user_id']);
							$r['user_data'] = $target_user;
							$r['target_date'] = $r['start_date'] . " " . $r['start_time'] . " - " . $r['end_date'] . " " . $r['end_time'];
							// スケジューラのメンバーが被っているかどうか
							$overlapUser = $this->isExistOverlapTarget($r, $targetMember);
							$push = true;
							foreach ($result as $rrow) {
								if ($rrow['id'] == $r['id']) { $push = false; break; }
							}
							if ($overlapUser && $push) {
								$r['targetdata'] = $overlapUser;
								$result[] = $r;
							}

							if (count($result) >= $maxCount) { return $result; }
						}
					} else {


						// 日にちが被っているかどうか
						if ( !(($r['end_time'] <= $start_time && $r['end_time'] < $end_time)
							|| ($r['start_time'] >= $end_time && $r['start_time'] > $start_time))
							) {
							$target_user = \Model_Usr::find($r['user_id']);
							$r['user_data'] = $target_user;
							$r['target_date'] = $r['start_date'] . " " . $r['start_time'] . " - " . $r['end_date'] . " " . $r['end_time'];
							// スケジューラのメンバーが被っているかどうか
							$overlapUser = $this->isExistOverlapTarget($r, $targetMember);
							$push =  true;
							foreach ($result as $rrow) {
								if ($rrow['id'] == $r['id']) { $push = false; break; }
							}
							if ($overlapUser && $push) {
								$r['targetdata'] = $overlapUser;
								$result[] = $r;
							}
							
							if (count($result) >= $maxCount) { return $result; }
						}
					}
				}
			}

	 	}

		return $result;
	}

	private function isExistOverlapTarget($row, $data) {
		$model = $this->model_name;
		if ($model::$_kind_name == "scdl") {
			return $this->overlapExistMember($row, $data);
		} else if ($model::$_kind_name == "reserve") {
			return $this->overlapExistBuilding($row, $data);
		}
	}
	private function overlapExistMember($row, $users) {
		foreach ($row['user'] as $user) {
			if (isset($users[$user->id])) {
				return $user['display_name'];
			}
		}
		return "";
	}

	private function overlapExistBuilding($row, $buildings) {
		foreach ($row['building'] as $build) {
			if (isset($buildings[$build->item_id])) {
				return $build['item_name'];
			}
		}
		return "";
	}


	/*
	 * ajax グループIDからユーザリストを返す
	 * @return users の配列
	 */
	public function action_get_user_list()
	{
		if (!\Input::is_ajax()) throw new HttpNotFoundException;;
		$where = array();

		if (\Input::post("gid", 0)) {
			$where = array(array('usergroup.id', '=', \Input::post("gid", 0)));
		} else {
			$where = array();
		}
		$response = \Model_Usr::find('all',
			array(
				'related'   => count($where) ? array('usergroup') : array(),
				'where'=> $where,
				'order_by' => array('pronunciation' => 'asc')
				)
			);
		$result = array();
		$index = 0;
		foreach ($response as $row) {
//			$row[0] = $index;
			$index++;
			$result[] = $row;
		}
		echo $this->response($result, 200); die();
	}

	/*
	 * ajax IDから施設リストを返す
	 * @return users の配列
	 */
	public function action_get_building_list()
	{
		if (!\Input::is_ajax()) throw new HttpNotFoundException;;
		$where = array(array('item_group', 'building'));
		if (\Input::post("bid")) {
			$where = array(
						array('item_group2', '=', \Input::post("bid", 0)),
						array('item_group', 'building')
						);
		}
		$response = \Model_Scdl_Item::find('all',
			array(
				'where'=> $where,
				)
			);
		echo $this->response($response, 200); die();
	}

	/**
	 * [check_error_scdl description]
	 * @return [type] [description]
	 */
	private function check_error_scdl() {
		$flg_exist = false;
		$model = $this->model_name;
		$target_id = "";
		if ($model::$_kind_name == "scdl") {
			// スケジューラの場合はメンバーが必須
			$target_data = explode("/", \Input::post("hidden_members"));
			$target_name = "メンバー";
			$target_id = "member_kizon";
		} else if ($model::$_kind_name == "reserve") {
			// 施設予約の場合は施設が必須
			$target_data = explode("/", \Input::post("hidden_buildings"));
			$target_name = "施設";
			$target_id = "building_kizon";
		}
		foreach ($target_data as $v) {
			if ($v)
				$flg_exist = true;
		}
		if (!$flg_exist) {
			$this->_scdl_errors[$target_id] = $target_name . "を選択してください。";
		}
		// 時間が反転
		if (\Input::post("repeat_kb") == 0) {
			$start = strtotime(\Input::post("start_date") . " " . \Input::post("start_time"));
			$end = strtotime(\Input::post("end_date") . " " . \Input::post("end_time"));
			if ($start > $end) {
				$this->_scdl_errors["start_time"] = "開始時間と終了時間の入力が不正です。";
			} else if (\Input::post("start_date") == "" || \Input::post("end_date") == "") {
				// 自動挿入しようかと思ったが、繰り返しなしの場合は全期間はおかしいのでエラーでとめておく
				$this->_scdl_errors["start_date"] = "開始日付と終了日付を入力してください。";
			}
		} else {
			if (\Input::post("start_time") >= \Input::post("end_time")) {
				$this->_scdl_errors["start_time"] = "開始時間と終了時間の入力が不正です。";
			} else if (\Input::post("start_date") >= \Input::post("end_date")) {
				$this->_scdl_errors["start_date"] = "開始日付と終了日付の入力が不正です。";
			}
		}
		if (\Input::post("repeat_kb") == 4 
			&& (\Input::post("target_day") < 1 || \Input::post("target_day") > 31 || \Input::post("target_day") == "")) {
				$this->_scdl_errors['target_day'] = "対象の日を正しく入力してください。";
		}
		if (\Input::post("repeat_kb") == 5) { 
			if (\Input::post("target_month") < 1 || \Input::post("target_month") > 12 || \Input::post("target_month") == "") {
				$this->_scdl_errors['target_month'] = "対象の月を正しく入力してください。";
			}
			if (\Input::post("target_day") < 1 || \Input::post("target_day") > 31 || \Input::post("target_day") == "") {
				$this->_scdl_errors['target_day'] = "対象の日を正しく入力してください。";
			}
		}
		return (count($this->_scdl_errors) == 0);
	}

	/*
	 * ダッシュボード用「週表示」
	 */
	public function action_dashboard_week_calendar()
	{
		$weeknum = \Locomo\Cal::get_current_weeknum('', 1);
		$current = \Locomo\Cal::get_week_calendar_by_weeknum(date('Y-m'), $weeknum, $start_with = 1);
		list($year, $mon, $day) = explode('-', $current['dates'][0]);
		return self::action_calendar($year, $mon, $day, $mode = 'week');
	}

	/*
	 * ダッシュボード用「日表示」
	 */
	public function action_dashboard_today()
	{
		list($year, $mon, $day) = explode('-', date('Y-m-d'));
		return self::action_calendar($year, $mon, $day);
	}


}
