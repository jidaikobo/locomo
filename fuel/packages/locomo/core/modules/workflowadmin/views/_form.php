<?php echo \Form::open(); ?>

<fieldset>
	<div class="form-group">
		<?php echo \Form::label('ワークフロー名', 'name'); ?>
		<?php echo \Form::input('name', Input::post('name', isset($item) ? $item->name : ''), array('placeholder'=>'User name')); ?>
	</div>
</fieldset>

<div class="form-group">
	<?php
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo Html::anchor('workflowadmin/index_admin', '一覧に戻る', array('class' => 'button'));
		echo \Form::submit('submit', '保存', array('class' => 'button primary'));
	?>
</div>

<?php echo \Form::close(); ?>