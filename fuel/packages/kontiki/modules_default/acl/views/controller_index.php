<?php echo $include_tpl('inc_header.php'); ?>

<ul>
	<li>アクセス権限は「ユーザグループ単位」「ユーザ単位」のいずれかごとに設定できます。</li>
	<li>「コントローラ」のドロップダウンでは、このシステムで制御できる項目を選択できます。</li>
	<li>ユーザグループ単位の場合は、対象コントローラとユーザグループを。ユーザ単位の場合は、同様に対象コントローラとユーザを選択して「次へ」を押してください。</li>
</ul>

<!--ユーザグループ単位の権限設定-->
<?php echo \Form::open(array('action' => \Uri::base(false).'acl/actionset_index/', 'class'=>'form-horizontal')); ?>
<fieldset>
<legend>ユーザグループ単位の権限設定</legend>
<div class="form-group">
<?php
	echo \Form::label('コントローラをドロップダウンから選択', 'controller', array('class'=>'control-label'));
	echo Form::select('controller', 'none', $controllers, array('class' => 'col-md-4 form-control'));
?>
</div>
<div class="form-group">
<?php
	echo \Form::label('ユーザグループをドロップダウンから選択', 'usergroup', array('class'=>'control-label'));
	echo Form::select('usergroup', 'none', $usergroups, array('class' => 'col-md-4 form-control'));
?>
</div>
<div class="form-group">
<?php
	echo Form::hidden($token_key, $token);
	echo \Form::submit('submit', '次へ', array('class' => 'btn btn-primary'));
?>
</div>
</fieldset>
<?php echo \Form::close(); ?>

<!--ユーザ単位の権限設定-->
<?php echo \Form::open(array('action' => \Uri::base(false).'acl/actionset_index/', 'class'=>'form-horizontal')); ?>
<fieldset>
<legend>ユーザ単位の権限設定</legend>
<div class="form-group">
<?php
	echo \Form::label('コントローラをドロップダウンから選択', 'controller', array('class'=>'control-label'));
	echo Form::select('controller', 'none', $controllers, array('class' => 'col-md-4 form-control'));
?>
</div>
<div class="form-group">
<?php
	echo \Form::label('ユーザをドロップダウンから選択', 'user', array('class'=>'control-label'));
	echo Form::select('user', 'none', $users, array('class' => 'col-md-4 form-control'));
?>
</div>
<div class="form-group">
<?php
	echo Form::hidden($token_key, $token);
	echo \Form::submit('submit', '次へ', array('class' => 'btn btn-primary'));
?>
</div>
</fieldset>
<?php echo \Form::close(); ?>

<!--オーナ権限設定-->
<?php echo \Form::open(array('action' => \Uri::base(false).'acl/actionset_owner_index/', 'class'=>'form-horizontal')); ?>
<fieldset>
<legend>オーナ権限設定</legend>
<div class="form-group">
<?php
	echo \Form::label('コントローラをドロップダウンから選択', 'controller', array('class'=>'control-label'));
	echo Form::select('controller', 'none', $controllers_owner, array('class' => 'col-md-4 form-control'));
?>
</div>
<div class="form-group">
<?php
	echo Form::hidden($token_key, $token);
	echo \Form::hidden('owner', '1');
	echo \Form::submit('submit', '次へ', array('class' => 'btn btn-primary'));
?>
</div>
</fieldset>
<?php echo \Form::close(); ?>

<?php echo $include_tpl('inc_footer.php'); ?>
