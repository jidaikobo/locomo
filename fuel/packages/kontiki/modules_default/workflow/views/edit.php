<?php echo $include_tpl('inc_header.php'); ?>

<?php echo render('_form'); ?>

<p>
	<?php
		echo Html::anchor('workflow/view/'.$item->id, '表示',array('class'=>'button'));
		echo Html::anchor('workflow/index_admin', '一覧に戻る',array('class'=>'button'));
	?>
</p>

<?php echo $include_tpl('inc_footer.php'); ?>
