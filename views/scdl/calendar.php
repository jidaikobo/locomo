
<?php print htmlspecialchars_decode($display_month); ?> / 
<?php print htmlspecialchars_decode($display_week); ?>


<h1><?php print $year; ?>年<?php print (int)$mon; ?>月</h1>

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
	<?php print htmlspecialchars_decode($prev_url); ?> 
	<?php print htmlspecialchars_decode($next_url); ?>
	<?php 
		for($i = 1; $i <= 12; $i++) {
			if ($i == $mon) {
	?>
		<span><?php print $i; ?></span>
	<?php
	} else {
	?>
	<span><a href="<?php print \Uri::create( $kind_name . '/calendar/' . $year . '/' . $i); ?>"><?php print $i; ?></a></span>
	<?php
	}
	?>

	<?php } ?>
	<?php print htmlspecialchars_decode($prev_year_url); ?> 
	<?php print htmlspecialchars_decode($next_year_url); ?>

</div>
<table class="calendar month lcm_focus">
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
<?php foreach($schedule_data as $v) { ?>
	<?php if ($v['week'] == 1) { print '<tr>'; } ?>
	<td class="week<?php print $v['week']; ?>">
		<div class="each_date">
		
		<?php if (isset($v['day'])) { ?>
			<a href="<?php echo \Uri::create("scdl/calendar/" . sprintf("%04d/%02d/%02d/", $year, $mon, $v['day'])) ?>" class="title">
				<span class="date_str"><?php print $v['day']; ?>日</span>
				<span class="skip"><?php print $week_name[$v['week']] . '曜日'; ?> <?php if (count($v['data']) > 0) { print count($v['data']) . '件の登録';} else { print '登録なし'; } ?></span>
			</a>
			<div>
			<?php print count($v['data']); ?>件
			</div>
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
	<?php if ($v['week'] == 0) { print '</tr>'; } ?>
<?php } ?>
</table>



