<h1><?php echo $title ?></h1>
<?php
echo \Form::open(array('action' => $action, 'class'=>'lcm_form form_group'));
echo $plain;
$pk = $item::primary_key()[0];
echo \Form::hidden('id', $item->$pk);
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
?>
<div class="submit_button">
	<?php
		echo \Form::submit('submit', '完全に削除する', array('class' => 'button primary'));
	?>
</div>
<?php echo \Form::close(); ?>
