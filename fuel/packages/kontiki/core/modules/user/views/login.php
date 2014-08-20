<?php echo $include_tpl('inc_header.php'); ?>

<script type="text/javascript">
<!--thx WordPress-->
function kontiki_attempt_focus(){
	setTimeout(
		function(){
			try{
				account = document.getElementById('account');
				account.focus();
				account.select();
			} catch(e){}
		}, 200
	);
}
kontiki_attempt_focus();
</script>

<?php echo \Form::open(array('action' => \Uri::base(false).'user/login/'.$ret)); ?>

<fieldset>
<legend>ログイン</legend>

<!--ユーザ名かメールアドレス-->
<div class="form-group">
	<?php echo \Form::label('アカウント', 'account'); ?>
	<?php echo \Form::input('account', Input::post('account', isset($item) ? $item->account : ''), array('id' => 'account', 'placeholder'=>'ユーザ名かメールアドレスを入力')); ?>
</div>

<!--パスワード-->
<div class="form-group">
	<?php echo \Form::label('パスワード', 'password'); ?>
	<?php echo \Form::password('password', Input::post('password', isset($item) ? $item->password : ''), array('placeholder'=>'パスワードを入力')); ?>
</div>

<?php
//buttons
echo Html::anchor('/', '戻る', array('class' => 'button'));
echo \Form::submit('submit', 'ログイン', array('class' => 'button main'));
?>
</fieldset>
<?php echo \Form::close(); ?>

<?php echo $include_tpl('inc_footer.php'); ?>