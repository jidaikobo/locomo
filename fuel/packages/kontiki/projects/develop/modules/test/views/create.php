<?php echo $include_tpl('inc_admin_header.php'); ?>

<h2>New <span class='muted'>Test</span></h2>


<?php
echo \Form::open();

echo $form->field('title')->set_template('{label}{required}');
echo $form->field('title')->set_template('{error_msg}{field}');

echo $form->field('body')->set_template('{label}{required}');
echo $form->field('body')->set_template('{error_msg}{field}');
?>

<fieldset>
	<legend><?php echo \Form::label('編集履歴用メモ', 'revision_comment'); ?></legend>
	<?php echo \Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;')); ?>
</fieldset>

<div class="button_group">
		<?php
		if(@$is_revision):
			echo Html::anchor('user/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
			echo Html::anchor('user/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
		else:
			echo \Form::hidden($token_key, $token);
			echo Html::anchor('user', '一覧に戻る',array('class'=>'button'));
			echo \Form::submit('submit', '保存', array('class' => 'button primary'));
		endif;
		?>
</div>

<?php
echo \Form::close();
?>

<?php
//echo $form;
//echo render('_form');
?>

<p><?php echo Html::anchor('test', 'Back'); ?></p>

<?php echo $include_tpl('inc_admin_footer.php'); ?>
