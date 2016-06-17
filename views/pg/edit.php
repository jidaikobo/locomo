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

<?php
	// revision memo template - optional
	//echo render(LOCOMOPATH.'views/revision/inc_revision_memo.php');
?>

<div class="submit_button">
	<?php
if ( ! \Auth::is_admin()):
		echo $form->field('is_visible')->set_template('{error_msg}{field}');

endif;

	if ( ! @$is_revision):
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
	endif;
	?>
</div>

</div><!--/form_group-->

<?php echo \Form::close(); ?>
