<?php echo \Form::open(array("class"=>"form-horizontal")); ?>

<fieldset>

	<table class="tbl">
	###fields###
	</table>

	<!--コントローラがリビジョンをサポートしている場合だけ有効です。適宜削除してください-->
	<div class="form-group revision_comment">
		<?php echo Form::label('編集メモ', 'revision_comment', array('class'=>'control-label')); ?>
		<?php echo Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'')); ?>
	</div>
	<!--リビジョン用編集メモここまで-->
	
	<div class="form-group">
		<?php
		if( ! @$is_revision): 
			echo Form::hidden($token_key, $token);
			echo Form::submit('submit', '保存する', array('class' => 'button main'));
		endif;
		?>
	</div>

</fieldset>
<?php echo \Form::close(); ?>