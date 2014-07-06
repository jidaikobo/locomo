<?php echo \View::forge(PKGPATH.'kontiki/views/inc_header.php'); ?>

<?php
echo isset($explanation) ? $explanation : '' ;
?>

<h2>書式例</h2>
<p>php oil g model post title:varchar[50] body:text user_id:int</p>

<?php echo \Form::open(array("class"=>"form-horizontal")); ?>
<fieldset>
	<div class="form-group">
		<?php echo \Form::label('oilコマンド書式', 'cmd', array('class'=>'control-label')); ?>
		<?php echo \Form::textarea('cmd', Input::post('cmd', isset($cmd) ? $cmd : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'oilコマンド書式')); ?>
	</div>

	<div class="form-group">
		<label class='control-label'>&nbsp;</label>
		<?php echo Form::hidden($token_key, $token); ?>
		<?php echo \Form::submit('submit', 'Scaffold', array('class' => 'btn btn-primary')); ?>
	</div>
</fieldset>
<?php echo \Form::close(); ?>

<?php echo \View::forge(PKGPATH.'kontiki/views/inc_footer.php'); ?>
