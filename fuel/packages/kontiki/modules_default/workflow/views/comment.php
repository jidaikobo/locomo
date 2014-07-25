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
		echo Form::submit('submit', 'ワークフロー処理を進める', array('class' => 'button main'));
	?>
</p>

<?php echo \Form::close(); ?>
<p>
	<?php
//		echo Html::anchor('workflow/view/'.$workflow_id, '表示',array('class'=>'button'));
		echo Html::anchor('workflow/index_admin', '一覧に戻る',array('class'=>'button'));
	?>
</p>

<?php echo $include_tpl('inc_footer.php'); ?>
