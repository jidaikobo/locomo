<?php echo render('inc_admin_header'); ?>

<h2>Editing <span class='muted'>Sample</span></h2>
<?php echo \Form::open(\Uri::create('sample/bulk', array(), \Input::get())); ?>

<div class="form_group">
<fieldset>

<?php echo $form; ?>

</fieldset>


<p>
	<?
	if( ! @$is_revision): 
		echo \Form::hidden($token_key, $token);
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	endif;
	?>
</p>

</div>

<?php echo \Form::close(); ?>
<p>
	<?php
		echo Html::anchor('sample', '一覧に戻る',array('class'=>'button'));
	?>
</p>

<?php echo $pagination; ?>


<?php echo render('inc_admin_footer'); ?>


