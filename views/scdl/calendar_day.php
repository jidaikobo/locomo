<?php 
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
?>

<?php if( ! $is_hmvc): ?>
<h1><?php echo $year; ?>年 <?php echo (int)$mon; ?>月 <?php echo (int)$day; ?>日 一日詳細カレンダ</h1>
<?php include("calendar_narrow.php"); ?>
<div class="field_wrapper calendar_detail">
	<div class="select_period lcm_focus" title="表示する日を変更">
		<?php print htmlspecialchars_decode($prev_url); ?> / 
		<?php print htmlspecialchars_decode($next_url); ?> / 
		<input type="text" name="move_date" value="<?php print sprintf("%04d-%02d-%02d", $year, $mon, $day);?>" style="width: 8em;" size="13" class="date" id="move_date" title="表示年月日" /><input class="button small" id="btn_move_date" type="button" value="指定の日を表示" onclick="move_date()" />
	</div>
	<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $day))); ?>" />新規追加</a>	
	<h2 class="skip">タイムテーブル グラフ</h2>
<?php if (isset($schedule_data['member_list']) && count($schedule_data['member_list']) > 0): ?>
	<table id="schedule_graph" class="table schedule_day graph tbl lcm_focus" title="タイムテーブル グラフ">
	<tbody>
	<?php foreach ($schedule_data['member_list'] as $row): ?>
			<tr>
			<th class="name" rowspan="<?php print count($row); ?>">
				<?php echo '<a href="'.\Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $day)).'&amp;member_id='.$row['model']->id).'">'.$row['model']->display_name.'</a>'; ?>
			</th>
				<?php foreach($schedule_data['schedules_list'] as $v) {?>
				<td colspan="4" class="time h<?php print $v['hour']; ?>">
					<?php print $v['hour']; ?>
				</td>
				<?php } ?>
			</tr>
	
				<?php
				foreach ($row as $member_rowdata):
					if (!isset($member_rowdata['data'])) { continue; }
					?>
					<tr class="bar">
					<?php // 24時間をloop
						$empty_cells = 0;
						$filled_cells = 0;
						foreach($schedule_data['schedules_list'] as $v):
							foreach ($v['data'] as $detail_data):
								foreach ($member_rowdata['data'] as $member_detail):
									if ($member_detail->id == $detail_data->schedule_id):
										$tooltipid = $detail_data->schedule_id . $detail_data->target_year . $detail_data->target_mon . $detail_data->target_day;
										if ($detail_data->primary)   $filled_cells++;
										if ( ! $filled_cells && ! $detail_data->primary)   $empty_cells++;
	
										if ($detail_data->secondary) $filled_cells++;
										if ( ! $filled_cells && ! $detail_data->secondary) $empty_cells++;
	
										if ($detail_data->third)     $filled_cells++;
										if ( ! $filled_cells && ! $detail_data->third)     $empty_cells++;
	
										if ($detail_data->fourth)    $filled_cells++;
										if ( ! $filled_cells && ! $detail_data->fourth)    $empty_cells++;
									endif;
								endforeach;
							endforeach;
						endforeach;
						//24jikan
						// 描画
						//  空白セル
						for ($i = 1; $i <= $empty_cells; $i++)
						{
							echo '<td>&nbsp;</td>';
						}

						//  予定セル
						echo '<td colspan="'.$filled_cells.'" class="active lcm_tooltip_parent" data-jslcm-tooltip-id="pop'.$tooltipid.'">&nbsp;</td>';

						//  空白セル
						for ($i = 1, $cnt_rest = 24*4-$empty_cells-$filled_cells; $i <= $cnt_rest; $i++)
						{
							echo '<td>&nbsp;</td>';
						} ?>
					</tr>
				<?php endforeach;
			endforeach; ?>
		</tbody>
	</table>
<?php endif; 
	endif;//hmvcをとじる ?>

