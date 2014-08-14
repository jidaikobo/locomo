<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<div class="form_group">
<fieldset>
	<div class="form-group">
		<?php echo \Form::label('User name', 'user_name', array('class'=>'control-label')); ?>
		<?php echo \Form::input('user_name', Input::post('user_name', isset($item) ? $item->user_name : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'User name')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Usergroups', 'usergroups', array('class'=>'control-label')); ?>
		<div>
		<?php
			foreach($usergroups as $usergroup):
				$checked = null; 
				if(
					(isset($item) && in_array($usergroup->id, $item->usergroups)) ||
					(is_array(\Input::post('usergroup')) && array_key_exists($usergroup->id, \Input::post('usergroup')))
				):
					$checked = ' checked="checked"'; 
				endif;
				echo '<label>'.\Form::checkbox("usergroup[{$usergroup->id}]", 1, array('class' => '',$checked)).$usergroup->usergroup_name.'</label><br />';
			endforeach;
		?>
		</div>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Password', 'password', array('class'=>'control-label')); ?>
		<?php echo \Form::input('password', '', array('class' => 'col-md-4 form-control', 'placeholder'=>'新規作成／変更する場合は入力してください')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Confirm Password', 'password', array('class'=>'control-label')); ?>
		<?php echo \Form::input('confirm_password', '', array('class' => 'col-md-4 form-control', 'placeholder'=>'Confirm Password')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Email', 'email', array('class'=>'control-label')); ?>
		<?php echo \Form::input('email', Input::post('email', isset($item) ? $item->email : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Email')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Status', 'status', array('class'=>'control-label')); ?>
		<?php echo \Form::input('status', Input::post('status', isset($item) ? $item->status : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Status')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('created date', 'created_at', array('class'=>'control-label')); ?>
		<em>未来の日付を入れると、予約項目になります。</em>
		<?php echo \Form::input('created_at', Input::post('created_at', isset($item) ? $item->created_at : ''), array('class' => 'col-md-4 form-control', 'placeholder'=> date('Y-m-d H:i:s'))); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('expired date', 'expired_at', array('class'=>'control-label')); ?>
		<?php echo \Form::input('expired_at', Input::post('expired_at', isset($item) ? $item->expired_at : ''), array('class' => 'col-md-4 form-control', 'placeholder'=> date('Y-m-d H:i:s'))); ?>
	</div>
</fieldset>

<fieldset>
	<legend><?php echo Form::label('編集履歴用メモ', 'revision_comment', array('class'=>'control-label')); ?></legend>
	<?php echo Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;')); ?>
</fieldset>

<p>
		<?php
		if( ! @$is_revision): 
			echo Form::hidden($token_key, $token);
			echo Form::submit('submit', 'Save', array('class' => 'button main'));
		endif;
		?>
</p>
</div>

<?php echo \Form::close(); ?>