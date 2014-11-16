<?php echo \Form::open(); ?>
<div class="form_group">
	<table class="formtable">
	<tr class="<?php echo $form->field('username')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('username')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('username')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('display_name')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('display_name')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('display_name')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('usergroup')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('usergroup')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('usergroup')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{fields} {field} {label}{fields}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<?php
	//管理者以外は旧パスワードを求める
	if( ! \Auth::is_admin()): ?>
	<tr class="<?php echo $form->field('old_password')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('old_password')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('old_password')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<?php endif; ?>
	<tr class="<?php echo $form->field('password')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('password')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('password')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('confirm_password')->set_template('{error_class}'); ?>">
		<th class="ctrl"><?php echo $form->field('confirm_password')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('confirm_password')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('email')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('email')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('email')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('created_at')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('created_at')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('created_at')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<?php if(\Auth::is_admin()): ?>
	<tr class="<?php echo $form->field('is_visible')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('is_visible')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('is_visible')->set_template('<span class="aria_nd" hidden="true">{error_msg}</span>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<?php endif; ?>
	</table>
	<div class="revision_comment">
		<h3><a href="javascript: void(0);" class="toggle_item disclosure"><?php echo \Form::label('編集履歴用メモ', 'revision_comment'); ?></a></h3>
	<?php echo \Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;','class'=>'hidden_item')); ?>
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
<?php echo \Form::close(); ?>
