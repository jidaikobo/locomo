<?php
//echo \Form::open(array('enctype' => 'multipart/form-data', 'class' => 'lcm_form form_group'));
echo \Form::open(array('class' => 'lcm_form form_group'));
?>

<h1>検索センターの同期</h1>

<!--form_group-->
<div class="lcm_form form_group">

<?php if ($cnt): ?>
<ul>
	<li><?php echo $cnt ?>件を同期しました。</li>
</ul>
<?php else: ?>
<ul>
	<li>同期はそれなりに時間を要します。</li>
	<li>検索結果が正しくない時に実行するようにしてください。</li>
</ul>

<div class="submit_button">
	<?php

	if ( ! @$is_revision):
		echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
		echo \Form::submit('submit', '同期する', array('class' => 'button primary'));
	endif;
	?>
</div>
<?php endif; ?>

</div><!--/form_group-->

<?php echo \Form::close(); ?>
