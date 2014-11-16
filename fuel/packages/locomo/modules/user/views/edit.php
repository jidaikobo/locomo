<?php echo \Form::open(); ?>

<div class="form_group">
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
<?php
//管理者以外は旧パスワードを求める
if( ! \Auth::is_admin()): ?>
	<tr>
		<th><?php echo $form->field('old_password')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('old_password')->set_template('{error_msg}{field}'); ?></td>
	</tr>
<?php endif; ?>
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
	<?php echo render(LOCOMOPATH.'modules/revision/views/inc_revision_memo.php'); ?>
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
<?php echo \Form::close(); ?>
