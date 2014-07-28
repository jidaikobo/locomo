<?php echo $include_tpl('inc_admin_header.php'); ?>

<h2>Editing <span class='muted'>XXX</span></h2>

<?php echo render('_form'); ?>

<p>
	<?php
	if(@$is_revision):
		echo Html::anchor('xxx/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
		echo Html::anchor('xxx/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
	else:
		//コントローラがリビジョンをサポートしていない場合、この箇所だけで十分です。
		echo Html::anchor('xxx/view/'.$item->id, '表示',array('class'=>'button'));
		echo Html::anchor('xxx', '一覧に戻る',array('class'=>'button'));
	endif;
	?>
</p>

<?php echo $include_tpl('inc_admin_footer.php'); ?>
