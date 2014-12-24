<?php
// このテンプレート、見ないで小長井くん。
?>
<table class="tbl datatable">
<thead>
	<tr>
		<th>表題</th>
		<th>申請日</th>
		<th>最後の進捗</th>
		<th class="ctrl">進捗</th>
		<th>操作</th>
	</tr>
</thead>
<!--関係のある項目を先に表示。-->
<?php foreach($related as $item):?>
	<tr>
		<td><div class="col_scrollable" tabindex="-1"><?php echo $item->{$subject_field} ?></div></td>
		<td class="ctrl"><?php echo $item->workflow_apply_date ? date('Y-m-d', strtotime($item->workflow_apply_date)) : '-' ?></td>
		<td class="ctrl"><?php echo $item->latest_action_date ? date('Y-m-d', strtotime($item->latest_action_date)) : '-' ?></td>
		<td class="ctrl"><?php echo $item->workflow_step_status ?: '-' ?></td>
		<td class="ctrl">
			<a href="<?php echo \Uri::create($controller_uri.'/view/'.$item->{$pk}); ?>">
				<span class="skip"><?php echo $item->{$subject_field} ?>を</span>確認
			</a>
		</td>
	</tr>
<?php endforeach; ?>
<!--こちらは後に表示-->
<?php foreach($not_related as $item): ?>
	<tr>
		<td><div class="col_scrollable" tabindex="-1"><?php echo $item->{$subject_field} ?></div></td>
		<td class="ctrl"><?php echo $item->workflow_apply_date ? date('Y-m-d', strtotime($item->workflow_apply_date)) : '-' ?></td>
		<td class="ctrl"><?php echo $item->latest_action_date ? date('Y-m-d', strtotime($item->latest_action_date)) : '-' ?></td>
		<td class="ctrl"><?php echo $item->workflow_step_status ?: '-' ?></td>
		<td class="ctrl">
		<?php if(empty($item->workflow_users) && @$item->creator_id == \Auth::get('id')): ?>
			<a href="<?php echo \Uri::create($controller_uri.'/edit/'.$item->{$pk}); ?>">
				<span class="skip"><?php echo $item->{$subject_field} ?>を</span>編集
			</a>
		<?php else: ?>
			進行中
		<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

