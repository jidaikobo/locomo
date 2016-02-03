<?php
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
	$currentday = (date("Y") == $year && date("n") == $mon ) ? date("j") : '';
?>
<?php /*
<h1><?php print $year; ?>年<?php print (int)$mon; ?>月 月間カレンダ</h1>
*/ ?>
<h1 id="page_title" class="clearfix">
	<a href="javascript: void(0);" class="toggle_item disclosure nomarker tabindex_ctrl">
		<?php print $year; ?>年<?php print (int)$mon; ?>月 月間カレンダ
		<span class="icon fr ">
		<img src="<?php echo \Uri::base() ?>lcm_assets/img/system/mark_search.png" alt="">
		<span class="hide_if_smalldisplay" aria-hidden="true" role="presentation">検索</span>
		<span class="skip"> エンターで検索条件を開きます</span>
		</span>
	</a>
</h1>
<div class="hidden_item form_group off" style="display: none;">
<section>
	<h1 class="skip">検索</h1>
	<form class="search" action="" onsubmit="calendar_narrow_text();return false;">
		<div class="submit_button">
			<script>
			function calendar_narrow_text(clear){
				var str = $('#form_narrow_text').val();
				var cnt = 0;
				var msg = 'ヒットしませんでした';
				$(document).find('.detail_pop_wrapper div').each(function(){ // すべてのdetail_popを浚う
					var detail_id = $(this)[0].id;
					if($(this).text().indexOf(str)==-1){ // ヒットしない場合
						$('table.calendar .events .lcm_tooltip_parent').each(function(){
							if($(this).data('jslcmTooltipId') == detail_id){
								$(this).hide();
							}
						});
					} else { // ヒットする場合
						$('table.calendar .events .lcm_tooltip_parent').each(function(){
							if($(this).data('jslcmTooltipId') == detail_id){
								$(this).show();
							}
						});
					}
				});
				cnt = $('table.calendar').find('.lcm_tooltip_parent:visible').length;
				if(cnt) 	msg = cnt+'件ヒットしました';

				if(clear){
					$('#narrow_text_info').hide();
				}else{
					$('#narrow_text_info').find('p').text(msg).end().show();
				}
			}
			$(function(){
				$('#form_clear').on('click',function(){
					$('#form_narrow_text').val('');
					calendar_narrow_text(true);
				});
			});
			</script>
			<input type="text" title="カレンダ内検索" id="form_narrow_text" value="">
			<input type="submit" value="カレンダ内検索" class="button primary" id="form_submit" name="submit">
			<input type="button" value="解除" class="button" id="form_clear" name="clear">
		</div><!--/.submit_button-->
	</form>
</section>
</div>
<div id="narrow_text_info" class="flash_alert alert_success" style="display: none;">
	<a href="#msg" id="anchor_alert_success" class="skip tabindex_ctrl" tabindex="1">インフォメーション:メッセージが次の行にあります</a>
	<p id="msg" tabindex="-1"></p>
</div>

<div class="field_wrapper calendar">

<?php
	// 絞り込み等
	include("calendar_narrow.php");

	// 月選択
	$mon_select_html = '';
	$mon_select_html.= '<div class="select_period lcm_focus pagination" title="月を選択">';
	$mon_select_html.= htmlspecialchars_decode($prev_url);
	$mon_select_html.= htmlspecialchars_decode($next_url);
	$mon_select_html.= '<span class="select_num lcm_focus" title="各月を選択">';
	for($i = 1; $i <= 12; $i++):
		if ($i == $mon):
			$mon_select_html.= '<strong class="active">'.$i.'</strong>';
		else:
			$mon_select_html.= '<span><a href="'.\Uri::create( $kind_name . '/calendar/' . $year . '/' . $i).$cond.'">'.$i.'</a></span>';
		endif;
	endfor;
	$mon_select_html.= '</span>';
	$mon_select_html.= htmlspecialchars_decode($prev_year_url);
	$mon_select_html.= htmlspecialchars_decode($next_year_url);
	$mon_select_html.= '</div>';

	// 上段表示分
	echo $mon_select_html;
?>

<h2 class="skip">カレンダ</h2>
<table class="calendar month lcm_focus" title="カレンダ">
	<thead>
		<tr>
			<th class="week1"><span>月曜日</span></th>
			<th class="week2"><span>火曜日</span></th>
			<th class="week3"><span>水曜日</span></th>
			<th class="week4"><span>木曜日</span></th>
			<th class="week5"><span>金曜日</span></th>
			<th class="week6"><span>土曜日</span></th>
			<th class="week0"><span>日曜日</span></th>
		</tr>
	</thead>
