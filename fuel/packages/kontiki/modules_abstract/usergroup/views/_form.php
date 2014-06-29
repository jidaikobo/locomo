<?php echo Form::open(array("class"=>"form-horizontal")); ?>

	<fieldset>
		<div class="form-group">
			<?php echo Form::label('Usergroup name', 'usergroup_name', array('class'=>'control-label')); ?>

				<?php echo Form::input('usergroup_name', Input::post('usergroup_name', isset($item) ? $item->usergroup_name : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Usergroup name')); ?>

		</div>

		<div class="form-group">
			<label class='control-label'>&nbsp;</label>
			<?php echo Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>		</div>
	</fieldset>
<?php echo Form::close(); ?>