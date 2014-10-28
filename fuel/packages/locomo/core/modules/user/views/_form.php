<?php echo \Form::open(); ?>
<h2>編集</h2>
<div class="form_group">
	<fieldset>
		<legend>項目</legend>
		<table>
		<tr>
			<th><?php echo $form->field('user_name')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('user_name')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<tr>
			<th><?php echo $form->field('display_name')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('display_name')->set_template('{error_msg}{field}'); ?></td>
		</tr>
		<tr>
			<th><?php echo $form->field('usergroup')->set_template('{label}{required}'); ?></th>
			<td><?php echo $form->field('usergroup')->set_template('{fields} {field} {label}<br /> {fields}'); ?></td>
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
			echo $form->field('status')->set_template('{field}');
			if(!@$is_revision):
				echo \Form::hidden($token_key, $token);
				echo \Form::submit('submit', '保存', array('class' => 'button primary'));
			endif;
			?>
		</div>
	</div>
</div>

<?php echo \Form::close(); ?>