<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<fieldset>
	<legend>編集</legend>

	<table class="tbl">
	<tr>
	<th><?php echo \Form::label('title', 'title', array('class'=>'control-label')); ?></th>
	<td><?php echo \Form::input('title', Input::post('title', isset($item) ? $item->title : ''), array('class' => 'col-md-4 form-control', 'placeholder' => 'title')); ?></td>
</tr>

<tr>
	<th><?php echo \Form::label('body', 'body', array('class'=>'control-label')); ?></th>
	<td><?php echo \Form::input('body', Input::post('body', isset($item) ? $item->body : ''), array('class' => 'col-md-4 form-control', 'placeholder' => 'body')); ?></td>
</tr>

<tr>
	<th><?php echo \Form::label('user_id', 'user_id', array('class'=>'control-label')); ?></th>
	<td><?php echo \Form::input('user_id', Input::post('user_id', isset($item) ? $item->user_id : ''), array('class' => 'col-md-4 form-control', 'placeholder' => 'user_id')); ?></td>
</tr>

<tr>
	<th>カテゴリ</th>
	<td>
		<?php
		foreach(\Post\Model_Post::get_options('postcategories') as $key => $option):
			$checked = false; 
			if(
				(isset($item) && in_array($key, $item->postcategories)) ||
				(is_array(\Input::post('postcategories')) && array_key_exists($key, \Input::post('postcategories')))
			):
				$checked = true; 
			endif;
			echo '<label>'.\Form::checkbox('postcategories[]', $key, $checked ).$option.'</label>';
		endforeach;
		?>
	</td>
</tr>

	</table>
</fieldset>

	<!--コントローラがリビジョンをサポートしている場合だけ有効です。適宜削除してください-->
	<fieldset>
		<legend><?php echo Form::label('編集履歴用メモ', 'revision_comment', array('class'=>'control-label')); ?></legend>
		<?php echo Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;')); ?>
	</fieldset>
	<!--リビジョン用編集メモここまで-->
	
	<div class="form-group">
		<?php
		if( ! @$is_revision): 
			echo Form::hidden($token_key, $token);
			echo Form::submit('submit', '保存する', array('class' => 'button primary'));
		endif;
		?>
	</div>

<?php echo \Form::close(); ?>