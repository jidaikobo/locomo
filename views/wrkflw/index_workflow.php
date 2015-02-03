<table class="tbl datatable">
<thead>
	<tr>
		<th>表題</th>
		<th>申請日</th>
		<?php if ( ! \Request::is_hmvc()): ?>
		<th>最後の進捗</th>
		<th class="ctrl">進捗</th>
		<?php endif; ?>
		<th>操作</th>
	</tr>
</thead>
<!--関係のある項目を先に表示。-->
<?php foreach($related as $item):?>
	<tr>
		<td><div class="col_scrollable" tabindex="-1"><?php echo $item->{$subject_field} ?></div></td>
		<td class="ctrl"><?php echo $item->workflow_apply_date ? date('Y-m-d', strtotime($item->workflow_apply_date)) : '-' ?></td>
		<?php if ( ! \Request::is_hmvc()): ?>
		<td class="ctrl"><?php echo $item->latest_action_date ? date('Y-m-d', strtotime($item->latest_action_date)) : '-' ?></td>
		<td class="ctrl"><?php echo $item->workflow_step_status ?: '-' ?></td>
		<?php endif; ?>
		<td class="ctrl">
		<?php if(in_array(\Auth::get('id'), $item->workflow_users) || in_array(\Auth::get('id'),[-1,-2])): ?>
			<?php if ($item->workflow_status == 'before_progress') : ?>
				<a href="<?php echo \Uri::create($controller_uri.'/route/'.$item->{$pk}); ?>">
					<span class="skip"><?php echo $item->{$subject_field} ?>を</span>ルート設定
				</a>
			<?php elseif ($item->creator_id == \Auth::get('id') && $item->workflow_status != 'in_progress') : ?>
				<a href="<?php echo \Uri::create($controller_uri.'/edit/'.$item->{$pk}); ?>">
					<span class="skip"><?php echo $item->{$subject_field} ?>を</span>編集
				</a>
			<?php else : ?>
				<?php if ($model::find($item->{$pk})): ?>
					<a href="<?php echo \Uri::create($controller_uri.'/view/'.$item->{$pk}); ?>">
						<span class="skip"><?php echo $item->{$subject_field} ?>を</span>確認
					</a>
				<?php else : ?>
					進行中
				<?php endif; ?>
			<?php endif; ?>
		<?php else: ?>
			進行中
		<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>

</table>
