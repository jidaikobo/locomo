<h1><?php echo $title ?></h1>

<ul>
	<li>ファイルやディレクトリの実際の状況とデータベースの内容に矛盾が生じているようでしたら、これを実行してください。</li>
	<li>また、この同期によって、ファイルシステム上にある不正なファイル名（全角文字等）が修正されます。</li>
	<li>ファイルやディレクトリの数によっては時間がかかることがあります。</li>
	<li>この処理は、時々自動的に行われますので、原則、明示的な実行は不要です。</li>
</ul>

<?php
echo \Form::open(array('class' => 'lcm_form form_group'));
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
?>
<div class="submit_button">
	<?php echo \Form::submit('submit', '同期する', array('class' => 'button primary')); ?>
</div>
<?php echo \Form::close();?>
