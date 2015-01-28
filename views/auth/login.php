<h2>
<?php
	// use default or override logo
	echo \Asset::img('system/logo.png', array('id' => 'logo', 'title' => \Config::get('site_title')));
	echo '<span class="site_title" aria-hidden="true">'.\Config::get('site_title').'</span>';
?>
</h2>

<?php echo \Form::open(array('action' => \Uri::base(false).'auth/login/','class' => 'login')); ?>

<!-- username or mail -->
<div class="form_group account">
	<?php echo \Form::label('アカウント', 'username'); ?>
	<?php echo \Form::input('username', Input::post('username', isset($item) ? $item->username : ''), array('placeholder'=>'ユーザ名かメールアドレス','title'=>'アカウント ユーザ名かメールアドレスを入力してください', 'size'=>'20')); ?>
</div>

<!-- password -->
<div class="form_group password">
	<?php echo \Form::label('パスワード', 'password'); ?>
	<?php echo \Form::password('password', Input::post('password', isset($item) ? $item->password : ''), array('placeholder'=>'パスワード','title'=>'パスワード', 'size'=>'20')); ?>
</div>
<!--remember me-->
<div class="form_group remember">
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
if ( ! \Config::get('no_home')) echo Html::anchor('/', \Config::get('site_title')."へ");
echo \Form::close();
?>
