<?php echo \Form::open(); ?>

<div class="form_group">
<fieldset>
	<legend>編集</legend>
	<table class="tbl">
	<tr>
	<th><?php echo $form->field('title')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('title')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('body')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('body')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('created_at')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('created_at')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('expired_at')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('expired_at')->set_template('{error_msg}{field}'); ?></td>
</tr>


	</table>
</fieldset>

<!--コントローラがリビジョンをサポートしている場合だけ有効です。適宜削除してください-->
<fieldset>
	<legend><?php echo \Form::label('編集履歴用メモ', 'revision_comment'); ?></legend>
	<?php echo \Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;')); ?>
</fieldset>
<!--リビジョン用編集メモここまで-->

<p>
	<?php
	echo $form->field('status')->set_template('{error_msg}{field}');

	if( ! @$is_revision): 
		echo \Form::hidden($token_key, $token);
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	endif;
	?>
</p>

</div>

<?php echo \Form::close(); ?>