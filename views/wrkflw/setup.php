<?php echo \Form::open(); ?>

<h2>ワークフロー名：<?php echo $workflow_name; ?></h2>

<fieldset>
	<legend>執筆者設定</legend>

	<table class="tbl">
	<tr>
		<th class="ctrl"><?php echo \Form::label('執筆者グループ', "writers_groups"); ?></th>
		<td>
			<em class="exp">執筆者グループIDをカンマ区切りで入力してください。</em>
			<?php
			echo \Form::input(
				"steps[writers][groups]",
				Input::post("steps[writers][groups]", \Arr::get($steps, 'writers.groups', '')),
				array('id' => "writers_groups", 'placeholder'=>'1,2,3')
			); ?>
		</td>
	</tr>
	<tr>
		<th><?php echo \Form::label('執筆者', "writers_users"); ?></th>
		<td>
			<em class="exp">執筆者ユーザIDをカンマ区切りで入力してください。</em>
			<?php
			echo \Form::input(
				"steps[writers][users]",
				Input::post("steps[writers][users]", \Arr::get($steps, 'writers.users', '')),
				array('id' => "writers_users", 'placeholder'=>'1,2,3')
			); ?>
		</td>
	</tr>
	</table>
</fieldset>

<?php foreach($stepnum as $step): ?>
<fieldset>
	<legend>ステップ<?php echo $step; ?></legend>

	<table class="tbl">
	<tr>
		<th><?php echo \Form::label('ステップ名', "steps_{$step}_name"); ?></th>
		<td>
		<em class="exp">ステップ名は必須です。ステップ名がないと値は保存されません。</em>
		<?php
		echo \Form::input(
			"steps[allowers][$step][name]",
			Input::post("steps[allowers][$step][name]", \Arr::get($steps, "allowers.{$step}.name", '')),
			array('id' => "form_steps_{$step}_name", 'placeholder'=>'ステップ名称（室長|事務局|事務局長）')
		); ?>
		</td>
	</tr>
	<tr>
		<th><?php echo \Form::label('承認者グループ', "steps_{$step}_groups"); ?></th>
		<td>
		<em class="exp">承認ユーザグループIDをカンマ区切りで入力してください。グループの一名が承認したら承認プロセスが進みます。</em>
		<?php
		echo \Form::input(
			"steps[allowers][$step][groups]",
			Input::post("steps[allowers][$step][groups]", \Arr::get($steps, "allowers.{$step}.groups", '')),
			array('id' => "form_steps_{$step}_groups", 'placeholder'=>'1,2,3')
		); ?>
		</td>
	</tr>
	<tr>
		<th><?php echo \Form::label('承認者', "steps_{$step}_users"); ?></th>
		<td>
		<em class="exp">承認ユーザIDをカンマ区切りで入力してください。このうちの一名が承認したら承認プロセスが進みます。</em>
		<?php
		echo \Form::input(
			"steps[allowers][$step][users]",
			Input::post("steps[allowers][$step][users]", \Arr::get($steps, "allowers.{$step}.users", '')),
			array('id' => "form_steps_{$step}_users", 'placeholder'=>'1,2,3')
		); ?>
		</td>
	</tr>
<!--
	<tr>
		<th><?php echo \Form::label('承認アクション', "steps_{$step}_action"); ?></th>
		<td>
		<em class="exp">承認アクションをカンマ区切りで入力してください。入力できるアクションは現在は<code>mail</code>です。</em>
		<?php
		echo \Form::input(
			"steps[allowers][$step][action]",
			Input::post("steps[allowers][$step][action]", \Arr::get($steps, "allowers.{$step}.action", '')),
			array('id' => "form_steps_{$step}_action", 'placeholder'=>'mail')
		); ?>
		</td>
	</tr>
-->
	</table>

<!--
		<?php echo \Form::label('承認条件', "steps_{$step}_condition"); ?>
		<?php
			$conditions = array('none' => '選択してください', 'single' => '一名の承認', 'all' => '全員の承認');
			echo \Form::select("steps[$step][condition]", Input::post("steps[$step][condition]", isset($steps[$step]) ? $steps[$step]['condition'] : ''), $conditions, array('id' => "form_steps_{$step}_condition", 'class' => 'col-md-4 form-control'));
		?>
-->
	<?php echo \Form::hidden("steps[allowers][$step][condition]", ''); ?>
	<?php echo \Form::hidden("steps[allowers][$step][action]", ''); ?>
</fieldset>
<?php endforeach; ?>

<p>
	<?php
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::hidden('allstep', $allstep);
		echo \Form::submit('add_step', '承認ステップを1つ増やす', array('class' => 'button primary'));
		echo \Form::submit('submit', '保存', array('class' => 'button primary'));
	?>
</p>

<?php echo \Form::close(); ?>
