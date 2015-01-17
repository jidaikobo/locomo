<?php echo \Form::open(); ?>

<div class="form_group">
<fieldset>
	<legend>編集</legend>
	<table class="tbl">
		<?php /* echo $form; */ ?>
	<tr>
		<th class="ctrl"><?php echo $form->field('ctrl')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('ctrl')->set_template('{error_msg}{field}'); ?></td>
	</tr>
	
	<tr>
		<th><?php echo $form->field('body')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('body')->set_template('{error_msg}{field}'); ?></td>
	</tr>
	</table>
</fieldset>

<?php echo render(LOCOMOPATH.'views/revision/inc_revision_memo.php'); ?>

<div class="submit_button">
	<?php
		echo $form->field('title')->set_template('{field}');
//		echo \Form::hidden('action', $action);
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	?>
</div>

</div>

<?php echo \Form::close(); ?>
