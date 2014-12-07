<?php echo \Form::open(); ?>

<div class="form_group">
<h2 class="skip"><?php echo \Form::label('拡張oilコマンド書式', 'cmd'); ?></h2>
<p>
	<?php echo \Form::textarea('cmd', Input::post('cmd', isset($cmd) ? $cmd : ''), array('style' => 'width:100%;height:10em;', 'placeholder'=>'拡張oilコマンド書式')); ?><br />
	<?php echo \Form::select('type', null, array('all' => 'モジュールとしてすべてのファイルを生成する', 'all2' => '通常のコントローラ一式としてすべてのファイルを生成する', 'model' => 'モデルとマイグレーションのみ生成する', 'view' => 'viewsのファイル群のみ生成する')); ?>
</p>
<div class="submit_button">
	<?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
	<?php echo \Form::submit('submit', 'Scaffold', array('class' => 'button primary')); ?>
</div>
<?php echo \Form::close(); ?>
</div>

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
workflow_status:enum[before_progress,in_progress]:null
</textarea>

<h3>足場組み</h3>
<p>拡張oil書式をpostしたあと、所定の場所にファイル群が展開されます。</p>
<p>migrationsのマイグレーションファイルと、モデルに書かれたデータベースのフィールドなど、必要な調整を加えた後、ターミナルでoilコマンドを実行します。</p>
<textarea class="pre" style="width: 100%;">php oil refine migrate:up[--modules=モジュール名]</textarea>

<h3>削除</h3>
<p>ターミナルで</p>
<textarea class="pre" style="width: 100%;">php oil refine migrate:down[--modules=モジュール名]</textarea>
<p>を実行した後、展開されたファイル群を削除してください。</p>

<h3>オーバライド</h3>
<p><code>LOCOMOPATH./modules/scaffold/classes/helper/</code>から、<code>APPPATH.classes/scaffold/helper/</code>に必要なファイルを移動すれば、処理をオーバライドできます。</p>
<p><code>LOCOMOPATH./modules/scaffold/module_templates/</code>から、<code>APPPATH.locomo/modules/scaffold/module_templates/</code>に必要なファイルを移動すれば、基礎テンプレートのみを変更できます。</p>


