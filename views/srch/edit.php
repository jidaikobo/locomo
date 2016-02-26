<?php
//echo \Form::open(array('enctype' => 'multipart/form-data', 'class' => 'lcm_form form_group'));
echo \Form::open(array('class' => 'lcm_form form_group'));
?>

<!--form_group-->
<div class="lcm_form form_group">
<?php
	// use model's form definition instead of raw-like html
	echo $form;
?>

<?php /* ?>
<table class="formtable">
<tr>
	<th><?php echo $form->field('controller')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('controller')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('pid')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('pid')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('url')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('url')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('seacrh')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('seacrh')->set_template('{error_msg}{field}'); ?></td>
</tr>


</table>
<?php */ ?>

<?php
	// revision memo template - optional
	//echo render(LOCOMOPATH.'views/revision/inc_revision_memo.php');
?>

<div class="submit_button">
	<?php

	if ( ! @$is_revision):
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	endif;
	?>
</div>

</div><!--/form_group-->

<?php echo \Form::close(); ?>