<?php if (isset($schedule_data['member_list']) && count($schedule_data['member_list']) > 0) { ?>
<?php if( ! $is_hmvc): ?>
	<h2 class="skip">タイムテーブル 一覧</h2>
<?php endif; ?>
	<table id="schedule_detail" class="tbl datatable schedule_day detail lcm_focus" title="タイムテーブル 一覧">
		<thead>
		<tr>
			<th class="time">
				予定時刻
			</th>
			<th class="members">
				メンバー
			</th>
			<th class="detail">
				内容
			</th>
			<?php if( ! $is_hmvc): ?>
			<th class="name">
				登録者
			</th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>

<?php foreach ($schedule_data['unique_schedule_data'] as $detaildata) { ?>
		<tr>
			<td class="time">
			<?php
				$detaildata->display_startdate = date('Y年n月j日', strtotime($detaildata->start_date . " " . $detaildata->start_time));
				$detaildata->display_enddate = date('Y年n月j日', strtotime($detaildata->end_date . " " . $detaildata->end_time));
				$detaildata->display_starttime = date('i', strtotime($detaildata->start_time))==0 ?
					date('G時', strtotime($detaildata->start_date . " " . $detaildata->start_time)) :
					preg_replace("/時0/", "時", date('G時i分', strtotime($detaildata->start_date . " " . $detaildata->start_time)));
				$detaildata->display_endtime = date('i', strtotime($detaildata->end_time))==0 ?
					date('G時', strtotime($detaildata->end_date . " " . $detaildata->end_time)) :
					preg_replace("/時0/", "時", date('G時i分', strtotime($detaildata->start_date . " " . $detaildata->end_time)));

				//"〜"は、前後にスペースを持ち、前方の文字列に含めて扱う。もしかすると適当なクラス、skipと疑似要素で〜(から)の読み上げが達成できるかもしれないが、あとで
				//表の上にもう一度日付を出したほうが迷わない？？
				
				if ($detaildata->repeat_kb == 0 && $detaildata->display_startdate != $detaildata->display_enddate) { //期間の予定。開始日終了日同日の場合は単日予定として除外
					//開始日終了日が異なる場合は期間 //開始日〜終了日 (何時〜何時）開始日と終了日を比較しつつ、表示振り分け
					if(date('Y', strtotime($detaildata->start_date)) == date('Y', strtotime($detaildata->end_date))) : //年が同じかどうか
						$detaildata->display_startdate = intval(date("Y")) == $year ? //現在と同年なら省略
							date('n月j日', strtotime($detaildata->start_date)) :
							date('Y年n月j日', strtotime($detaildata->start_date));
						$detaildata->display_enddate = date('n', strtotime($detaildata->start_date)) == date('n', strtotime($detaildata->end_date)) ? //同月なら省略
							date('j日', strtotime($detaildata->end_date)) :
							date('n月j日', strtotime($detaildata->end_date));
					endif;

					if($detaildata->allday_kb):
						echo '<span class="nowrap">'.$detaildata->display_startdate.' 〜</span> <span class="nowrap">'.$detaildata->display_enddate.'</span>';
					else:
						echo '<span class="nowrap">'.$detaildata->display_startdate.' '.$detaildata->display_starttime.' 〜</span> <span class="nowrap">'.$detaildata->display_enddate.' '.$detaildata->display_endtime.'</span>';
					endif;
				} else {
					if($detaildata->allday_kb){
						echo '<span class="nowrap">終日</span>';
					}else{
						echo '<span class="nowrap">'.$detaildata->display_starttime . ' 〜</span> <span class="nowrap">' . $detaildata->display_endtime.'</span>';
					}
				}
			?>
			</td>
			<th class="members">
				<span class="display_inline_block nowrap">
				<?php
				$members = array();
				foreach ($detaildata->user as $member_data):
					$members[] = $member_data->display_name;
				endforeach;
				print implode(',&nbsp;</span><span class="display_inline_block nowrap">', $members);
				?>
				</span>
			</th>
			<td class="detail">
			<?php
				$eventtitle_icon = '';
				$eventtitle_skip = '<span class="skip">';

/*
				//外部表示(施設予約)
				if(\Request::active()->controller !== "\Controller_Scdl"):
					$eventtitle_icon.= $detaildata['public_display']==2 ? '<span class="text_icon reserve public"></span>' : '';
					$eventtitle_skip.= $detaildata['public_display']==2 ? '外部に表示 ' : '';
				endif;
*/
				//詳細区分
				foreach($detail_kbs as $key => $value):
					if($detaildata[$key]):
						$eventtitle_icon.= '<span class="text_icon schedule '.$key.'"></span>';
						$eventtitle_skip.= ' '.$value;
					endif;
				endforeach;
				//繰り返し区分
				$eventtitle_icon.= $detaildata['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$detaildata['repeat_kb'].'"></span>' : '';
				$eventtitle_skip.= $detaildata['repeat_kb'] != 0 ? $repeat_kbs[$detaildata['repeat_kb']] : '';
				//代理登録
				if(($detaildata->user_id && $detaildata->updater_id)&&($detaildata->user_id != $detaildata->updater_id)):
					$eventtitle_icon.= '<span class="text_icon schedule dairi"></span>';
					$eventtitle_skip.= '代理登録 ';
				endif;
/*
				//重要度
				$importance_v = $model_name::value2index('title_importance_kb', html_entity_decode($detaildata['title_importance_kb']));
				$eventtitle_icon.= '<span class="icon" style="width: 1em;"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$importance_v.'.png" alt=""></span>';
				$eventtitle_skip.= ' '.$importance_kbs[$importance_v];
*/
				$eventtitle_skip.= '</span>';
			?>
				<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop<?php echo $detaildata->scdlid.$detaildata->target_year.$detaildata->target_mon.$detaildata->target_day ?>">
				<?php
				echo '<a href="'.\Uri::create($kind_name.'/viewdetail/').$detaildata->scdlid . sprintf("/%04d/%02d/%02d/", $detaildata->target_year, $detaildata->target_mon, $detaildata->target_day) . '">';
				echo $eventtitle_icon.htmlspecialchars($detaildata['title_text']).$eventtitle_skip;
				echo '</a>';
				echo '</p>';
				?>
			</td>
			<?php if( ! $is_hmvc): ?>
			<td class="name nowrap">
			<?php
				echo $detaildata->create_user['display_name'];
			?>
			</td>
			<?php endif; ?>
		</tr>
<?php } ?>
		</tbody>
	</table>
<?php include(LOCOMOPATH . "/views/scdl/inc_legend.php"); //カレンダ凡例 ?>
<?php }else{ ?>
<p tabindex="0">予定の登録がありません</p>
<?php } ?>
<?php if( ! $is_hmvc): ?>
</div><!-- /.field_wrapper -->
<?php endif; ?>
<?php
if($schedule_data['unique_schedule_data']):
	echo '<div style="display: none;"><section class="detail_pop_wrapper">';
	echo '<h1>予定詳細一覧</h1>';
	foreach($schedule_data['unique_schedule_data'] as $v):
		$detail_pop_data = $v;
		include("detail_pop.php");
	endforeach;
	echo '</section></div>';
endif;
;?>

<script>
function move_date(){
	var target = $("#move_date").val().replace(/-/g, "/");
	location.href='<?php echo \Uri::base().$kind_name.'/calendar/' ?>' + target;
}
</script>
