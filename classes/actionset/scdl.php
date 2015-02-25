<?php
namespace Locomo;
class Actionset_Scdl extends \Actionset
{
	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;

	/*
	(str)  realm         メニューの表示位置。デフォルトはbase
	(arr)  urls          メニューに表示するリンク先
	(bool) show_at_top   モジュール／コントローラトップに表示するかどうか
	(str)  action_name   ACL設定画面などで用いる
	(str)  explanation   モジュール先頭画面等で用いる説明文
	(str)  acl_exp       ACL設定画面などで用いる説明文
	(str)  help          ヘルプ
	(int)  order         表示順
	(arr)  dependencies  このアクションセットが依存するアクション。ACLで用いる
	*/

	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
				$controller.'::action_create',
				$controller.'::action_edit',
				$controller.'::action_delete',
				$controller.'::action_copy',
				$controller.'::action_viewdetail',
				$controller.'::action_attend',
				$controller.'::action_regchange',
				$controller.'::action_somedelete',
				$controller.'::action_calendar',
				$controller.'::action_get_user_list',
				$controller.'::action_get_building_list',
				$controller.'::action_dashboard_week_calendar',
				$controller.'::action_dashboard_today',
				$controller.'::action_view_invisible', // action is not exist yet
		);
		\Arr::set($retvals, 'dependencies', $actions);
		\Arr::set($retvals, 'action_name', 'スケジューラの管理権限');
		\Arr::set($retvals, 'acl_exp', 'スケジューラの管理権限です。');
		return $retvals;
	}

	/**
	 * actionset_calendar
	 */
	public static function actionset_calendar($controller, $obj = null, $id = null, $urls = array())
	{
		// datevals
		$y = date('Y');
		$m = date('m');
		$d = date('d');
		$ym = date('Y-m');
		$ymd = date('Y-m-d');

		// 月および週表示
		if (\Request::main()->action == 'calendar')
		{
			// コントローラの場合。数字のみの四桁だったら年と見なす
			if (is_numeric(\Uri::segment(3)) && strlen(\Uri::segment(3)) == 4)
			{
				$y = \Uri::segment(3) ?: $y ;
				$m = \Uri::segment(4) ?: $m ;
				$d = \Uri::segment(5) ?: $d ;
			// モジュールだとリンクが深くなるので簡易対応
			} elseif (is_numeric(\Uri::segment(4)) && strlen(\Uri::segment(4)) == 4) {
				$y = \Uri::segment(4) ?: $y ;
				$m = \Uri::segment(5) ?: $m ;
				$d = \Uri::segment(6) ?: $d ;
			}
			$ym = date('Y-m', strtotime("$y/$m/$d"));
			$ymd = date('Y-m-d', strtotime("$y/$m/$d"));
		// 編集画面と閲覧画面
		} elseif (in_array(\Request::main()->action, ['edit', 'viewdetail']) && is_object($obj)) {
			// 項目が日付を持っていたらそれを使う
			if(isset($obj->start_date))
			{
				$y = date('Y', strtotime($obj->start_date));
				$m = date('m', strtotime($obj->start_date));
				$ym = date('Y-m', strtotime($obj->start_date));
				$ymd = date('Y-m-d', strtotime($obj->start_date));
			}
		}

		// マイナスの値対策
		$y = abs($y);
		$m = abs($m);
		$d = abs($d);

		// 与えられている日付から第何週かを得る
		$weeknum = \Locomo\Cal::get_current_weeknum($ymd, $start_with = 1) ;
		$ym = $ym ?: date('Y-m');

		// 週の第一日
		$current = \Locomo\Cal::get_week_calendar_by_weeknum($ym, $weeknum, $start_with = 1);
		list($year, $mon, $day) = explode('-', $current['dates'][0]);
		$week_1st_day = $year.DS.$mon.DS.$day;
		$ym_str = $y.DS.$m;

		// uri
		$actions = array(
			array($controller.DS."calendar/", '今月'),
			array($controller.DS."calendar/".$ym_str, '月表示'),
			array($controller.DS."calendar/".$week_1st_day.'/week', '週表示'),
		);
		$urls = static::generate_urls($controller.'::action_edit', $actions, ['create']);

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'カレンダ',
			'show_at_top'  => true,
			'acl_exp'      => 'カレンダ形式のスケジューラの表示権限です。',
			'explanation'  => 'カレンダ形式のスケジューラの表示権限です。',
			'help'         => '',
			'order'        => 1
		);
		return $retvals;
	}

	/**
	 * actionset_create
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."create/", '新規作成'));
		$urls = static::generate_urls($controller.'::action_create', $actions, ['create']);

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '新規作成',
			'show_at_top'  => true,
			'acl_exp'      => '予定の新規追加権限です。',
			'explanation'  => '予定の新規追加です。',
			'help'         => '',
			'order'        => 5
		);
		return $retvals;
	}

	/**
	 * actionset_viewdetail
	 */
	public static function actionset_viewdetail($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'edit' && $id)
		{
			$actions = array(array($controller.DS."viewdetail/" . $id, '閲覧'));
			$urls = static::generate_urls($controller.'::action_viewdetail', $actions);
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '閲覧',
			'show_at_top'  => true,
			'acl_exp'      => 'スケジューラの個票閲覧権限です。',
			'explanation'  => 'スケジューラの個票閲覧権限です。',
			'help'         => '',
		);
		return $retvals;
	}

	/**
	 * actionset_edit
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'viewdetail' && $id)
		{
			$actions = array(array($controller.DS."edit/" . $id, '編集'));
			$urls = static::generate_urls($controller.'::action_edit', $actions);
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '編集',
			'show_at_top'  => true,
			'acl_exp'      => 'スケジューラの個票編集権限です。',
			'explanation'  => 'スケジューラの個票編集権限です。',
			'help'         => '',
		);
		return $retvals;
	}

	/**
	 * actionset_attend
	 */
	public static function actionset_attend($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'viewdetail' && $id && $obj->attend_flg)
		{
			$schedule_data = \DB::select()->from("lcm_scdls_members")->where("schedule_id", $id)->where("user_id", \Auth::get('id'))->execute()->as_array();
			// 自分がメンバーであった場合
			if (count($schedule_data) > 0) {
				$actions = array(array($controller.DS."attend/" . $id, '出席確認'));
				$urls = static::generate_urls($controller.'::action_attend', $actions, ['']);
			}
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '出席',
			'show_at_top'  => true,
			'acl_exp'      => '出席するかどうかを確認',
			'explanation'  => '出席するかどうかを確認',
			'help'         => '',
			'order'        => 10
		);
		return $retvals;
	}


	/**
	 * actionset_somedelete
	 */
	public static function actionset_somedelete($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'viewdetail' && $id && $obj->repeat_kb >= 1)
		{
			$y = \Uri::segment(4);
			$m = \Uri::segment(5);
			$d = \Uri::segment(6);
			if ($datestr = strtotime("$y/$m/$d"))
			{
				$actions = array(array($controller.DS."somedelete/$id/".date('Y/m/d', $datestr), '部分削除', array('class' => 'confirm')));
				$urls = static::generate_urls($controller.'::action_somedelete', $actions, ['']);
			}
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '部分削除',
			'show_at_top'  => true,
			'acl_exp'      => '部分削除',
			'explanation'  => '部分削除',
			'help'         => '',
			'order'        => 12
		);
		return $retvals;
	}

	/**
	 * actionset_regchange
	 */
	public static function actionset_regchange($controller, $obj = null, $id = null, $urls = array())
	{

		if(\Request::main()->action == 'viewdetail' && $id && $obj->provisional_kb)
		{
			$actions = array(array($controller.DS."regchange/" . $id . "/" . \Uri::segment(4) . "/" . \Uri::segment(5) . "/" . \Uri::segment(6), '本登録'));
			$urls = static::generate_urls($controller.'::action_regchange', $actions, ['']);
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '本登録',
			'show_at_top'  => true,
			'acl_exp'      => '本登録',
			'explanation'  => '本登録',
			'help'         => '',
			'order'        => 10
		);
		return $retvals;
	}


	/**
	 * actionset_copy
	 */
	public static function actionset_copy($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action != 'edit' && $id) {
			$actions = array(array($controller.DS."copy/?from=" . $id, 'コピー'));
			$urls = static::generate_urls($controller.'::action_copy', $actions, []);
		}
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'コピー',
			'show_at_top'  => true,
			'acl_exp'      => 'コピー',
			'explanation'  => 'コピー',
			'help'         => '',
			'order'        => 20
		);
		return $retvals;
	}
	
