<h1>アクセス権の設定<?php if ($user): ?>（ユーザ単位）<?php else: ?>（ユーザグループ単位）<?php endif; ?></h1>

<p><?php if ($user): ?>
対象ユーザ：<code><?php echo $user ?></code><br />
<?php else: ?>
対象グループ：<code><?php echo $usergroup ?></code><br />
<?php endif; ?>
対象コントローラ：<code><?php echo $ctrl_str ?></code></p>

<ul>
<?php if ($user): ?>
	<li>ユーザグループ単位で許可されいてるアクションを不許可にはできません</li>
<?php endif; ?>
	<li>コンフィグで許可されいてるアクションを不許可にはできません</li>
</ul>

<?php echo \Form::open(array('action' => \Uri::base(false).'acl/update_acl/')); ?>
<h2>設定</h2>

<?php foreach($actionsets as $controller => $each_actionsets): ?>
	<fieldset>
	<legend><?php echo \Util::get_locomo($controller, 'nicename') ?></legend>
	<table class="tbl2">
	<?php
		foreach($each_actionsets as $action => $actionset):
		if ( ! isset($actionset['action_name'])) continue;
		if ( ! isset($actionset['dependencies'])) continue;
	?>
		<tr>
			<th style="width:30%">
				<?php
				$checked = in_array($action, $aprvd_actionset[$controller]) ? ' checked="checked"' : null ;
				echo '<label>'.\Form::checkbox("acls[{$controller}][{$action}]", 1, array($checked)).' '.$actionset['action_name'].'</label><br />';
				?>
			</th>
			<td><?php echo @$actionset['explanation'] ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	</fieldset>
	<?php
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

