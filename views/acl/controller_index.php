<h1>アクセス権管理</h1>

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
<p>
<?php
	echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
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
	echo \Form::label('コントローラをドロップダウンから選択', 'mod_or_ctrl');
	echo \Form::select('mod_or_ctrl', 'none', \Model_Acl::get_mod_or_ctrl());
?>
</p>
<p>
<?php
	echo \Form::label('ユーザをドロップダウンから選択', 'usergroup');
	echo \Form::select('group_list', 'none', \Model_Acl::get_usergroups());
	echo \Form::select('user', 'none', \Model_Usr::get_users());
?>
</p>
<p>
<?php
	echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
	echo \Form::submit('submit', '次へ', array('class' => 'button primary'));
?>
</p>
</fieldset>
<?php echo \Form::close(); ?>

<script>
// ユーザグループによるユーザの絞り込み
$("#form_group_list").change(function(event) {
	get_group_user(event, $("#form_group_list").val(), "form_user");
});

var base_uri = $('body').data('uri');
function get_group_user(e, groupId, targetEle) {

	var targetEle = targetEle;
	var group_id = groupId;

	$.ajax({
		url: base_uri + 'usr/user_list.json',
		type: 'post',
		data: 'gid=' + group_id,
		success: function(res) {
			exists = JSON.parse(res);

			document.getElementById(targetEle).options.length=0;

			for(var i in exists) {
				$("#" + targetEle).append($('<option>').html(exists[i]['display_name']).val(exists[i]['id']));
			}
		}
	});
}
</script>
