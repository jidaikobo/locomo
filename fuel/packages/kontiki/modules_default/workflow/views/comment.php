<?php echo $include_tpl('inc_header.php'); ?>

<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<h2>ワークフロー名</h2>

<fieldset>
	<legend>コメント</legend>

	<div class="form-group">
		<?php echo \Form::label('コメント', "comment", array('class'=>'control-label')); ?>
		<?php echo \Form::textarea("comment", '', array('class' => 'col-md-4 form-control')); ?>
	</div>
</fieldset>

<p>
	<?php
		echo Html::anchor($controller.'/view/'.$id, '戻る',array('class'=>'button'));
//		echo Html::anchor('workflow/index_admin', '戻る',array('class'=>'button'));
		echo Form::submit('submit', $button, array('class' => 'button main'));
	?>
</p>

<?php echo \Form::close(); ?>

<?php echo $include_tpl('inc_footer.php'); ?>
