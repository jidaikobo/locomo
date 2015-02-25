<?php
namespace Locomo;
class Cal
{
	/*
	 * get_week_calendar_by_weeknum()
	 *
	 * @param [str]  $target_month [YYYY-MM]
	 * @param [int]  $target_term  [n]
	 * @param [int]  $start_with   [1:mon, 7:sun default]
	 * @param [bool] $is_recursive 
	 * @return array()
	
	対象の月をYYYY-MMの形式で渡し、対象の週を数字で渡します。
	返ってくるのは、その週の日付と、コントローラ用のURLです。
	
	例）2014年9月第4週の場合
	get_week_calendar_by_weeknum('2014-9', 4)
	
	array(3) {
	  ["dates"]=>
	  array(7) {
	    [0]=>
	    string(10) "2014-09-21"
	    [1]=>
	    string(10) "2014-09-22"
	    [2]=>
	    string(10) "2014-09-23"
	    [3]=>
	    string(10) "2014-09-24"
	    [4]=>
	    string(10) "2014-09-25"
	    [5]=>
	    string(10) "2014-09-26"
	    [6]=>
	    string(10) "2014-09-27"
	  }
	  ["links"]=>
	  array(6) {
	    ["prev_month_link"]=>
	    string(24) "month=2014-08&term=6"
	    ["prev"]=>
	    string(24) "month=2014-09&term=3"
	    ["next_month_link"]=>
	    string(24) "month=2014-10&term=1"
	    ["next"]=>
	    string(24) "month=2014-09&term=5"
	    ["this_month"]=>
	    array(5) {
	      [1]=>
	      string(24) "month=2014-09&term=1"
	      [2]=>
	      string(24) "month=2014-09&term=2"
	      [3]=>
	      string(24) "month=2014-09&term=3"
	      [4]=>
	      string(24) "month=2014-09&term=4"
	      [5]=>
	      string(24) "month=2014-09&term=5"
	    }
	    ["current"]=>
	    string(24) "month=2014-09&term=4"
	  }
	  ["max_week_num"]=>
	  int(5)
	  ["current_week_num"]=>
	  int(4)
	}
	
	*/
	public static function get_week_calendar_by_weeknum($target_month = 'YYYY-MM', $target_term = 1, $start_with = 7, $is_recursive = false)
	{
		$target_term = intval($target_term);

		// 現在
		$current_week_num = static::get_current_weeknum();
		if ($target_month == 'YYYY-MM')
		{
			$target_month = date('Y-m');
			$target_term = $current_week_num;
		}
	
		// 前の月を取得
		$this_year  = date('y', strtotime($target_month.'-01'));
		$this_month = date('n', strtotime($target_month.'-01'));
		$thismonth_maxdate = date('t', strtotime($target_month.'-01'));
		$u_lastmonth = mktime(0, 0, 0, $this_month-1, 1, $this_year);
		$lastmonth_maxdate = date('t', $u_lastmonth);
	
		// 朔日の曜日を取得
		$day_of_first = (int) date('N', strtotime($target_month.'-01'));
	
		// 週の開始日を集める。$nは週。
		// $target_termに7の倍数を足して、マイナス分を引く。
		$week_1st_dates = array();
		for ($n = 1 ; $n <= 6 ; $n++)
		{
			$year  = $this_year;
			$month = $this_month;
			$adjust = $start_with == 7 ? 1 : 2 ;
	
			// 第一週の週の開始日の日付
			if($n === 1)
			{
				// 一日が週の開始日だったらそのまま
				if($day_of_first === $start_with)
				{
					$date = 1;
				} else {
				// 第一週週の開始日が前月の場合、一週間前の開始日をとる
					$date  = $lastmonth_maxdate - $day_of_first + $adjust;
					$month = $month - 1;
					$year  = $year < 0 ? $year - 1 : $year ;
				}
			} else {
				// それ以外の週の週の開始日の日付
				if ($start_with == 7)
				{
					$basedate = $day_of_first === $start_with ? $n : $n - 1;
				} else {
					$basedate = $n - 1;
				}
				$date = $basedate * 7 - $day_of_first + $adjust;
			}
			$week_1st_dates[$n] = date('Y-n-j', mktime(0, 0, 0, $month, $date, $year));
		}

		// $week_1st_datesの最大値を制限
		foreach($week_1st_dates as $k => $week_1st_date)
		{
			if(strtotime($week_1st_date) > strtotime($target_month.'-'.$thismonth_maxdate)) unset($week_1st_dates[$k]);
		}
	
		// target_termの値を確認し、不正な値だったらfalse
		if(count($week_1st_dates) < $target_term || $target_term <= 0) return false;
	
		// 指定週の週の開始日の日付を返す
		$the_week_1st_date = $week_1st_dates[$target_term];
	
		// 配列を作成
		$dates = array();
		$current = 0;
		for ($n = 0 ; $n < 7 ; $n++)
		{
			// 日付を年月日に分割
			list($y, $m, $d) = explode('-',$the_week_1st_date);
	
			// 日付を加算し、月や年が増えるときには処理
			$d = $d + $n;
			$dates[] = date('Y-m-d', mktime(0, 0, 0, intval($m), intval($d), intval($y)));
		}
	
		// 該当月の最大週数を取得
		$maxweek_of_thismonth = max(array_keys($week_1st_dates));
	
		// 戻るリンクを生成するために前の月の最大週を取得する
		$prev_term4finalweek = 0;
		$prev_term4before_finalweek = 0;
		$prev_month = date('Y-m', mktime(0, 0, 0, $this_month-1, 1, $this_year));
		if ( ! $is_recursive)
		{
			// 前の月について自分（get_week_calendar_by_weeknum()）に尋ねる
			$prev_month_val = static::get_week_calendar_by_weeknum($prev_month, 1, $start_with, $is_recursive = true);
			$prev_term4finalweek = $prev_month_val['max_week_num'];
			$prev_term4before_finalweek = $prev_month_val['max_week_num'] - 1;
		}
	
		// リンク文字列を生成する
		$links = array();
	
		// 戻る系リンク
		$links['prev_month_link'] = 'month='.$prev_month.'&amp;term='.$prev_term4finalweek;
		if ($target_term == 1)
		{
			// 現在が第一週で朔日が週の開始日だったら「前」リンクは前月最終週
			if ($day_of_first == 7)
			{
				$links['prev'] = $links['prev_month_link'];
			} else {
			// 現在が第一週で朔日が週の開始日でなければ「前」リンクは前月最終週の前週に
				$links['prev'] = 'month='.$prev_month.'&amp;term='.$prev_term4before_finalweek;
			}
		} else {
			$prev_term = $target_term-1;
			$links['prev'] = 'month='.$target_month.'&amp;term='.$prev_term;
		}
	
		// 次へ系リンク
		$next_month = date('Y-m', mktime(0, 0, 0, $this_month+1, 1, $this_year));
		$links['next_month_link'] = 'month='.$next_month.'&amp;term=1';
		if ($target_term == $maxweek_of_thismonth)
		{
			// 月の最終日が土曜日だったら
			if (date('N', strtotime($target_month.'-'.$thismonth_maxdate)) == 6)
			{
				$links['next'] = $links['next_month_link'];
			} else {
				$links['next'] = 'month='.$next_month.'&amp;term=2';
			}
		} else {
			$next_term = $target_term+1;
			$links['next'] = 'month='.$target_month.'&amp;term='.$next_term;
		}
	
		// 第n週リンク
		for( $n = 1 ; $n <= $maxweek_of_thismonth ; $n++ )
		{
			$links['this_month'][$n] = 'month='.$target_month.'&amp;term='.$n;
		}
	
		// 現在の週
		$links['current'] = 'month='.date('Y-m').'&amp;term='.$current_week_num;
	
		// 戻り値
		$retvals = array();
		$retvals['dates'] = $dates;
		$retvals['links'] = $links;
		$retvals['max_week_num'] = $maxweek_of_thismonth;
		$retvals['current_week_num'] = $current_week_num;
	
		return $retvals;
	}
	
	// get_current_weeknum
	// start_with のデフォルトは7（日曜日）
	// thx http://generation1986.g.hatena.ne.jp/primunu/20080317/1205767155
	public static function get_current_weeknum($ymd = '', $start_with = 7)
	{
		$now = $ymd ? strtotime($ymd) : time();
//		$saturday = 6;
		$end_of_week_day = $start_with == 7 ? 6 : 7 ;//日曜始まり（7）なら土曜日が終端日。月曜始まりなら日曜日が終端日
		$week_day = 7;
		$w = intval(date('N', $now)); // 1-7 の曜日
		$d = intval(date('d', $now)); // 日

		// 終端日を発見
		if ($w != $end_of_week_day)
		{
			$w = ($end_of_week_day - $w) + $d;
		} else { // 終端日の場合を修正
			$w = $d;
		}
		return ceil($w/$week_day);
	}
}
