<?php
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
	$currentday = (date("Y") == $year && date("n") == $mon ) ? date("j") : '';
?>

<?php if( ! $is_hmvc): ?>
<?php /* ?>
<h1><?php echo $year; ?>年 <?php echo (int)$mon; ?>月 週間カレンダ</h1>
<?php */ ?>
<h1 id="page_title" class="clearfix">
	<a href="javascript: void(0);" class="toggle_item disclosure nomarker tabindex_ctrl">
		<?php echo $year; ?>年 <?php echo (int)$mon; ?>月 週間カレンダ
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
				if( $('table.calendar tbody th')[0] ){
					$('table.calendar tbody tr').each(function(){
						$(this).show();
						if($(this).find('.lcm_tooltip_parent:visible').length){
							$(this).show();
						}else{
							$(this).hide();
						};
					});
				}
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
	include("calendar_narrow.php");
	// 週選択
	$week_select_html = '';
	$week_select_html.= '<div class="select_period" title="週の選択">';
	$week_select_html.= htmlspecialchars_decode($prev_url).' / ';
	$week_select_html.= htmlspecialchars_decode($next_url);
	$week_select_html.= '</div>';
	echo $week_select_html;
?>

<h2 class="skip">カレンダ</h2>
<?php endif; ?>
<table class="calendar week lcm_focus" title="カレンダ">
<?php if( ! $is_hmvc): ?>
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th class="week1"><span>月曜日</span></th>
		<th class="week2"><span>火曜日</span></th>
		<th class="week3"><span>水曜日</span></th>
		<th class="week4"><span>木曜日</span></th>
		<th class="week5"><span>金曜日</span></th>
		<th class="week6"><span>土曜日</span></th>
		<th class="week0"><span>日曜日</span></th>
	</tr>
	</thead>
<?php endif; ?>
	<tbody>
<?php
$detail_pop_array = array();
$bgids = array();
foreach($narrow_building_list as $v):
	$bgids[$v->item_id] = true;
endforeach;
$reserve_rows = array();
$reservenarrow_bid  = \Session::get('reservenarrow_bid');
$reservenarrow_bgid = \Session::get('reservenarrow_bgid');
$show_empty_row     = \Session::get('show_empty_row');

foreach($narrow_building_list as $row):
	//該当する場合は、予定リストの中に施設が含まれている。順番は$narrow_building_listに依るので、item_sortはみないでよい。
//	if(isset($schedule_data['building_list'][$row->item_id])):
	if (true):
		if(isset($schedule_data['building_list'][$row->item_id])):
			$each_row_data = $schedule_data['building_list'][$row->item_id];
		else:
			if ( ! $show_empty_row) continue;
			$each_row_data = array();
			$each_row_data['data'] = array();
			$each_row_data['model'] = (object) array(
				'id' => $row->id,
				'item_id' => $row->item_id,
				'item_name' => $row->item_name,
				'item_sort' => $row->item_id,
			);
		endif;
		if(($reservenarrow_bid == null && $reservenarrow_bgid == null) || // 絞り込み：なし
			 ($reservenarrow_bid != null && $reservenarrow_bid == $each_row_data['model']->item_id) || // 絞り込み：単一
			 ($reservenarrow_bid == null && $reservenarrow_bgid != null && isset($bgids[$each_row_data['model']->item_id]))): // 絞り込み：グループ
			$reserve_rows[$each_row_data['model']->item_sort] ="\t".'<tr class="lcm_focus" title="'.$each_row_data['model']->item_name .'">'."\n\t\t"
