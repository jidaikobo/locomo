<?php echo $include_tpl('inc_header.php'); ?>

<?php echo render('_form'); ?>

<p>
	<?php
	if(@$is_revision):
		echo Html::anchor('usergroup/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
		echo Html::anchor('usergroup/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
	else:
		echo Html::anchor('usergroup/view/'.$item->id, '表示',array('class'=>'button'));
		echo Html::anchor('usergroup', '一覧に戻る',array('class'=>'button'));
	endif;
	?>
</p>

<?php echo $include_tpl('inc_footer.php'); ?>
