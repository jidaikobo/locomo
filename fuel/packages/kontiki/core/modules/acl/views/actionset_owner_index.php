<?php echo $include_tpl('inc_admin_header.php'); ?>

<p>対象コントローラ：<code><?php echo $controller ?></code></p>

<?php echo \Form::open(array('action' => \Uri::base(false).'acl/update_owner_acl/')); ?>
<fieldset>
<legend><?php echo $controller ?>設定（オーナ向け）</legend>
<table>
<?php foreach($actionsets as $action => $actionset): ?>
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
echo \Form::hidden('owner', $hidden_owner);
?>
</fieldset>

<div class="button_group">
	<?php echo Html::anchor('acl/controller_index/', '戻る', array('class' => 'button')); ?>
	<?php echo \Form::submit('submit', '保存する', array('class' => 'button main')); ?>
</div>
<?php echo \Form::close(); ?>

<?php echo $include_tpl('inc_admin_footer.php'); ?>
