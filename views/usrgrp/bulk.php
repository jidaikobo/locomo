<?php if (isset($search_form)) echo $search_form; ?>


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

