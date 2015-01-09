<?php
echo \Form::open();
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo render(LOCOMOPATH.'views/revision/inc_revision_memo.php');
?>
<div class="submit_button">
<?php echo \Form::submit('submit', '保存', array('class' => 'button primary')); ?>
</div>
<?php echo \Form::close();  ?>
