<h1>アクセス権管理</h1>

<ul>
	<li>アクセス権限は「ユーザグループ単位」「ユーザ単位」のいずれかごとに設定できます。</li>
	<li>「コントローラ」のドロップダウンでは、このシステムで制御できる項目を選択できます。</li>
	<li>ユーザグループ単位の場合は、対象コントローラとユーザグループを。ユーザ単位の場合は、同様に対象コントローラとユーザを選択して「次へ」を押してください。</li>
</ul>

<!--ユーザグループ単位の権限設定-->
<?php echo \Form::open(array('action' => \Uri::base(false).'acl/actionset_index/', 'class'=>'lcm_form form_group')); ?>
<fieldset>
<legend>ユーザグループ単位の権限設定</legend>
<p>
<?php
	echo \Form::label('コントローラをドロップダウンから選択', 'mod_or_ctrl');
	echo \Form::select('mod_or_ctrl', 'none', \Model_Acl::get_mod_or_ctrl());
?>
</p>
<p>
<?php
	echo \Form::label('ユーザグループをドロップダウンから選択', 'usergroup');
	echo \Form::select('usergroup', 'none', \Model_Acl::get_usergroups());
?>
</p>
<div class="submit_button">
<?php
	echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
	echo \Form::submit('submit', '次へ', array('class' => 'button primary'));
?>
</div>
</fieldset>
<?php echo \Form::close(); ?>

<!--ユーザ単位の権限設定-->
<?php echo \Form::open(array('action' => \Uri::base(false).'acl/actionset_index/', 'class'=>'lcm_form form_group')); ?>
<fieldset>
<legend>ユーザ単位の権限設定</legend>
<p>
<?php
	echo \Form::label('コントローラをドロップダウンから選択', 'mod_or_ctrl');
	echo \Form::select('mod_or_ctrl', 'none', \Model_Acl::get_mod_or_ctrl());
?>
</p>
<p>
<?php
	echo \Form::label('ユーザをドロップダウンから選択', 'usergroup');
	echo \Form::select('group_list', 'none', \Model_Acl::get_usergroups(), array('class'=>'select_narrow_down', 'data-target-id'=>'form_user'));
	echo \Form::select('user', 'none', \Model_Usr::get_users());
?>
</p>
<div class="submit_button">
<?php
	echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
	echo \Form::submit('submit', '次へ', array('class' => 'button primary'));
?>
</div>
</fieldset>
<?php echo \Form::close(); ?>