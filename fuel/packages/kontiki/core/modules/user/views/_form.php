<?php echo \Form::open(); ?>

<div class="form_group">
<fieldset>
	<div class="form-group">
		<?php echo \Form::label('User name', 'user_name'); ?>
		<?php echo \Form::input('user_name', Input::post('user_name', isset($item) ? $item->user_name : ''), array('placeholder'=>'User name')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Usergroups', 'usergroups'); ?>
		<div>
		<?php
		foreach(\User\Model_User::get_options('usergroups') as $key => $option):
			$checked = false; 
			if(
				(isset($item) && in_array($key, $item->usergroups)) ||
				(is_array(\Input::post('usergroups')) && array_key_exists($key, \Input::post('usergroups')))
			):
				$checked = true; 
			endif;
			echo '<label>'.\Form::checkbox('usergroups[]', $key, $checked ).$option.'</label><br />';
		endforeach;
		?>
		</div>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Password', 'password'); ?>
		<?php echo \Form::input('password', '', array('placeholder'=>'新規作成／変更する場合は入力してください')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Confirm Password', 'password'); ?>
		<?php echo \Form::input('confirm_password', '', array('placeholder'=>'Confirm Password')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Email', 'email'); ?>
		<?php echo \Form::input('email', Input::post('email', isset($item) ? $item->email : ''), array('placeholder'=>'Email')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('Status', 'status'); ?>
		<?php echo \Form::input('status', Input::post('status', isset($item) ? $item->status : ''), array('placeholder'=>'Status')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('created date', 'created_at'); ?>
		<em>未来の日付を入れると、予約項目になります。</em>
		<?php echo \Form::input('created_at', Input::post('created_at', isset($item) ? $item->created_at : ''), array('placeholder'=> date('Y-m-d H:i:s'))); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('expired date', 'expired_at'); ?>
		<?php echo \Form::input('expired_at', Input::post('expired_at', isset($item) ? $item->expired_at : ''), array('placeholder'=> date('Y-m-d H:i:s'))); ?>
	</div>
</fieldset>

<fieldset>
	<legend><?php echo \Form::label('編集履歴用メモ', 'revision_comment'); ?></legend>
	<?php echo \Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;')); ?>
</fieldset>

<div class="button_group">
		<?php
		if(@$is_revision):
			echo Html::anchor('user/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
			echo Html::anchor('user/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
		else:
			echo \Form::hidden($token_key, $token);
			echo Html::anchor('user', '一覧に戻る',array('class'=>'button'));
			echo \Form::submit('submit', '保存', array('class' => 'button primary'));
		endif;
		?>
</div>
</div>

<?php echo \Form::close(); ?>