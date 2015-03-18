<h1><?php echo $title ?></h1>
<div class="lcm_form view">
<?php
echo $plain;
?>

<div class="input_group">
	<h2>ユーザ</h2>
	<div class="field">
	<?php
	foreach ($item->user as $uid => $v):
		echo $v->display_name;
	endforeach;
	?>
	</div><!--/.field-->
</div><!--/.input_group-->

</div><!-- /.lcm_form.view -->