//			.'<th>'.$each_row_data['model']->item_name.'</th>'."\n\t\t";
			.'<th><a href="'.\Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d", $year, $mon)).'&amp;building_id='.$each_row_data['model']->id).'" class="">'.$each_row_data['model']->item_name.'</a></th>'."\n\t\t";

			foreach($schedule_data['schedules_list'] as $schedule_row):
				if (isset($schedule_row['is_holiday']) && $schedule_row['is_holiday']) {
					$class_str = "week_holiday";
				} else {
					$class_str = 'week'.$schedule_row['week'];
				}
				$class_str.= $currentday == $schedule_row['day'] ? ' today' : '';
				//each_date_title_strはフォーカス移動時読み上げ文字列
				//date_str, each_date_title_skip は枠内タイトル読み上げ文字列

				$each_date_title_str = $currentday == $schedule_row['day'] ? '今日 ' : '';
				$each_date_title_str.=  $schedule_row['day'].'日 '.$week_name[$schedule_row['week']].'曜日 ';
				$each_date_title_str.=  isset($schedule_row['is_holiday']) && $schedule_row['is_holiday'] ? '祝日 ' : '';//祝日の名前(振り替え休日のことも考えたほうがよいのかも)。

				$schedule_num = 0;
				foreach($schedule_row['data'] as $v1):
					foreach($each_row_data['data'] as $item_detail):
						if($item_detail['id'] == $v1['scdlid']) $schedule_num++;
					endforeach;
				endforeach;
				$each_date_title_str .= $schedule_num!=0 ? $schedule_num . '件の登録' : ' 登録なし';
				$date_str = $schedule_row['day'] < 10 ? '&nbsp;'.$schedule_row['day'] : $schedule_row['day'];
				$each_date_title_skip = $week_name[$schedule_row['week']] . '曜日';
				$each_date_title_skip.= isset($schedule_row['is_holiday']) && $schedule_row['is_holiday'] ? '祝日</span><span class="holiday_name">'.'祝日'.'</span><span class="skip">' : '';
				$each_date_title_skip.= $schedule_num!=0 ? ' '.$schedule_num . '件の登録' : ' 登録なし';
	 ?>
			<?php $reserve_rows[$each_row_data['model']->item_sort] .= '<td class="'.$class_str.'"><div class="each_date lcm_focus" title="'.$each_date_title_str.'">'."\n\t\t\t";
				$reserve_rows[$each_row_data['model']->item_sort] .= '<a href="'.\Uri::create(Config::get('base_url').$kind_name.'/calendar/'.sprintf('%04d/%02d/%02d/', $schedule_row['year'], $schedule_row['mon'], $schedule_row['day'])).'" class="title"><span class="date_str">'.$date_str.'日</span><span class="skip">'.$each_date_title_skip.'</span></a>'."\n\t\t\t";
				$reserve_rows[$each_row_data['model']->item_sort] .= '<a href="'.\Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $schedule_row['year'], $schedule_row['mon'], $schedule_row['day'])).'&amp;building_id='.$each_row_data['model']->id).'" class="add_new" title="新規追加"><span class="skip">新規追加</span></a>'."\n\t\t\t"
				.'<div class="events">';
				if (isset($schedule_row['day'])):
					foreach ($schedule_row['data'] as $v2):
						foreach ($each_row_data['data'] as $building_detail):
							if ($building_detail['id'] == $v2['scdlid']):
								$detail_pop_array[] = $v2;

								$eventtitle_icon = '';
								$eventtitle_skip = '<span class="skip">';

								//外部表示(施設予約)
								if(\Request::active()->controller !== "\Controller_Scdl"):
									$eventtitle_icon.= $v2['public_display']==2 ? '<span class="text_icon reserve public"></span>' : '';
									$eventtitle_skip.= $v2['public_display']==2 ? '外部表示 ' : '';
								endif;

								//詳細区分
								foreach($detail_kbs as $key => $value):
									if($v2[$key]):
										$eventtitle_icon.= '<span class="text_icon schedule '.$key.'"></span>';
										$eventtitle_skip.= $value.' ';
									endif;
								endforeach;
								//繰り返し区分
								$eventtitle_icon.= $v2['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$v2['repeat_kb'].'"></span>' : '';
								$eventtitle_skip.= $v2['repeat_kb'] != 0 ? $repeat_kbs[$v2['repeat_kb']].' ' : '';
								//代理登録
								if(($v2->user_id && $v2->creator_id)&&($v2->user_id != $v2->creator_id)):
									$eventtitle_icon.= '<span class="text_icon schedule dairi"></span>';
									$eventtitle_skip.= '代理登録 ';
								endif;
/*
								//重要度
								$importance_v = $model_name::value2index('title_importance_kb', html_entity_decode($v2['title_importance_kb']));
								$eventtitle_icon.= '<span class="icon" style="width: 1em;"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$importance_v.'.png" alt=""></span>';
								$eventtitle_skip.= ' '.$importance_kbs[$importance_v];
*/

								$eventtitle_skip.= '</span>';
								// 時間
								$event_time_display_data = $model_name::make_target_day_info($v2);
								$event_time_display = (\Session::get('scdl_display_time') == "1") ? "inline" : "none";
								$event_time = '<span class="scdl_time sr_add bracket" style="display:' . $event_time_display . '">'. $event_time_display_data['start_time'] . '<span class="sr_replace to"><span>から</span></span>' . $event_time_display_data['end_time'] . '</span>';

								$reserve_rows[$each_row_data['model']->item_sort] .= '<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop'.$v2->scdlid.$v2->target_year.$v2->target_mon.$v2->target_day.'">'."\n\t\t\t\t";
								$reserve_rows[$each_row_data['model']->item_sort] .= '<a href="'.\Uri::create($kind_name."/viewdetail/".$v2['scdlid'].sprintf("/%d/%d/%d", $v2['target_year'], $v2['target_mon'], $v2['target_day'])).'">'.$eventtitle_icon.$event_time.htmlspecialchars($v2['title_text']).$eventtitle_skip.'</a>'."\n\t\t\t".'</p>';
							endif;
						endforeach;
					endforeach;
				endif;
				$reserve_rows[$each_row_data['model']->item_sort] .= '</div><!-- /.events -->'."\n\t\t\t";
				$reserve_rows[$each_row_data['model']->item_sort] .= '</div><!-- /.each_date -->'."\n\t\t".'</td>';
			endforeach;
			$reserve_rows[$each_row_data['model']->item_sort] .= "\n\t</tr>\n"; ?>
	<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php ksort($reserve_rows) ;
	$row = "" ;?>
<?php foreach($reserve_rows as $row):
	echo $row;
endforeach; ?>
	</tbody>
</table>
<?php include("inc_legend.php"); //カレンダ凡例 ?>
<?php if( ! $is_hmvc): ?>
<?php
	// 週選択
	echo '<div class="lcmbar_bottom">';
	echo $week_select_html;
	echo '</div><!-- /.lcmbar_bottom -->';
?>
</div><!-- /.field_wrapper.calendar -->
<?php endif; ?>
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

?>
