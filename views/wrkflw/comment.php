<?php echo \Form::open(); ?>

<h2>ワークフロー名</h2>

<fieldset>
	<legend><?php echo \Form::label('コメント', "comment"); ?></legend>

	<div class="form-group">
		<?php echo \Form::textarea("comment", \Input::post('comment')); ?>
	</div>
</fieldset>

<?php if (@$target_steps): ?>
<fieldset>
	<legend>差戻し先</legend>
	<div class="form-group">
		<?php echo \Form::select("target_step", '', $target_steps); ?>
	</div>
</fieldset>
<?php endif; ?>

<p>
	<?php
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', $button, array('class' => 'button primary'));
	?>
</p>

<?php echo \Form::close(); ?>

