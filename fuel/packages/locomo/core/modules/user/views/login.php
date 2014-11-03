<script type="text/javascript">
<!--thx WordPress-->
function locomo_attempt_focus(){
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
locomo_attempt_focus();
</script>


<h2><img src="/content/fetch_view/images/parts/logo.png" alt=""><?php echo $site_title ;?></h2>

<?php echo \Form::open(array('action' => \Uri::base(false).'user/login/','class' => 'login')); ?>

<!--ユーザ名かメールアドレス-->
<div class="form_group">
	<?php echo \Form::label('アカウント', 'account'); ?>
	<?php echo \Form::input('account', Input::post('account', isset($item) ? $item->account : ''), array('placeholder'=>'ユーザ名かメールアドレスを入力')); ?>
</div>

<!--パスワード-->
<div class="form_group">
	<?php echo \Form::label('パスワード', 'password'); ?>
	<?php echo \Form::password('password', Input::post('password', isset($item) ? $item->password : ''), array('placeholder'=>'パスワードを入力')); ?>
</div>

<?php
//buttons
echo \Form::hidden('ret', $ret);
echo \Form::submit('submit', 'ログイン', array('class' => 'button primary button_block'));
?>

<?php
if(!\Config::get('use_login_as_top')){
echo Html::anchor('/', "{$site_title}へ");
}
echo \Form::close();
?>