<?php $detail_pop_array = array(); ?>
<?php foreach($schedule_data as $v): ?>
	<?php if ($v['week'] == 1) { print '<tr>'; } ?>
	<?php $class_str = 'week'.$v['week'];
		// 祝日対応
		if (isset($v['is_holiday']) && $v['is_holiday']) { $class_str = "week_holiday"; }
		if(isset($v['day'])):
			$class_str.= $currentday == $v['day'] ? ' today' : '';
			//each_date_title_strはフォーカス移動時読み上げ文字列
			//date_str, each_date_title_skip は枠内フォーカス時タイトル読み上げ文字列
			$each_date_title_str = $currentday == $v['day'] ? '今日 ' : '';
			$each_date_title_str .= $v['day'].'日'.$week_name[$v['week']].'曜日 ';
			$each_date_title_str.=  (isset($v['is_holiday']) && $v['is_holiday']) ? '祝日 ' : '';//祝日の名前(振り替え休日のことも考えたほうがよいのかも)。
			$each_date_title_str .= (count($v['data']) > 0) ? count($v['data']) . '件の登録' : '登録なし';
			$date_str = $v['day'] < 10 ? '&nbsp;'.$v['day'] : $v['day'];
			$each_date_title_skip = $week_name[$v['week']] . '曜日';
			$each_date_title_skip.= (isset($v['is_holiday']) && $v['is_holiday']) ? '祝日</span><span class="holiday_name">'.'祝日'.'</span><span class="skip">' : '';//祝日の名前。
			$each_date_title_skip.= (count($v['data']) > 0) ? count($v['data']) . '件の登録' : '登録なし';
		else:
			$class_str.= '';
		endif;
	 ?>
	<td class="<?php echo $class_str ?>">
		<?php if (isset($v['day'])):?>
		<div class="each_date lcm_focus" title="<?php echo $each_date_title_str ?>">
			<a href="<?php echo \Uri::create($kind_name . "/calendar/" . sprintf("%04d/%02d/%02d/", $year, $mon, $v['day'])) ?>" class="title">
				<span class="date_str"><?php echo $date_str ?>日</span>
				<span class="skip"><?php echo $each_date_title_skip ?></span>
			</a>
			<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $v['day']))); ?>" class="add_new" title="新規追加"><span class="skip">新規追加</span></a>
			<div class="events">
			<?php foreach ($v['data'] as $v2):
				$detail_pop_data = $v2;
				$detail_pop_array[] = $v2;

				$eventtitle_icon = '';
				$eventtitle_skip = '<span class="skip">';

				//詳細区分
				foreach($detail_kbs as $key => $value):
					if($v2[$key]):
					$eventtitle_icon.= '<span class="text_icon schedule '.$key.'"></span>';
					$eventtitle_skip.= ' '.$value.' ';
					endif;
				endforeach;

				//外部表示(施設予約)
				if(\Request::active()->controller !== "\Controller_Scdl"):
					$eventtitle_icon.= $v2['public_display']==2 ? '<span class="text_icon reserve public"></span>' : '';
					$eventtitle_skip.= $v2['public_display']==2 ? '外部表示 ' : '';
				endif;

				//繰り返し区分
				$eventtitle_icon.= $v2['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$v2['repeat_kb'].'"></span>' : '';
				$eventtitle_skip.= $v2['repeat_kb'] != 0 ? $repeat_kbs[$v2['repeat_kb']].' ' : '';
				// 時間
				$event_time_display_data = $model_name::make_target_day_info($v2);
				$event_time_display = (\Session::get('scdl_display_time') == "1") ? "inline" : "none";
				$event_time = '<span class="scdl_time sr_add bracket" style="display:' . $event_time_display . '">'. $event_time_display_data['start_time'] . '<span class="sr_replace to"><span>から</span></span>' . $event_time_display_data['end_time'] . '</span>';
				//代理登録
				if(($v2->user_id && $v2->updater_id)&&($v2->user_id != $v2->updater_id)):
					$eventtitle_icon.= '<span class="text_icon schedule dairi"></span>';
					$eventtitle_skip.= '代理登録 ';
				endif;
/*
				//詳細区分
				foreach($detail_kbs as $key => $value):
					if($v2[$key]):
					$eventtitle_icon.= '<span class="text_icon schedule '.$key.'"></span>';
					$eventtitle_skip.= ' '.$value;
					endif;
				endforeach;
				//重要度
				$importance_v = $model_name::value2index('title_importance_kb', html_entity_decode($v2['title_importance_kb']));
				$eventtitle_icon.= '<span class="icon" style="width: 1em;"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$importance_v.'.png" alt=""></span>';
				$eventtitle_skip.= ' '.$importance_kbs[$importance_v];
				$eventtitle_skip.= '</span>';
*/
				echo '<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop'.$v2->scdlid.$v2->target_year.$v2->target_mon.$v2->target_day.'">';

				echo '<a href="' . \Uri::create($kind_name . "/viewdetail/" . $v2['scdlid'] . sprintf("/%d/%d/%d", $v2['target_year'], $v2['target_mon'], $v2['target_day'])) . '">';

				echo $eventtitle_icon.$event_time.htmlspecialchars($v2['title_text']).$eventtitle_skip;
				echo '</a>';

				echo '</p>';
			endforeach;?>
			</div><!-- /.events -->
		</div><!-- /.each_date -->
		<?php endif; ?>
	</td>
	<?php if ($v['week'] == 0) { print '</tr>'; } ?>
<?php endforeach; ?>
</table>
<?php include("inc_legend.php"); //カレンダ凡例 ?>
</div><!-- /.field_wrapper -->
<?php
if($detail_pop_array):
	echo '<div style="display: none;"><section class="detail_pop_wrapper">';
	echo '<h1>予定詳細一覧</h1>';
	foreach($detail_pop_array as $v):
		$detail_pop_data = $v;
		include("detail_pop.php");
	endforeach;
	echo '</section></div>';
endif;

	// 下段表示分
	echo '<div style="text-align: center; margin: 25px 0 15px;">';
	echo $mon_select_html;
	echo '</div>';
?>
