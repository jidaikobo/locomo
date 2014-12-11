<?php
if ( ! \Input::get('create') && ! @$is_revision):
	echo \Form::open(array('method' => 'get'));
	echo \Form::input('likes[name]', \Input::get('likes')['name']);
	echo \Form::submit('submit', '検索', array('class'=>'button'));
	echo \Form::close();
endif;
?>

<?php if ($form): ?>
<?php echo \Form::open(\Uri::create(\Uri::current(), array(), \Input::get())); ?>
<div class="form_group">

<?php echo $form; ?>

<p>
	<?php
		echo \Form::hidden('is_locomo_bulk', true);
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	?>
</p>

</div>
<?php endif; ?>
<?php echo \Form::close() ?>
<?php echo \Pagination::create_links() ?>

