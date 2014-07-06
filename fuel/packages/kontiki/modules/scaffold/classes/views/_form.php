<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<fieldset>

	<table class="tbl">
	###fields###
	</table>
	
	<div class="form-group">
		<label class='control-label'>&nbsp;</label>
		<?php echo Form::hidden($token_key, $token); ?>
		<?php echo \Form::submit('submit', '保存する', array('class' => 'btn btn-primary')); ?>
	</div>

</fieldset>
<?php echo \Form::close(); ?>