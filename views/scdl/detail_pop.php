<?php if (isset($detail_pop_data->title_text)) { ?>
<div id="pop<?php print $detail_pop_data->scdlid.$detail_pop_data->target_year.$detail_pop_data->target_mon.$detail_pop_data->target_day; ?>" aria-hidden="true">
<table class="tbl2">
	<thead>
	<tr>
		<th style="text-align: left;" colspan="2">
		<?php
			//外部表示(施設予約)
			if(\Request::active()->controller !== "\Controller_Scdl"):
				echo $detail_pop_data['public_display']==2 ? '<span class="text_icon reserve public"><span class="skip">外部表示</span></span>' : '';
			endif;
		 ?>
			<?php print $detail_pop_data->title_text; ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<th style="width: 6em;">
			<?php
			// 指定なし
			//print '予定日時：</th><td>' . $model_name::display_target_day_info($detail_pop_data);
			print '予定日時：</th><td>' . htmlspecialchars_decode($detail_pop_data->display_target_day_info);
			?>
		</td>
	</tr>
	<?php if (!$detail_pop_data->private_kb) { ?>
	<tr>
		<th class="min">
			メッセージ：</th><td><?php print preg_replace("/(\r\n|\r|\n)/", "<br />", $detail_pop_data->message); ?>
		</td>
	</tr>
	<?php } ?>

	<?php if (count($detail_pop_data->user) && !$detail_pop_data->private_kb) { ?>
	<tr>
	<th>メンバー：</th>
	<td>
		<?php $members = [];
		foreach ($detail_pop_data->user as $row) {
			$members[] .= $row['display_name'];
		}
		echo '<span style="inline-block">'.implode(',</span> <span  style="display: inline-block">', $members).'</span>';
		?>
	</td>
	</tr>
	<?php } ?>

	<?php if (count($detail_pop_data->building) && !$detail_pop_data->private_kb) { ?>
	<tr>
	<th>対象施設：</th>
	<td>
		<?php $buildings = [];
		foreach ($detail_pop_data->building as $row) {
			$buildings[$row->item_id] = $row['item_name'];
		}
		ksort($buildings);
		echo '<span style="inline-block">'.implode(',</span> <span  style="display: inline-block">', $buildings).'</span>';
		?>

	</td>
	</tr>
	<?php } ?>
	<?php if (!$detail_pop_data->private_kb) { ?>
	<tr>
<?php /* ?>
		<th>
			予定の種類：</th><td><?php print $detail_pop_data->title_kb; ?>
		</td>
<?php */ ?>
	</tr>
	<?php if ($detail_pop_data->kind_flg == 2) { ?>
	<?php /* ?>
	<tr>
		<th class="min">
			施設使用目的：</th><td><?php print $detail_pop_data->purpose_kb . " " . $detail_pop_data->purpose_text; ?>
		</td>
	</tr>
	<?php */ ?>
	<?php if ($detail_pop_data->user_num > 0) { ?>
	<tr>
		<th class="min">
			施設使用人数：</th><td><?php print $detail_pop_data->user_num.'人'; ?>
		</td>
	</tr>
	<?php } ?>
	<?php } ?>
	<?php } ?>
	<tr>
		<th>
			登録者：</th><td><?php print $detail_pop_data->create_user['display_name']; ?>
		</td>
	</tr>
	</tbody>
</table>
<span class="skip">

<?php //ページ内検索用文字列
	//仮登録


//	if() echo '仮登録';

	//代理登録
	if(($detail_pop_data->user_id && $detail_pop_data->updater_id)&&($detail_pop_data->user_id != $detail_pop_data->updater_id)) echo '代理登録 ';
?>
</span>
</div>
<?php } ?>
