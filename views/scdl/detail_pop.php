


<?php if (isset($detail_pop_data->title_text)) { ?>
<div id="pop<?php print $detail_pop_data->scdlid; ?>" aria-hidden="true" style="display:none;">
<table class="tbl2">
	<tr>
		<th>
			<?php
			
			// 表示加工
			$detail_pop_data->display_startdate = date('Y年n月j日', strtotime($detail_pop_data->start_date . " " . $detail_pop_data->start_time));
			$detail_pop_data->display_enddate = date('Y年n月j日', strtotime($detail_pop_data->end_date . " " . $detail_pop_data->end_time));
			$detail_pop_data->display_starttime = preg_replace("/時0/", "時", date('G時i分', strtotime($detail_pop_data->start_date . " " . $detail_pop_data->start_time)));
			$detail_pop_data->display_endtime = preg_replace("/時0/", "時", date('G時i分', strtotime($detail_pop_data->end_date . " " . $detail_pop_data->end_time)));

			if ($detail_pop_data->repeat_kb == 0) {
				// 毎年
				print '開催日時：</th><td>' . $detail_pop_data->display_startdate . ' ' . $detail_pop_data->display_starttime . "〜<br>" . $detail_pop_data->display_enddate . " " . $detail_pop_data->display_endtime;
			} else {
				print '開催日時：</th><td>' . $detail_pop_data->target_year . "年" . $detail_pop_data->target_mon . "月" . $detail_pop_data->target_day . "日";
				print '　' . $detail_pop_data->display_starttime . "〜" . $detail_pop_data->display_endtime;
			}
			?>
		</td>
	</tr>
	<tr>
		<th>
			タイトル：</th><td><?php print $detail_pop_data->title_text; ?>
		</td>
	</tr>

	<?php if ($detail_pop_data->kind_flg == 1) { ?>
	<tr>
		<th>
			メッセージ：</th><td><?php print mb_substr($detail_pop_data->message, 0, 20); ?>
		</td>
	</tr>
	<?php } ?>

	<?php if (count($detail_pop_data->user)) { ?>
	<tr>
	<th>メンバー</th>
	<td>
		<?php foreach ($detail_pop_data->user as $row) {
			print $row['display_name'] . " ";
		}
		?>
	</td>
	</tr>
	<?php } ?>

	<?php if (count($detail_pop_data->building)) { ?>
	<tr>
	<th>対象施設</th>
	<td>
		<?php foreach ($detail_pop_data->building as $row) {
			print $row['item_name'] . " ";
		}
		?>
	</td>
	</tr>
	<?php } ?>

	<tr>
		<th>
			予定の種類：</th><td><?php print $detail_pop_data->title_kb; ?>
		</td>
	</tr>
	<?php if ($detail_pop_data->kind_flg == 2) { ?>
	<tr>
		<th>
			施設使用目的：</th><td><?php print $detail_pop_data->purpose_kb . " " . $detail_pop_data->purpose_text; ?>
		</td>
	</tr>
	<tr>
		<th>
			施設使用人数：</th><td><?php print $detail_pop_data->user_num; ?>
		</td>
	</tr>
	<?php } ?>

	<tr>
		<th>
			登録者：</th><td><?php print @$detail_pop_data->create_user->display_name; ?>
		</td>
	</tr>
</table>

</div>
<?php } ?>



