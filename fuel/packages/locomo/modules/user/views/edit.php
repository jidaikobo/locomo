<?php echo \Form::open(); ?>
<h2>編集</h2>
<div class="form_group">
	<fieldset>
		<legend>項目</legend>
		<table>
		<tr>
			<th><?php echo $form->field('username')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('username')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<tr>
			<th><?php echo $form->field('display_name')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('display_name')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<tr>
			<th><?php echo $form->field('usergroup')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('usergroup')->set_template('{fields} {field} {label}{fields}'); ?></td>
		</tr>
		<tr>
			<th><?php echo $form->field('password')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('password')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<tr>
			<th><?php echo $form->field('confirm_password')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('confirm_password')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<tr>
			<th><?php echo $form->field('email')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('email')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<tr>
			<th><?php echo $form->field('created_at')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('created_at')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<?php if(\Auth::is_admin()): ?>
		<tr>
			<th><?php echo $form->field('is_visible')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('is_visible')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<?php endif; ?>
		</table>
	</fieldset>
	<div class="submit_field">
		<div class="revision_comment">
		<fieldset>
			<legend><?php echo \Form::label('編集履歴用メモ', 'revision_comment'); ?></legend>
			<?php echo \Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;')); ?>
		</fieldset>
		</div>
		<div class="submit_button">
			<?php
			if( ! \Auth::is_admin()):
				echo $form->field('is_visible')->set_template('{field}');
			endif;

			if(!@$is_revision):
				echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
				echo \Form::submit('submit', '保存', array('class' => 'button primary'));
			endif;
			?>
		</div>
	</div>
</div>
<?php echo \Form::close(); ?>
