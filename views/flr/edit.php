<?php
if (\Request::active()->action == 'purge_dir')
{
	echo '<p>ディレクトリを削除すると、そのディレクトリの中に含まれるものもすべて削除されます。この削除は取り消しができません。注意してください。</p>';
}

echo \Form::open();
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
?>
<div class="submit_button">
<?php
	$str = \Request::active()->action == 'purge_dir' ? '完全に削除する' : '保存' ;
	echo \Form::submit('submit', $str, array('class' => 'button primary'));
?>
</div>
<?php echo \Form::close();  ?>
