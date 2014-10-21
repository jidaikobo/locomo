<?php echo render('inc_admin_header'); ?>

<ul>
	<li>アクセス権限は「ユーザグループ単位」「ユーザ単位」のいずれかごとに設定できます。</li>
	<li>「コントローラ」のドロップダウンでは、このシステムで制御できる項目を選択できます。</li>
	<li>ユーザグループ単位の場合は、対象コントローラとユーザグループを。ユーザ単位の場合は、同様に対象コントローラとユーザを選択して「次へ」を押してください。</li>
</ul>

<!--ユーザグループ単位の権限設定-->
<?php echo \Form::open(array('action' => \Uri::base(false).'acl/actionset_index/')); ?>
<fieldset>
<legend>ユーザグループ単位の権限設定</legend>
<p>
<?php
	echo \Form::label('コントローラをドロップダウンから選択', 'controller');
	echo \Form::select('controller', 'none', $controllers4acl);
?>
</p>
<p>
<?php
	echo \Form::label('ユーザグループをドロップダウンから選択', 'usergroup');
	echo \Form::select('usergroup', 'none', $usergroups);
?>
</p>
<p>
<?php
	echo \Form::hidden($token_key, $token);
	echo \Form::submit('submit', '次へ', array('class' => 'button primary'));
?>
</p>
</fieldset>
<?php echo \Form::close(); ?>

<!--ユーザ単位の権限設定-->
<?php echo \Form::open(array('action' => \Uri::base(false).'acl/actionset_index/')); ?>
<fieldset>
<legend>ユーザ単位の権限設定</legend>
<p>
<?php
	echo \Form::label('コントローラをドロップダウンから選択', 'controller');
	echo \Form::select('controller', 'none', $controllers4acl);
?>
</p>
<p>
<?php
	echo \Form::label('ユーザをドロップダウンから選択', 'user');
	echo \Form::select('user', 'none', $users);
?>
</p>
<p>
<?php
	echo \Form::hidden($token_key, $token);
	echo \Form::submit('submit', '次へ', array('class' => 'button primary'));
?>
</p>
</fieldset>
<?php echo \Form::close(); ?>

<!--オーナ権限設定-->
<?php echo \Form::open(array('action' => \Uri::base(false).'acl/actionset_owner_index/')); ?>
<fieldset>
<legend>オーナ権限設定</legend>
<p>
<?php
	echo \Form::label('コントローラをドロップダウンから選択', 'controller');
	echo \Form::select('controller', 'none', $controllers_owner4acl);
?>
</p>
<p>
<?php
	echo \Form::hidden($token_key, $token);
	echo \Form::hidden('owner', '1');
	echo \Form::submit('submit', '次へ', array('class' => 'button primary'));
?>
</p>
</fieldset>
<?php echo \Form::close(); ?>

<?php echo render('inc_admin_footer'); ?>
