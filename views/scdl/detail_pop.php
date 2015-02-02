


<?php if (isset($detail_pop_data->title_text)) { ?>
<div id="pop<?php print $detail_pop_data->scdlid; ?>" style="display:none;">
<table>
	<tr>
		<td>
			<?php
			if ($detail_pop_data->repeat_kb == 0) {
				// 毎年
				print '開催日時:' . $detail_pop_data->start_date . '　' . $detail_pop_data->start_time . "〜" . $detail_pop_data->end_date . " " . $detail_pop_data->end_time;
			} else {
				print '開催日時:' . $detail_pop_data->target_year . "年" . $detail_pop_data->target_mon . "月" . $detail_pop_data->target_day . "日";
				print '　' . $detail_pop_data->start_time . "〜" . $detail_pop_data->end_time;
			}
			?>
		</td>
	</tr>
	<tr>
		<td>
			タイトル：<?php print $detail_pop_data->title_text; ?>
		</td>
	</tr>

	<?php if ($detail_pop_data->kind_flg == 1) { ?>
	<tr>
		<td>
			メッセージ：<?php print $detail_pop_data->message; ?>
		</td>
	</tr>
	<tr>
		<td>
			予定の種類：<?php print $detail_pop_data->title_kb; ?>
		</td>
	</tr>
	<?php } ?>
	<?php if ($detail_pop_data->kind_flg == 2) { ?>
	<tr>
		<td>
			施設使用目的：<?php print $detail_pop_data->purpose_kb . " " . $detail_pop_data->purpose_text; ?>
		</td>
	</tr>
	<tr>
		<td>
			施設使用人数：<?php print $detail_pop_data->user_num; ?>
		</td>
	</tr>
	<?php } ?>

	<tr>
		<td>
			登録者：<?php print $detail_pop_data->create_user->username; ?>
		</td>
	</tr>
</table>

</div>
<?php } ?>



