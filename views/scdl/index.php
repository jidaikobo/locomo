
<h2>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h2>
<?php if ($items): ?>
<table class="tbl">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th>繰り返し区分</th>
			<th>繰り返し終了日時</th>
			<th>開始日時</th>
			<th>終了日時</th>
			<th>繰り返し曜日</th>
			<th>タイトル</th>
			<th>タイトル（重要度）</th>
			<th>タイトル（区分）</th>
			<th>詳細設定</th>
			<th>メッセージ</th>
			<th>表示するグループフラグ</th>
			<th>グループ指定</th>
			<th>施設使用目的区分</th>
			<th>施設使用目的テキスト</th>
			<th>施設利用人数</th>
			<th>作成者</th>
			<th>作成日時</th>
			<th>更新日時</th>
			<th>削除日</th>
			<th>可視属性</th>

		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><?php echo $item->repeat_kb; ?></td>
	<td><?php echo $item->start_date; ?></td>
	<td><?php echo $item->end_date; ?></td>
	<td><?php echo $item->week_kb; ?></td>
	<td><?php echo $item->title_text; ?></td>
	<td><?php echo $item->title_importance_kb; ?></td>
	<td><?php echo $item->title_kb; ?></td>
	<td><?php echo $item->detail_kb; ?></td>
	<td><?php echo $item->message; ?></td>
	<td><?php echo $item->group_kb; ?></td>
	<td><?php echo $item->group_detail; ?></td>
	<td><?php echo $item->purpose_kb; ?></td>
	<td><?php echo $item->purpose_text; ?></td>
	<td><?php echo $item->user_num; ?></td>
	<td><?php echo $item->user_id; ?></td>
	<td><?php echo $item->created_at; ?></td>
	<td><?php echo $item->updated_at; ?></td>
	<td><?php echo $item->deleted_at; ?></td>
	<td><?php echo $item->is_visible ? 'Yes' : 'No'; ?></td>

		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>scheduleが存在しません。</p>

<?php endif; ?>

