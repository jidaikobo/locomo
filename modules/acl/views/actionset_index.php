
<p><?php if ($user): ?>
対象ユーザ：<code><?php echo $user ?></code><br />
<?php else: ?>
対象グループ：<code><?php echo $usergroup ?></code><br />
<?php endif; ?>
対象コントローラ：<code><?php echo $ctrl_str ?></code></p>

<p>依存した行為を許可すると、自動的にほかの行為が許可される場合があります。たとえば「項目を編集する権利」を持った人は、「通常項目を閲覧する権利」が自動的に許可されます。</p>

<?php echo \Form::open(array('action' => \Uri::base(false).'acl/update_acl/')); ?>
<h2>設定（ユーザグループ単位）</h2>

<?php foreach($actionsets as $controller => $each_actionsets): ?>
<h3><?php echo $controller::$locomo['nicename'] ?></h3>
	<?php foreach($each_actionsets as $realm => $each_actionset): ?>
	<fieldset>
	<legend><?php echo $realm ?></legend>
	<table class="tbl2">
	<?php
		foreach($each_actionset as $action => $actionset):
		if ( ! isset($actionset['action_name'])) continue;
		?>
		<tr>
			<th style="width:30%">
				<?php
				$checked = in_array($action, $aprvd_actionset[$controller][$realm]) ? ' checked="checked"' : null ;
				echo '<label>'.\Form::checkbox("acls[{$controller}][{$realm}][{$action}]", 1, array($checked)).' '.$actionset['action_name'].'</label><br />';
				?>
			</th>
			<td><?php echo @$actionset['explanation'] ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	</fieldset>
	<?php
	endforeach; 
endforeach;
?>

<?php
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::hidden('mod_or_ctrl', $mod_or_ctrl);
echo \Form::hidden('user', $hidden_user);
echo \Form::hidden('usergroup', $hidden_usergroup);
?>

<div class="button_group">
	<?php echo Html::anchor('acl/controller_index/', '戻る', array('class' => 'button')); ?>
	<?php echo \Form::submit('submit', '保存する', array('class' => 'button primary')); ?>
</div>
<?php echo \Form::close(); ?>

