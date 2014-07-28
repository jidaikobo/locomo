<?php echo $include_tpl('inc_header.php'); ?>

<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<h2>ワークフロー名</h2>

<fieldset>
	<legend><?php echo \Form::label('コメント', "comment", array('class'=>'control-label')); ?></legend>

	<div class="form-group">
		<?php echo \Form::textarea("comment", '', array('class' => 'col-md-4 form-control')); ?>
	</div>
</fieldset>

<?php if(@$target_steps): ?>
<fieldset>
	<legend>差戻し先</legend>
	<div class="form-group">
		<?php echo \Form::select("target_step", '', $target_steps, array('class' => 'col-md-4 form-control')); ?>
	</div>
</fieldset>
<?php endif; ?>

<p>
	<?php
		echo Html::anchor($controller.'/view/'.$id, '戻る',array('class'=>'button'));
//		echo Html::anchor('workflow/index_admin', '戻る',array('class'=>'button'));
		echo Form::submit('submit', $button, array('class' => 'button main'));
	?>
</p>

<?php echo \Form::close(); ?>

<?php echo $include_tpl('inc_footer.php'); ?>
