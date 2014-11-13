
<?php echo \Form::open(); ?>

<div class="form_group">
<fieldset>
	<legend>編集</legend>
	<table class="tbl">
		<?php /* echo $form; */ ?>
	<tr>
	<th><?php echo $form->field('title')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('title')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('controller')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('controller')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('body')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('body')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('seq')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('seq')->set_template('{error_msg}{field}'); ?></td>
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

	if( ! @$is_revision): 
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	endif;
	?>
</p>

</div>

<?php echo \Form::close(); ?>

<p>
	<?php
	if(@$is_revision):
		echo Html::anchor('help/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
		echo Html::anchor('help/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
	else:
		//コントローラがリビジョンをサポートしていない場合、この箇所だけで十分です。
		echo Html::anchor('help/view/'.$item->id, '表示',array('class'=>'button'));
		echo Html::anchor('help', '一覧に戻る',array('class'=>'button'));
	endif;
	?>
</p>

