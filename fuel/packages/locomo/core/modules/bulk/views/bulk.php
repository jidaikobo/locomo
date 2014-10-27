<?php echo render('inc_admin_header'); ?>

<h2>Editing <span class='muted'>Sample</span></h2>

<?php
	echo \Form::open(array('method' => 'get'));
	echo \Form::input('likes[name]', \Input::get('likes')['name']);
	echo \Form::submit('submit', '検索', array('class'=>'button'));
	echo \Form::close();
?>



<?php if($form): ?>
<?php echo \Form::open(\Uri::create('', array(), \Input::get())); ?>
<div class="form_group">

<?php echo $form; ?>

<p>
	<?php
	if( ! @$is_revision): 
		echo \Form::hidden($token_key, $token);
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	endif;
	?>
</p>

</div>
<?php endif; ?>

<?php echo \Form::close(); ?>

<?php echo $pagination; ?>

<?php echo render('inc_admin_footer'); ?>


