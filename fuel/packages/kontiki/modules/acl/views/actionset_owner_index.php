<?php echo \View::forge('inc_header'); ?>

<p>対象コントローラ：<code><?php echo $controller ?></code></p>

<?php echo \Form::open(array('action' => \Uri::base(false).'acl/update_owner_acl/', 'class'=>'form-horizontal')); ?>
<fieldset>
<legend><?php echo $controller ?>設定（オーナ向け）</legend>
<div class="form-group">
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
echo Form::hidden('controller', $hidden_controller);
echo Form::hidden('owner', $hidden_owner);
?>
</div>
<div class="form-group">
	<?php echo Html::anchor('acl/controller_index/', '<i class="icon-eye-open"></i> 戻る', array('class' => 'btn btn-small')); ?>
	<?php echo \Form::submit('submit', '保存する', array('class' => 'btn btn-primary')); ?>
</div>
</fieldset>
<?php echo \Form::close(); ?>

<?php echo \View::forge('inc_footer');
