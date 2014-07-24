<?php echo $include_tpl('inc_header.php'); ?>

<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<h2>ワークフロー名：<?php echo $workflow_name; ?></h2>

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
</fieldset>

<p>
	<?php
		echo Form::hidden($token_key, $token);
		echo Form::submit('submit', 'ワークフロー処理を進める', array('class' => 'button main'));
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