/*
	// actionset_view
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '',
			'show_at_top'  => true,
			'acl_exp'      => '',
			'explanation'  => '',
			'help'         => '',
			'order'        => 10
		);
		return $retvals;
	}
*/
	/**
	 * delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		// ユーザIDが一致しない項目では削除リンクを出さない
		if (isset($obj->deleted_at) && is_null($obj->deleted_at) && $id)
		{
//			if (isset($obj->creator_id) && $obj->creator_id == \Auth::get('id'))
			if (isset($obj->user_id) && $obj->user_id == \Auth::get('id'))
			{
				$actions = array(array($controller.DS."delete/".$id, '削除', array('class' => 'confirm', 'data-jslcm-msg' => '削除してよいですか？')));
				$urls = static::generate_urls($controller.'::action_delete', $actions, ['create']);
			}
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '削除',
			'show_at_top'  => true,
			'acl_exp'      => 'スケジューラの個票削除権限です。',
			'explanation'  => 'スケジューラの個票削除権限です。',
			'help'         => '',
		);
		return $retvals;
	}

	/**
	 * delete_others()
	 */
	public static function actionset_delete_others($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = self::actionset_delete($controller, $obj, $id);
		if (isset($obj->deleted_at) && is_null($obj->deleted_at) && $id)
		{
			$actions = array(array($controller.DS."delete_others/".$id, '削除', array('class' => 'confirm', 'data-jslcm-msg' => '削除してよいですか？')));
			// delete_othersのACLがなければ出さない
			$urls = static::generate_urls($controller.'::action_delete_others', $actions, ['create']);
		}

		\Arr::set($retvals, 'urls', $urls);
		return $retvals;
	}
}
