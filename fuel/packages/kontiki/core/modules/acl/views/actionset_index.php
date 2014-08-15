<?php echo $include_tpl('inc_header.php'); ?>

<p><?php if($user): ?>
対象ユーザ：<code><?php echo $user ?></code><br />
<?php else: ?>
対象グループ：<code><?php echo $usergroup ?></code><br />
<?php endif; ?>
対象コントローラ：<code><?php echo $controller ?></code></p>

<p>依存した行為を許可すると、自動的にほかの行為が許可される場合があります。たとえば「項目を編集する権利」を持った人は、「通常項目を閲覧する権利」が自動的に許可されます。</p>

<?php echo \Form::open(array('action' => \Uri::base(false).'acl/update_acl/')); ?>
<fieldset>
<legend><?php echo $controller ?>設定（ユーザグループ単位）</legend>

<table>
<?php
foreach($actionsets as $action => $actionset):
if( ! isset($actionset['action_name'])) continue;
?>
<tr>
	<th style="white-space: nowrap;">
		<?php
		$checked = in_array($action, $aprvd_actionset) ? ' checked="checked"' : null ;
		echo '<label>'.\Form::checkbox("acls[{$action}]", 1, array($checked)).' '.$actionset['action_name'].'</label><br />';
		?>
	</th>
	<td><?php echo $actionset['explanation'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php
echo \Form::hidden($token_key, $token);
echo \Form::hidden('controller', $hidden_controller);
echo \Form::hidden('user', $hidden_user);
echo \Form::hidden('usergroup', $hidden_usergroup);
?>
</fieldset>

<div class="button_group">
	<?php echo Html::anchor('acl/controller_index/', '戻る', array('class' => 'button')); ?>
	<?php echo \Form::submit('submit', '保存する', array('class' => 'button main')); ?>
</div>
<?php echo \Form::close(); ?>

<?php echo $include_tpl('inc_footer.php'); ?>
