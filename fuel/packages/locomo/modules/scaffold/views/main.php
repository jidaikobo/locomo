
<?php echo \Form::open(); ?>
<h3><?php echo \Form::label('拡張oilコマンド書式', 'cmd'); ?></h3>
<p><?php echo \Form::textarea('cmd', Input::post('cmd', isset($cmd) ? $cmd : ''), array('style' => 'width:100%;height:10em;', 'placeholder'=>'拡張oilコマンド書式')); ?></p>
<p>
	<?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
	<?php echo \Form::submit('submit', 'Scaffold', array('class' => 'button primary')); ?>
</p>
<?php echo \Form::close(); ?>

<h2>使い方</h2>

<h3>拡張oil書式例</h3>
<textarea class="pre" style="width: 100%;height: 10em;">test(テストモジュール) 
title(表題):varchar[255]:default[title]
body(本文):text:default['']
is_bool(真偽値):bool:null:default[0]
created_at(作成日時):datetime:null
updated_at(更新日時):datetime:null
expired_at(有効期日):datetime:null
deleted_at(削除日):datetime:null
is_visible(可視属性):int:null
creator_id:int[5]
modifier_id:int[5]
workflow_status:enum[init,approve,reject,remand,finish]:null
</textarea>

<h3>足場組み</h3>
<p>拡張oil書式をpostしたあと、<code>APPPATH.modules</code>にモジュールが展開されます。</p>
<p>モジュール内のmigrationsのマイグレーションファイルと、モデルに書かれたデータベースのフィールドなど、必要な調整を加えた後、ターミナルでoilコマンドを実行します。</p>
<textarea class="pre" style="width: 100%;">php oil refine migrate:up --modules=モジュール名</textarea>

<h3>削除</h3>
<p>ターミナルで</p>
<textarea class="pre" style="width: 100%;">php oil refine migrate:down --modules=モジュール名</textarea>
<p>を実行した後、<code>APPPATH.modules</code>に展開されたモジュールファイルを削除してください。</p>