<h1>削除</h1>
<?php echo \Form::open(); ?>
<div class="form_group">
<h2 class="skip"><?php echo \Form::label('削除できる項目', 'candidates'); ?></h2>

<?php
$basepath = APPPATH.'logs/scffld/';
if ($files = \File::read_dir($basepath, 1))
{
	$candidates = array();
	foreach ($files as $dir => $file)
	{
		$target = $basepath.$dir.'files.php';
		if( ! file_exists($target)) continue;
		require($target); // fetch $scfflds
		$candidates[substr($dir, 0, -1)] = $scfflds;
	}

	if ($candidates)
	{
		$html = '<table class="tbl">';
		foreach ($candidates as $k => $v)
		{
			$html.= '<tr><th><label><input type="checkbox" name="candidates" value="'.$k.'" />'.$k.'</label></th>';
			$html.= '<td><ul>';
			foreach ($v as $vv)
			{
				$html.= '<li>';
			}
			$html.= '</ul></td></tr>';
		}
		$html.= '<table>';
		echo $html;
	}

}



?>

<p><?php echo \Form::textarea('cmd', Input::post('cmd', \Session::get_flash('cmd_raw')), array('style' => 'width:100%;height:10em;', 'placeholder'=>'拡張oilコマンド書式')); ?><br /></p>

<p><?php echo \Form::select('type', Input::post('type', \Session::get_flash('type')), array('app' => '通常のコントローラ一式としてすべてのファイルを生成する', 'module' => 'モジュールとしてすべてのファイルを生成する', 'model' => 'モデルとマイグレーションのみ生成する', 'view' => 'viewsとpresenterのファイル群のみ生成する')); ?></p>

<p><?php echo \Form::select('model', Input::post('model', \Session::get_flash('model')), array('Model_Base' => '\Orm\Model', 'Model_Base_Soft' => '\Orm\Model_Soft', 'Model_Base_Temporal' => '\Orm\Model_Temporal (experiment)', 'Model_Base_Nestedset' => '\Orm\Model_Nestedset (experiment)')); ?>（モデル生成時のみ）</p>

<div class="submit_button">
	<?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
	<?php echo \Form::submit('submit', 'Scaffold', array('class' => 'button primary')); ?>
</div>
<?php echo \Form::close(); ?>
</div>
