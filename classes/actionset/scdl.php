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
				$controller.'/create',
				$controller.'/edit',
				$controller.'/delete',
				$controller.'/copy',
				$controller.'/viewdetail',
				$controller.'/attend',
				$controller.'/regchange',
				$controller.'/somedelete',
				$controller.'/someedit',
				$controller.'/calendar',
				$controller.'/get_user_list',
				$controller.'/get_building_list',
				$controller.'/dashboard_week_calendar',
				$controller.'/dashboard_today',
				$controller.'/view_invisible', // action is not exist yet
		);
		\Arr::set($retvals, 'dependencies', $actions);
		\Arr::set($retvals, 'action_name', 'スケジューラの管理権限');
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
				// 単日イベントの場合（開始日と終了日が一致）は、その値を使う。
				if ($obj->start_date == $obj->end_date)
				{
					$y = date('Y', strtotime($obj->start_date));
					$m = date('m', strtotime($obj->start_date));
					$d = date('d', strtotime($obj->start_date));
				} else if (\Uri::segment(4)){
					$y = \Uri::segment(4) ?: $y ;
					$m = \Uri::segment(5) ?: $m ;
					$d = \Uri::segment(6) ?: $d ;
				}

				$ym = date('Y-m', strtotime($y.'-'.$m.'-'.$d));
				$ymd = date('Y-m-d', strtotime($y.'-'.$m.'-'.$d));
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
		$urls = array(
			0 => array($controller.DS."calendar/", '今月'),
			1 => array($controller.DS."calendar/".$ym_str, '月表示'),
			3 => array($controller.DS."calendar/{$y}/{$m}/{$d}", '日表示'),
		);
		if ($controller == '\Controller_Scdl')
		{
			$urls[2] = array($controller.DS."calendar/".$week_1st_day.'/week/member', '週表示');
		} else {
			$urls[2] = array($controller.DS."calendar/".$week_1st_day.'/week/building', '週表示');
		}
		ksort($urls);
		$urls = \Request::main()->action == 'create' ? array() : $urls ;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'カレンダ',
			'show_at_top'  => true,
			'explanation'  => 'カレンダ形式で表示します。',
			'order'        => 1
		);
		return $retvals;
	}

	/**
	 * actionset_create
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action != 'create')
		{
			$urls = array(array($controller.DS."create/", '新規作成'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '新規作成',
			'show_at_top'  => true,
			'explanation'  => '予定の新規追加です。',
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
			$urls = array(array($controller.DS."viewdetail/" . $id, '閲覧'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '閲覧',
			'show_at_top'  => true,
			'explanation'  => 'スケジューラの個票閲覧権限です。',
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
			$urls = array(array($controller.DS."edit/" . $id, '編集'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '編集',
			'show_at_top'  => true,
			'explanation'  => 'スケジューラの個票編集権限です。',
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
				$urls = array(array($controller.DS."attend/" . $id, '出席確認'));
			}
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '出席',
			'show_at_top'  => true,
			'explanation'  => '出席するかどうかを確認',
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
				$urls = array(array($controller.DS."somedelete/$id/".date('Y/m/d', $datestr), '部分削除', array('class' => 'confirm')));
			}
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '部分削除',
			'show_at_top'  => true,
			'explanation'  => '部分削除',
			'order'        => 12
		);
		return $retvals;
	}

	/**
	 * [actionset_someedit description]
	 * @param  [type] $controller [description]
	 * @param  [type] $obj        [description]
	 * @param  [type] $id         [description]
	 * @param  array  $urls       [description]
	 * @return [type]             [description]
	 */
	public static function actionset_someedit($controller, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'viewdetail' && $id && $obj->repeat_kb >= 1)
		{
			$y = \Uri::segment(4);
			$m = \Uri::segment(5);
			$d = \Uri::segment(6);
			if ($datestr = strtotime("$y/$m/$d"))
			{
				$urls = array(array($controller.DS."someedit/$id/".date('Y/m/d', $datestr), '部分編集', array()));
			}
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '部分編集',
			'show_at_top'  => true,
			'explanation'  => '部分編集',
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
			$urls = array(array($controller.DS."regchange/" . $id . "/" . \Uri::segment(4) . "/" . \Uri::segment(5) . "/" . \Uri::segment(6), '本登録',array('class' => 'confirm', 'data-jslcm-msg' => '本登録してよいですか？')));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '本登録',
			'show_at_top'  => true,
			'explanation'  => '本登録',
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
			$urls = array(array($controller.DS."copy/?from=" . $id, 'コピー'));
		}
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'コピー',
			'show_at_top'  => true,
			'explanation'  => 'コピー',
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
			'explanation'  => '',
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
		// とりあえず出す
		if (isset($obj->deleted_at) && is_null($obj->deleted_at) && $id)
		{
//			if (isset($obj->creator_id) && $obj->creator_id == \Auth::get('id'))
//			if (isset($obj->user_id) && $obj->user_id == \Auth::get('id'))
//			{
				$urls = array(array($controller.DS."delete/".$id, '削除', array('class' => 'confirm', 'data-jslcm-msg' => '削除してよいですか？')));
//			}
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '削除',
			'show_at_top'  => true,
			'explanation'  => 'スケジューラの個票削除権限です。',
		);
		return $retvals;
	}

	/**
	 * delete_others()
	 */
	public static function actionset_delete_others($controller, $obj = null, $id = null, $urls = array())
	{
/*
		$retvals = self::actionset_delete($controller, $obj, $id);
		if (isset($obj->deleted_at) && is_null($obj->deleted_at) && $id)
		{
			$urls = array(array($controller.DS."delete_others/".$id, '削除', array('class' => 'confirm', 'data-jslcm-msg' => '削除してよいですか？')));
			// delete_othersのACLがなければ出さない
		}

		\Arr::set($retvals, 'urls', $urls);
		return $retvals;
*/
		return array();
	}

	/**
	 * actionset_adminindex
	 */
	public static function actionset_adminindex($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Auth::is_admin())
		{
			$urls = array(array($controller.DS."index_admin/", '管理者向け一覧'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '管理者向け一覧',
			'show_at_top'  => true,
			'explanation'  => '管理者向けの一覧です。',
			'order'        => 50
		);
		return $retvals;
	}

	/**
	 * actionset_index_deleted
	 */
	public static function actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Auth::is_admin())
		{
			$urls = array(array($controller.DS."index_deleted/", '削除済み項目一覧'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '削除済み項目一覧',
			'show_at_top'  => true,
			'explanation'  => '管理者向けの削除済み項目一覧です。',
			'order'        => 50
		);
		return $retvals;
	}
}
