<?php echo render('inc_header'); ?>

<script type="text/javascript">
<!--thx WordPress-->
function kontiki_attempt_focus(){
	setTimeout(
		function(){
			try{
				account = document.getElementById('form_account');
				account.focus();
				account.select();
			} catch(e){}
		}, 200
	);
}
kontiki_attempt_focus();
</script>

<?php echo \Form::open(array('action' => \Uri::base(false).'user/login/'.$ret,'class' => 'login')); ?>
<div class="form_group">
<!--ユーザ名かメールアドレス-->
<div class="form-group">
	<?php echo \Form::label('アカウント', 'account'); ?>
	<?php echo \Form::input('account', Input::post('account', isset($item) ? $item->account : ''), array('placeholder'=>'ユーザ名かメールアドレスを入力')); ?>
</div>

<!--パスワード-->
<div class="form-group">
	<?php echo \Form::label('パスワード', 'password'); ?>
	<?php echo \Form::password('password', Input::post('password', isset($item) ? $item->password : ''), array('placeholder'=>'パスワードを入力')); ?>
</div>

<?php
//buttons
echo \Form::submit('submit', 'ログイン', array('class' => 'button primary'));
?>
</div>

<?php
echo Html::anchor('/', "{$site_title}へ");
echo \Form::close();
?>

<?php echo render('inc_footer'); ?>
