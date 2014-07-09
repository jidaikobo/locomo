<?php echo \View::forge(PKGPATH.'kontiki/views/inc_header.php'); ?>

<?php echo \Form::open(array('action' => \Uri::base(false).'user/login/'.$ret, 'class'=>'form-horizontal')); ?>

<fieldset>
<legend>ログイン</legend>

<!--ユーザ名かメールアドレス-->
<div class="form-group">
	<?php echo \Form::label('アカウント', 'account', array('class'=>'control-label')); ?>
	<?php echo \Form::input('account', Input::post('account', isset($item) ? $item->account : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'ユーザ名かメールアドレスを入力')); ?>
</div>

<!--パスワード-->
<div class="form-group">
	<?php echo \Form::label('パスワード', 'password', array('class'=>'control-label')); ?>
	<?php echo \Form::password('password', Input::post('password', isset($item) ? $item->password : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'パスワードを入力')); ?>
</div>

<?php
//buttons
echo Html::anchor('/', '戻る', array('class' => 'btn btn-small'));
echo \Form::submit('submit', 'ログイン', array('class' => 'btn btn-primary'));
?>
</fieldset>
<?php echo \Form::close(); ?>

<?php echo \View::forge(PKGPATH.'kontiki/views/inc_footer.php'); ?>
