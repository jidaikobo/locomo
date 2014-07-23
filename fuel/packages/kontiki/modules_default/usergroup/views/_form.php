<?php echo Form::open(array("class"=>"form-horizontal")); ?>

<fieldset>
	<div class="form-group">
		<?php echo Form::label('ユーザグループ名', 'usergroup_name', array('class'=>'control-label')); ?>
		<?php echo Form::input('usergroup_name', Input::post('usergroup_name', isset($item) ? $item->usergroup_name : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Usergroup name')); ?>
	</div>

	<div class="form-group revision_comment">
		<?php echo Form::label('編集メモ', 'revision_comment', array('class'=>'control-label')); ?>
		<?php echo Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'')); ?>
	</div>

	<div class="form-group">
		<?php if( ! @$is_revision): ?>
			<label class='control-label'>&nbsp;</label>
			<?php
			echo Form::hidden($token_key, $token);
			echo Form::submit('submit', 'Save', array('class' => 'button main'));
			?>
		<?php endif; ?>
	</div>
</fieldset>

<?php echo Form::close(); ?>