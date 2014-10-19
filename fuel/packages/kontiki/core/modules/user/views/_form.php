<?php echo \Form::open(); ?>

<div class="form_group">
	<h2>編集</h2>
	<fieldset>
		<legend>項目</legend>
		<div class="form-group">
		<?php
		echo $form->field('user_name')->set_template('{label}{required}');
		echo $form->field('user_name')->set_template('{error_msg}{field}');
		?>
		
		</div>
		<div class="form-group">
		<?php
		echo $form->field('display_name')->set_template('{label}{required}');
		echo $form->field('display_name')->set_template('{error_msg}{field}');
		?>
		</div>
				
		<div class="form-group">
		<?php echo $form->field('usergroup')->set_template('{label}{required}'); ?>
			<div>
				<?php echo $form->field('usergroup')->set_template('{fields} {field} {label}<br /> {fields}'); ?>
			</div>
		</div>
	
		<div class="form-group">
		<?php
		echo $form->field('password')->set_template('{label}{required}');
		echo $form->field('password')->set_template('{error_msg}{field}');
		?>
		</div>
	
		<div class="form-group">
		<?php
		echo $form->field('confirm_password')->set_template('{label}{required}');
		echo $form->field('confirm_password')->set_template('{error_msg}{field}');
		?>
		</div>
	
		<div class="form-group">
		<?php
		echo $form->field('email')->set_template('{label}{required}');
		echo $form->field('email')->set_template('{error_msg}{field}');
		?>
		</div>
	
		<div class="form-group">
		<?php
		echo $form->field('created_at')->set_template('{label}{required}');
		echo $form->field('created_at')->set_template('{error_msg}{field}');
		?>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo \Form::label('編集履歴用メモ', 'revision_comment'); ?></legend>
		<?php echo \Form::textarea('revision_comment', Input::post('revision_comment', isset($item->comment) ? $item->comment : ''), array('style'=>'width: 100%;')); ?>
	</fieldset>

<div class="button_group">
		<?php
		echo $form->field('status')->set_template('{field}');
		if(@$is_revision):
			echo Html::anchor('user/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
			echo Html::anchor('user/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
		else:
			echo \Form::hidden($token_key, $token);
			echo Html::anchor('user', '一覧に戻る',array('class'=>'button'));
			echo \Form::submit('submit', '保存', array('class' => 'button primary'));
		endif;
		?>
</div>
</div>

<?php echo \Form::close(); ?>