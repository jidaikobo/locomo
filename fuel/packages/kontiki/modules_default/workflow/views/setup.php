<?php echo $include_tpl('inc_header.php'); ?>

<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<h2>ワークフロー名：<?php echo $workflow_name; ?></h2>

<?php foreach($stepnum as $step): ?>
<fieldset>
	<legend>ステップ<?php echo $step; ?></legend>

	<div class="form-group">
		<?php echo \Form::label('ステップ名', "steps_{$step}_name", array('class'=>'control-label')); ?>
		<?php echo \Form::input("steps[$step][name]", Input::post("steps[$step][name]", isset($steps[$step]) ? $steps[$step]['name'] : ''), array('id' => "form_steps_{$step}_name", 'class' => 'col-md-4 form-control', 'placeholder'=>'ステップ名称（室長|事務局|事務局長）')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('承認条件', "steps_{$step}_condition", array('class'=>'control-label')); ?>
		<?php
			$conditions = array('none' => '選択してください', 'single' => '一名の承認', 'all' => '全員の承認');
			echo \Form::select("steps[$step][condition]", Input::post("steps[$step][condition]", isset($steps[$step]) ? $steps[$step]['condition'] : ''), $conditions, array('id' => "form_steps_{$step}_condition", 'class' => 'col-md-4 form-control'));
		?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('承認者', "steps_{$step}_allowers", array('class'=>'control-label')); ?>
		<em class="exp">承認ユーザIDをカンマ区切りで入力してください。</em>
		<?php echo \Form::input("steps[$step][allowers]", Input::post("steps[$step][allowers]", isset($steps[$step]) ? $steps[$step]['allowers'] : ''), array('id' => "form_steps_{$step}_allowers", 'class' => 'col-md-4 form-control', 'placeholder'=>'1,2,3')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('承認アクション', "steps_{$step}_actions", array('class'=>'control-label')); ?>
		<em class="exp">承認アクションをカンマ区切りで入力してください。入力できるアクションは現在は<code>mail</code>です。</em>
		<?php echo \Form::input("steps[$step][actions]", Input::post("steps[$step][actions]", isset($steps[$step]) ? $steps[$step]['actions'] : ''), array('id' => "steps_{$step}_actions", 'class' => 'col-md-4 form-control', 'placeholder'=>'mail')); ?>
	</div>
</fieldset>
<?php endforeach; ?>

<p>
	<?php
		echo Form::hidden($token_key, $token);
		echo Form::hidden('allstep', $allstep);
		echo Form::submit('add_step', '承認ステップを1つ増やす', array('class' => 'button main'));
		echo Form::submit('submit', '保存', array('class' => 'button main'));
	?>
</p>

<?php echo \Form::close(); ?>
<p>
	<?php
		echo Html::anchor('workflow/view/'.$workflow_id, '表示',array('class'=>'button'));
		echo Html::anchor('workflow/index_admin', '一覧に戻る',array('class'=>'button'));
	?>
</p>

<?php echo $include_tpl('inc_footer.php'); ?>
