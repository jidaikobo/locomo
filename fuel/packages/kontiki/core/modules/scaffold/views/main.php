<?php echo render('inc_header'); ?>

<?php echo \Form::open(); ?>
<h3><?php echo \Form::label('拡張oilコマンド書式', 'cmd'); ?></h3>
<p><?php echo \Form::textarea('cmd', Input::post('cmd', isset($cmd) ? $cmd : ''), array('style' => 'width:100%;height:5em;', 'placeholder'=>'拡張oilコマンド書式')); ?></p>
<p>
	<?php echo \Form::hidden($token_key, $token); ?>
	<?php echo \Form::submit('submit', 'Scaffold', array('class' => 'button primary')); ?>
</p>
<?php echo \Form::close(); ?>

<h2>使い方</h2>

<h3>拡張oil書式例</h3>
<textarea class="pre" style="width: 100%;height: 5em;">test(テストモジュール) title(表題):varchar[50] body(本文):text is_bool(真偽値):bool:null status(状態):varchar[20]:null created_at:datetime:null expired_at:datetime:null deleted_at:datetime:null</textarea>

<h3>足場組み</h3>
<p>oilふうコマンド書式をpostしたあと、<code>PROJPATH./modules</code>にモジュールが展開されます。</p>
<p>モジュール内のmigrationsのマイグレーションファイルと、モデルに書かれたデータベースのフィールドなど、必要な調整を加えた後、ターミナルでoilコマンドを実行します。oilコマンド実行前には、projects.iniの<code>cli_host</code>の値に注意してください。</p>
<textarea class="pre" style="width: 100%;">php oil refine migrate:up --modules=モジュール名</textarea>
<p>必要に応じて<code>PKGPATH.kontiki/modules</code>のファイルのパーミッションを調整してください。</p>

<h3>削除</h3>
<p>ターミナルで</p>
<textarea class="pre" style="width: 100%;">php oil refine migrate:down --modules=モジュール名</textarea>
<p>を実行した後、<code>PROJPATH./modules</code>に展開されたモジュールファイルを削除してください。</p>

<?php echo render('inc_footer'); ?>
