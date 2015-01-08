	<div class="revision_comment">
		<h3><a href="javascript: void(0);" class="toggle_item disclosure"><?php echo \Form::label('編集履歴用メモ<span class="skip">エンターで入力欄を開きます</span>', 'revision_comment'); ?></a></h3>
	<?php echo \Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;','class'=>'hidden_item')); ?>
	</div>
