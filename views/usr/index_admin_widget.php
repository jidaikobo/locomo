<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus" title="ユーザ一覧">
	<thead>
		<tr>
			<th>ID</th>
			<th><?php echo \Model_Usr::property('username')['label']; ?></th>
<?php if ($widget_size >= 2): ?>
			<th><?php echo \Model_Usr::property('display_name')['label']; ?></th>
<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td class="minimum num"><?php echo $item->id; ?></td>
			<td><?php echo Html::anchor('usr/view'.'/'.$item->id, $item->username, array('class' => 'view'));?></td>
<?php if ($widget_size >= 2): ?>
			<td><?php echo $item->display_name; ?></td>
<?php endif; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>ユーザが存在しません。</p>

<?php endif; ?>
