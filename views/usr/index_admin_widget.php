<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus" title="ユーザ一覧">
	<thead>
		<tr>
			<th>ID</th>
			<th><?php echo \Model_Usr::property('username')['label']; ?></th>
		</tr>
	</thead>
	<tbody>
	
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td class="minimum num"><?php echo $item->id; ?></td>
			<td style="min-width: 6em;" ><div class="col_scrollable"tabindex="-1">
			<?php 
					echo Html::anchor('usr/view'.'/'.$item->id, $item->display_name, array('class' => 'view'));
			?>
			</div></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>ユーザが存在しません。</p>

<?php endif; ?>
