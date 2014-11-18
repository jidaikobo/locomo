
<?php echo \Form::open(); ?>

<h2>ワークフロー名：<?php echo $workflow_name; ?></h2>

<?php foreach($stepnum as $step): ?>
<fieldset>
	<legend>ステップ<?php echo $step; ?></legend>

	<div class="form-group">
		<?php echo \Form::label('ステップ名', "steps_{$step}_name"); ?>
		<?php echo \Form::input("steps[$step][name]", Input::post("steps[$step][name]", isset($steps[$step]) ? $steps[$step]['name'] : ''), array('id' => "form_steps_{$step}_name", 'placeholder'=>'ステップ名称（室長|事務局|事務局長）')); ?>
	</div>
<!--
	<div class="form-group">
		<?php echo \Form::label('承認条件', "steps_{$step}_condition"); ?>
		<?php
			$conditions = array('none' => '選択してください', 'single' => '一名の承認', 'all' => '全員の承認');
			echo \Form::select("steps[$step][condition]", Input::post("steps[$step][condition]", isset($steps[$step]) ? $steps[$step]['condition'] : ''), $conditions, array('id' => "form_steps_{$step}_condition", 'class' => 'col-md-4 form-control'));
		?>
	</div>
-->
	<?php echo \Form::hidden("steps[$step][condition]", 'single'); ?>

	<div class="form-group">
		<?php echo \Form::label('承認者', "steps_{$step}_allowers"); ?>
		<em class="exp">承認ユーザIDをカンマ区切りで入力してください。このうちの一名が承認したら承認プロセスが進みます。</em>
		<?php echo \Form::input("steps[$step][allowers]", Input::post("steps[$step][allowers]", isset($steps[$step]) ? $steps[$step]['allowers'] : ''), array('id' => "form_steps_{$step}_allowers", 'placeholder'=>'1,2,3')); ?>
	</div>

	<div class="form-group">
		<?php echo \Form::label('承認アクション', "steps_{$step}_action"); ?>
		<em class="exp">承認アクションをカンマ区切りで入力してください。入力できるアクションは現在は<code>mail</code>です。</em>
		<?php echo \Form::input("steps[$step][action]", Input::post("steps[$step][action]", isset($steps[$step]) ? $steps[$step]['action'] : ''), array('id' => "steps_{$step}_action", 'placeholder'=>'mail')); ?>
	</div>
</fieldset>
<?php endforeach; ?>

<p>
	<?php
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::hidden('allstep', $allstep);
		echo Html::anchor('workflowadmin/index_admin', '一覧に戻る',array('class'=>'button'));
		echo \Form::submit('add_step', '承認ステップを1つ増やす', array('class' => 'button primary'));
		echo \Form::submit('submit', '保存', array('class' => 'button primary'));
	?>
</p>

<?php echo \Form::close(); ?>

