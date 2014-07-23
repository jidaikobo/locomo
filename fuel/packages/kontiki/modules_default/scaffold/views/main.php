<?php echo $include_tpl('inc_header.php'); ?>

<h2>書式例</h2>
<pre class="pre">php oil g model post title:varchar[50] body:text user_id:int</pre>

<?php echo \Form::open(array("class"=>"form-horizontal")); ?>
<fieldset>
	<legend><?php echo \Form::label('oilコマンド書式', 'cmd', array('class'=>'control-label')); ?></legend>
	<div class="form-group">
		<?php echo \Form::textarea('cmd', Input::post('cmd', isset($cmd) ? $cmd : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'oilコマンド書式')); ?>
	</div>

	<div class="form-group">
		<?php echo Form::hidden($token_key, $token); ?>
		<?php echo \Form::submit('submit', 'Scaffold', array('class' => 'btn btn-primary')); ?>
	</div>
</fieldset>
<?php echo \Form::close(); ?>

<h2>使い方</h2>

<h3>足場組み</h3>
<p>oilコマンド書式をpostしたあと、<code>PKGPATH.kontiki/modules</code>にモジュールが展開されます。</p>
<p>モジュール内のmigrationsのマイグレーションファイルと、モデルに書かれたデータベースのフィールドなど、必要な調整を加えた後、ターミナルでoilコマンドを実行します。</p>
<pre class="pre">php oil refine migrate:up --modules=モジュール名</pre>
<p>必要に応じて<code>PKGPATH.kontiki/modules</code>のファイルのパーミッションを調整してください。</p>

<h3>削除</h3>
<p>ターミナルで</p>
<pre class="pre">php oil refine migrate:down --modules=モジュール名</pre>
<p>を実行した後、<code>PKGPATH.kontiki/modules</code>に展開されたモジュールファイルを削除してください。</p>

<?php echo $include_tpl('inc_footer.php'); ?>
