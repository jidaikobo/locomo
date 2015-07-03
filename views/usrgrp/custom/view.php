<h1><?php echo $title ?></h1>
<div class="lcm_form view">
<?php
echo $plain;
?>

<div class="input_group">
	<h2>ユーザ</h2>
	<div class="field">
	<?php if ($item->user): ?>
		<ul>
		<?php
		foreach ($item->user as $uid => $v):
			echo '<li>'.$v->display_name.'</li>';
		endforeach;
		?>
		</ul>
	<?php else: ?>
		<p>所属ユーザはいません。</p>
	<?php endif; ?>
	</div><!--/.field-->
</div><!--/.input_group-->

</div><!-- /.lcm_form.view -->