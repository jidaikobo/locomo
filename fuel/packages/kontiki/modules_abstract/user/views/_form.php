<?php echo Form::open(array("class"=>"form-horizontal")); ?>

	<fieldset>
		<div class="form-group">
			<?php echo Form::label('User name', 'user_name', array('class'=>'control-label')); ?>

				<?php echo Form::input('user_name', Input::post('user_name', isset($item) ? $item->user_name : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'User name')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Password', 'password', array('class'=>'control-label')); ?>

				<?php echo Form::input('password', '', array('class' => 'col-md-4 form-control', 'placeholder'=>'新規作成／変更する場合は入力してください')); ?>

		</div>

		<div class="form-group">
			<?php echo Form::label('Confirm Password', 'password', array('class'=>'control-label')); ?>

				<?php echo Form::input('confirm_password', '', array('class' => 'col-md-4 form-control', 'placeholder'=>'Confirm Password')); ?>

		</div>

		<div class="form-group">
			<?php echo Form::label('Email', 'email', array('class'=>'control-label')); ?>

				<?php echo Form::input('email', Input::post('email', isset($item) ? $item->email : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Email')); ?>

		</div>

		<div class="form-group">
			<?php echo Form::label('Status', 'status', array('class'=>'control-label')); ?>

				<?php echo Form::input('status', Input::post('status', isset($item) ? $item->status : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Status')); ?>

		</div>

		<div class="form-group">
			<?php echo Form::label('created date', 'created_at', array('class'=>'control-label')); ?>

				<?php //echo Form::input('created_at', Input::post('created_at', isset($item) ? date('Y-m-d H:i:s',$item->created_at) : ''), array('class' => 'col-md-4 form-control', 'placeholder'=> date('Y-m-d H:i:s'))); ?>

		</div>

		<div class="form-group">
			<label class='control-label'>&nbsp;</label>
			<?php echo Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>		</div>
	</fieldset>
<?php echo Form::close(); ?>