<?php echo render('inc_admin_header'); ?>
<h2>項目一覧<?php echo ($hit != 0) ? '（全'.$hit.'件）' : ''; ?></h2>
<p><?php echo \Pagination::sort_info('\User\Model_User'); ?></p>
<?php 
	//index menu
	$actions = \Actionset::get_menu($controller, 'index', null, $get_authed_url = true);
	echo \Actionset::generate_menu_html($actions, array('class'=>'holizonal_list'));
?>
<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
###THEAD###
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
###TBODY###			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('user/view'.'/'.$item->id, '閲覧', array('class' => 'edit'));
					echo ' ';
					echo Html::anchor('user/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination; ?>
<?php else: ?>
<p>xxxが存在しません。</p>
<?php endif; ?>

<?php echo render('inc_admin_footer'); ?>
