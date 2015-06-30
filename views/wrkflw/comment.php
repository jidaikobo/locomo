<?php echo \Form::open(); ?>

<h1>
ワークフロー: <?php echo $workflow->name ?>
<?php if ($current_step == -1): ?>
（段階 承認申請）
<?php else: ?>
（段階 <?php echo $total_step ?>段階中<?php echo $current_step ?>段階目）
<?php endif; ?>
</h1>

<fieldset>
	<legend><?php echo \Form::label('コメント', "comment"); ?></legend>

	<div class="form-group">
		<?php echo \Form::textarea("comment", \Input::post('comment'), array('class' => 'textarea')); ?>
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

<div class="submit_button">
	<?php
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', $button, array('class' => 'button primary confirm'));
	?>
</div>

<?php echo \Form::close(); ?>

