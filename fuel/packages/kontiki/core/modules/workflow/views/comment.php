<?php echo $include_tpl('inc_header.php'); ?>

<?php echo \Form::open(); ?>

<h2>ワークフロー名</h2>

<fieldset>
	<legend><?php echo \Form::label('コメント', "comment"); ?></legend>

	<div class="form-group">
		<?php echo \Form::textarea("comment", ''); ?>
	</div>
</fieldset>

<?php if(@$target_steps): ?>
<fieldset>
	<legend>差戻し先</legend>
	<div class="form-group">
		<?php echo \Form::select("target_step", '', $target_steps); ?>
	</div>
</fieldset>
<?php endif; ?>

<p>
	<?php
		echo Html::anchor($controller.'/view/'.$id, '戻る',array('class'=>'button'));
//		echo Html::anchor('workflow/index_admin', '戻る',array('class'=>'button'));
		echo \Form::submit('submit', $button, array('class' => 'button primary'));
	?>
</p>

<?php echo \Form::close(); ?>

<?php echo $include_tpl('inc_footer.php'); ?>
