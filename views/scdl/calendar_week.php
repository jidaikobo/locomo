<?php print htmlspecialchars_decode($display_month); ?> / 
<?php print htmlspecialchars_decode($display_week); ?>

<h1>calendar一週間</h1>

<div>
月表示
</div>

<p>
<?php echo $year; ?>年　<?php echo (int)$mon; ?>月
</p>




<style>
.table table, .table td, .table th{
	border: 1px solid black;
	vertical-align: top;
}
.week0 {
	background-color: pink;
}
.week6 {
	background-color: lightblue;
}
</style>

<div class="lcm_focus">
	<?php print htmlspecialchars_decode($prev_url); ?> / 
	<?php print htmlspecialchars_decode($next_url); ?>
</div>
<table class="calendar week lcm_focus">
	<thead>
		<tr>
			<th class="week1">月曜日</th>
			<th class="week2">火曜日</th>
			<th class="week3">水曜日</th>
			<th class="week4">木曜日</th>
			<th class="week5">金曜日</th>
			<th class="week6">土曜日</th>
			<th class="week0">日曜日</th>
		</tr>
	</thead>
<tr>
<?php foreach($schedule_data as $v) {?>
	<td class="week<?php print $v['week']; ?>">
		<div class="each_date">
		
		<?php if (isset($v['day'])) { ?>
			<a href="<?php echo \Uri::create(Config::get('base_url') . '/scdl/calendar/' . sprintf("%04d/%02d/%02d/", $year, $mon, $v['day'])); ?>">
				<span class="date_str"><?php print (int)$v['day']; ?>日</span>
				<span class="skip"><?php print $week_name[$v['week']] . '曜日'; ?> <?php if (count($v['data']) > 0) { print count($v['data']) . '件の登録';} else { print '登録なし'; } ?></span>
			</a>
			<a href="<?php echo \Uri::create("scdl/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $v['day']))); ?>" />新規追加</a>
			
			<div class="events">
			<?php foreach ($v['data'] as $v2) {
				$detail_pop_data = $v2;
				?>
				<p><?php print htmlspecialchars_decode($v2['link_detail']); ?></p>
			<?php include("detail_pop.php"); ?>
			<?php } ?>
			</div>
		<?php } ?>
		</div>
	</td>

<?php } ?>
</tr>

</table>


