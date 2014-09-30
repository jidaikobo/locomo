<?php echo \Form::open(); ?>

<div class="form_group">
<fieldset>
	<legend>編集</legend>
	<table class="tbl">
	<tr>
	<th><?php echo \Form::label('title', 'title'); ?></th>
	<td><?php echo \Form::input('title', Input::post('title', isset($item) ? $item->title : ''), array('placeholder' => 'title')); ?></td>
</tr>

<tr>
	<th><?php echo \Form::label('body', 'body'); ?></th>
	<td><?php echo \Form::input('body', Input::post('body', isset($item) ? $item->body : ''), array('placeholder' => 'body')); ?></td>
</tr>

<!--
<tr>
	<th><?php echo \Form::label('status', 'status'); ?></th>
	<td><?php echo \Form::input('status', Input::post('status', isset($item) ? $item->status : ''), array('placeholder' => 'status')); ?></td>
</tr>

<tr>
	<th><?php echo \Form::label('created_at', 'created_at'); ?></th>
	<td><?php echo \Form::input('created_at', Input::post('created_at', isset($item) ? $item->created_at : ''), array('placeholder' => 'created_at')); ?></td>
</tr>

<tr>
	<th><?php echo \Form::label('expired_at', 'expired_at'); ?></th>
	<td><?php echo \Form::input('expired_at', Input::post('expired_at', isset($item) ? $item->expired_at : ''), array('placeholder' => 'expired_at')); ?></td>
</tr>

<tr>
	<th><?php echo \Form::label('deleted_at', 'deleted_at'); ?></th>
	<td><?php echo \Form::input('deleted_at', Input::post('deleted_at', isset($item) ? $item->deleted_at : ''), array('placeholder' => 'deleted_at')); ?></td>
</tr>
-->

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
	if( ! @$is_revision): 
		echo \Form::hidden($token_key, $token);
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	endif;
	?>
</p>

</div>

<?php echo \Form::close(); ?>