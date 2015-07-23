<h1>ファイル詳細</h1>
<?php echo $breadcrumbs ;?>
<?php echo $plain; ?>
<?php
if (\Auth::is_root()):
	echo '<h2>ファイル情報</h2>';
	echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
	var_dump( $file_info );
	echo '</textarea>' ;
endif;
?>