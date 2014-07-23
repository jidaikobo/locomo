<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<fieldset>
	<div class="form-group">
		<?php echo \Form::label('ワークフロー名', 'name', array('class'=>'control-label')); ?>
		<?php echo \Form::input('name', Input::post('name', isset($item) ? $item->name : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'User name')); ?>
	</div>

	<div class="form-group">
		<?php
			echo Form::hidden($token_key, $token);
			echo Form::submit('submit', '保存', array('class' => 'button main'));
		?>
	</div>

</fieldset>

<?php echo \Form::close(); ?>