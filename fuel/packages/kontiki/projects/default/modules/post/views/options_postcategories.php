<?php echo $include_tpl('inc_admin_header.php'); ?>

<?php echo \Form::open(array("class"=>"form-horizontal")); ?>
<?php if( ! @$is_revision): ?>
<div class="form_group">
	<h2>新しい項目の追加</h2>
	
	<fieldset>
		<!--table構造に応じて変更してください-->
		<legend><?php echo \Form::label('項目名', 'name', array('class'=>'control-label')); ?></legend>
		<?php echo \Form::input('name', '', array('placeholder' => 'name')); ?>
	</fieldset>

	
	<div class="form-group">
		<?php
			echo Form::hidden($token_key, $token);
			echo Form::hidden('mode', 'add');
			echo Form::submit('submit', '新規追加', array('class' => 'button primary'));
		?>
	</div>
</div>
<?php echo \Form::close(); ?>
<?php endif; ?>

<?php if($items): ?>

	<div class="form_group">
	<h2>編集</h2>
	<?php echo \Form::open(array("class"=>"form-horizontal")); ?>
	
	<fieldset>
	<legend>項目設定</legend>

	<table class="tbl">
	<thead>
		<tr>
			<th>項目名</th>
			<th>順序</th>
			<th>有効性</th>
			<th>操作</th>
		</tr>
	</thead>
	<?php
		foreach($items as $item):
			if( ! isset($item->id)) continue;
			$opacity = ($item->is_available) ? '' : 'opacity: 0.3;';
			//こちらもtable構造に応じて変更してください。
	?>
	<tr>
		<?php if($is_root): ?>
		<th class="ctrl"><?php echo $item->id ;?></th>
		<?php endif; ?>
		<td>
		<?php
			echo \Form::hidden("items[$item->id][id]", $item->id);
			echo \Form::input("items[$item->id][name]", $item->name, array('placeholder' => 'name', 'style' => 'width:100%;'.$opacity));
		?>
		</td>
		<td class="ctrl"><?php echo \Form::input("items[$item->id][order]", $item->order, array('size' => 3,'placeholder' => 'order', 'style' => 'text-align:center;'.$opacity)); ?></td>
		<td class="ctrl"><?php echo \Form::select("items[$item->id][is_available]", $item->is_available, [0 => '無効', 1 => '有効'], array('placeholder' => 'order')); ?></td>
		<td class="ctrl"><?php echo \Form::submit("delete[$item->id]", '削除', array('class' => 'button', 'onclick' => "return confirm('削除してよろしいですか？')", 'onkeypress' => "return confirm('削除してよろしいですか？')")); ?></td>
	</tr>
	<?php endforeach; ?>
	</table>
	</fieldset>

	<fieldset>
		<legend><?php echo Form::label('編集履歴用メモ', 'revision_comment', array('class'=>'control-label')); ?></legend>
		<?php echo Form::textarea('revision_comment', Input::post('revision_comment', isset($items->comment) ? $items->comment : ''), array('style'=>'width: 100%;')); ?>
	</fieldset>

	<div class="form-group">
		<?php
		if(@$is_revision):
			echo \Html::anchor($controller.'/options_revisions/'.$optname, '履歴一覧に戻る',array('class'=>'button'));
			echo \Html::anchor($controller.'/options/'.$optname, '編集画面に戻る',array('class'=>'button'));
		else:
			echo \Form::hidden($token_key, $token);
			echo \Form::hidden('mode', 'edit');
			echo \Form::submit('submit', '保存', array('class' => 'button primary'));
		endif;
		?>
	</div>
	</div>
	<?php echo \Form::close(); ?>

<?php else: ?>
<p>まだ設定されていません。</p>
<?php endif; ?>

<?php echo $include_tpl('inc_admin_footer.php'); ?>