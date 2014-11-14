<?php echo \Form::open(); ?>

<div class="form_group">
<fieldset>
	<legend>編集</legend>
	<table class="tbl">
		<?php echo $item; ?>



	</table>
</fieldset>

<!--コントローラがリビジョンをサポートしている場合だけ有効です。適宜削除してください-->
<fieldset>
	<legend><?php echo \Form::label('編集履歴用メモ', 'revision_comment'); ?></legend>
	<?php echo \Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;')); ?>
</fieldset>
<!--リビジョン用編集メモここまで-->


</div>

<?php echo \Form::close(); ?>
