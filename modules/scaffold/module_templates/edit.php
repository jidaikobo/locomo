<?php echo \Form::open(); ?>

<!--form_group-->
<div class="form_group">
<?php
	// use model's form definition instead of raw-like html
	//echo $form;
?>

<table class="formtable">
###FIELDS###
</table>

<?php
	// revision memo template - optional
	//echo render(LOCOMOPATH.'modules/revision/views/inc_revision_memo.php');
?>

<div class="submit_button">
	<?php
###HIDDEN_FIELDS###
	if ( ! @$is_revision): 
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	endif;
	?>
</div>

</div><!--/form_group-->

<?php echo \Form::close(); ?>
