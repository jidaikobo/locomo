<?php echo \Form::open(array('autocomplete'=>'off')); ?>
<div class="form_group">
	<table class="formtable">
	<tr class="<?php echo $form->field('username')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('username')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('username')->set_template('<em class="aria_nd"   hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('display_name')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('display_name')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('display_name')->set_template('<em class="aria_nd"   hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<!--
	{fields}<label class="radio inline">{field}{label}</label>{fields}
	-->
	<tr class="<?php echo $form->field('usergroup')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('usergroup')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('usergroup')->set_template('<em class="aria_nd"   hidden="true">{error_msg}</em>{fields} {field} {label}{fields}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<?php
	//管理者以外は旧パスワードを求める
	if ( ! \Auth::is_admin()): ?>
	<tr class="<?php echo $form->field('old_password')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('old_password')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('old_password')->set_template('<em class="aria_nd" hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<?php endif; ?>
	<tr class="<?php echo $form->field('password')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('password')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('password')->set_template('<em class="aria_nd" hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('confirm_password')->set_template('{error_class}'); ?>">
		<th class="ctrl"><?php echo $form->field('confirm_password')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('confirm_password')->set_template('<em class="aria_nd" hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('email')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('email')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('email')->set_template('<em class="aria_nd"   hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<tr class="<?php echo $form->field('created_at')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('created_at')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('created_at')->set_template('<em class="aria_nd" hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	</tr>
	<tr class="<?php echo $form->field('expired_at')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('expired_at')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('expired_at')->set_template('<em class="aria_nd" hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<?php if (\Auth::is_admin()): ?>
	<tr class="<?php echo $form->field('is_visible')->set_template('{error_class}'); ?>">
		<th><?php echo $form->field('is_visible')->set_template('{label}{required}'); ?></th>
		<td><?php echo $form->field('is_visible')->set_template('<em class="aria_nd" hidden="true">{error_msg}</em>{field}')->set_attribute('data-jslcm-tooltip',"{error_msg}"); ?></td>
	</tr>
	<?php endif; ?>
	</table>
	<?php echo render(LOCOMOPATH.'views/revision/inc_revision_memo.php'); ?>
</div>
<div class="submit_button">
	<?php
	if ( ! \Auth::is_admin()):
		echo $form->field('is_visible')->set_template('{field}');
	endif;
		if ( ! @$is_revision):
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', '保存', array('class' => 'button primary'));
	endif;
	?>
</div>
<?php echo \Form::close(); ?>