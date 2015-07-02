<?php if ($form): ?>
<?php if (isset($search_form)) echo $search_form; ?>

<!--ページネーション-->
<div class="index_toolbar clearfix">
<?php echo \Pagination::create_links(); ?>
</div>

<!--一覧-->
<?php echo \Form::open(array('action' => \Uri::create(\Uri::current(), array(''), \Input::get()), 'class'=>'lcm_form form_group')); ?>
<?php echo $form; ?>
<div class="submit_button">
	<?php
		echo \Form::hidden('is_locomo_bulk', true);
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
	?>
</div>
<?php endif; ?>
<?php echo \Form::close() ?>
