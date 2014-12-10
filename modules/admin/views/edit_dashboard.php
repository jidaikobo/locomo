<?php echo \Form::open(); ?>

<?php
	echo $form;
	echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
	echo \Form::submit('submit', '保存', array('class' => 'button primary'));
?>

<?php echo \Form::close(); ?>