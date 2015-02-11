
<div class="narrow_user">
<?php include("calendar_narrow.php"); ?>
</div>

<?php print htmlspecialchars_decode($display_month); ?> / 
<?php print htmlspecialchars_decode($display_week); ?>

<h1>calendar一日詳細</h1>

<p>
<?php echo $year; ?>年　<?php echo (int)$mon; ?>月 <?php echo (int)$day; ?>日
</p>


<style>
.table table, .table td, .table th{
	border: 1px solid black;
	vertical-align: top;
}
.active {
	background-color: blue;
}
.graydown {
	background-color: #CCC;
}
.week0 {
	background-color: pink;
}
.week6 {
	background-color: lightblue;
}
</style>

移動<input type="text" name="move_date" value="<?php print sprintf("%04d-%02d-%02d", $year, $mon, $day); ?>" class="date" id="move_date" />

<hr />

<div class="lcm_focus">
	<?php print htmlspecialchars_decode($prev_url); ?> / 
	<?php print htmlspecialchars_decode($next_url); ?>
</div>
<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $day))); ?>" />新規追加</a>

<?php if (isset($schedule_data['member_list']) && count($schedule_data['member_list']) > 0) { ?>
<?php foreach ($schedule_data['member_list'] as $row) { ?>
	<table class="table">
		<tr>
			<td colspan="48">
				<?php print $row['model']->display_name; ?>
			</td>
		</tr>
		<tr>

			<?php foreach($schedule_data['schedules_list'] as $v) {?>
			<td colspan="2">
				<?php print $v['hour']; ?>
			</td>
			<?php } ?>
		</tr>
		<tr>
			<?php foreach($schedule_data['schedules_list'] as $v) {?>
			<?php $p_active = false; $s_active = false; ?>
			
				<?php foreach ($v['data'] as $detail_data) {
					foreach ($row['data'] as $member_detail) {
						if ($member_detail->id == $detail_data->schedule_id) {
							if (($detail_data->primary))
								$p_active = true;
							if (($detail_data->secondary))
								$s_active = true;
							//print ' class="active"';
						}
					}
				}
				?>
			<td class="<?php if ($p_active) { print "active"; } ?>">
			</td>
			<td class="<?php if ($s_active) { print "active"; } ?>">
			</td>
			<?php } ?>
		</tr>




		</tr>
	</table>
<?php } ?>
<?php } ?>


<?php if (isset($schedule_data['member_list']) && count($schedule_data['member_list']) > 0) { ?>
	<table class="table">
		<tr>
			<td>
				氏名
			</td>
			<td>
				内容
			</td>
		</tr>
<?php foreach ($schedule_data['member_list'] as $row) { ?>
		<tr>
			<td>
				<?php print $row['model']->display_name; ?>
			</td>
			<td>
				<?php foreach ($row['data'] as $detaildata) { ?>
				<div>

<?php
		$detaildata->display_startdate = date('Y年n月j日', strtotime($detaildata->start_date . " " . $detaildata->start_time));
		$detaildata->display_enddate = date('Y年n月j日', strtotime($detaildata->end_date . " " . $detaildata->end_time));
		$detaildata->display_starttime = preg_replace("/時0/", "時", date('G時i分', strtotime($detaildata->start_date . " " . $detaildata->start_time)));
		$detaildata->display_endtime = preg_replace("/時0/", "時", date('G時i分', strtotime($detaildata->end_date . " " . $detaildata->end_time)));

		if ($detaildata->repeat_kb == 0) {
			echo $detaildata->display_startdate . " " . $detaildata->display_starttime . "〜" . $detaildata->display_enddate . " " . $detaildata->display_endtime;
		} else {
			echo sprintf("%d年%d月%d日", $year, $mon, $day) . " " . $detaildata->display_starttime . "〜" . $detaildata->display_endtime;
			if ($detaildata->week_kb != "" && $detaildata->repeat_kb == 2) {
				echo "(";
				$week = array('日', '月', '火', '水', '木', '金', '土');
				if ($detaildata->week_index) {
					echo "第" . $detaildata->week_index;
				} else {
					echo "毎週";
				}
				echo $week[$detaildata->week_kb] . "曜日)";
			}
		}
?>
				</div>
				<?php } ?>

			</td>
		</tr>
<?php } ?>
	</table>
<?php } ?>

<script>
$("#move_date").change(function(event){
	var target = $("#move_date").val().replace(/-/g, "/");
	location.href='/<?php print $kind_name; ?>/calendar/' + target;
});
</script>

