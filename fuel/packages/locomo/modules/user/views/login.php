<h2><img src="/content/fetch_view/images/parts/logo.png" alt=""><?php echo \Config::get('site_title') ;?></h2>

<?php echo \Form::open(array('action' => \Uri::base(false).'user/auth/login/','class' => 'login')); ?>

<!--ユーザ名かメールアドレス-->
<div class="form_group">
	<?php echo '<span id="label1">'.\Form::label('アカウント', 'username').'</span>'; ?>
	<?php echo '<span id="desc1" class="skip">ユーザ名かメールアドレス</span>'.\Form::input('username', Input::post('username', isset($item) ? $item->username : ''), array('placeholder'=>'ユーザ名かメールアドレス','aria-describedby'=>'desc1')); ?>
</div>

<!--パスワード-->
<div class="form_group">
	<?php echo '<span id="label2">'. \Form::label('パスワード', 'password').'</span>'; ?>
	<?php echo \Form::password('password', Input::post('password', isset($item) ? $item->password : ''), array('placeholder'=>'パスワード')); ?>
</div>

<!--remember me-->
<div class="form_group">
	<span id="label2">
	<?php echo \Form::checkbox('remember', Input::post('remember', isset($item) ? $item->remember : '')); ?>
	<?php echo \Form::label('ログインを維持する', 'remember'); ?>
	</span>
</div>

<?php
//buttons
echo \Form::hidden('ret', $ret);
echo \Form::submit('submit', 'ログイン', array('class' => 'button primary button_block'));
?>

<?php
if(!\Config::get('no_home')) echo Html::anchor('/', \Config::get('site_title')."へ");
echo \Form::close();
?>
