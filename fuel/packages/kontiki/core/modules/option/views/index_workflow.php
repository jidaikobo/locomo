<?php echo $include_tpl('inc_header.php'); ?>

<h2>ルート設定</h2>

<table class="tbl">
<?php foreach($items as $item): ?>
	<tr>
		<td class="id">
			<?php if($item['is_current']): ?>
				<a href="<?php echo \Uri::create($item['controller'].'/view/'.$item['controller_id']); ?>" class="button">
					<span class="skip"><?php echo $item['item']->{$item['primary_name_field']} ?>を</span>確認
				</a>
			<?php else: ?>
				進行中
			<?php endif; ?>
		</td>
		<td><?php echo $item['item']->{$item['primary_name_field']} ?></td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $include_tpl('inc_footer.php'); ?>
