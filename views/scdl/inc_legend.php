<div class="legend calendar" aria-hidden=true>
<?php /* ここから音声読み上げ用に文字を削除する */
	foreach($repeat_kbs as $k => $v):
		echo $k != 0 ? '<span class="display_inline_block"><span class="text_icon schedule repeat_kb_'.$k.'"></span><span></span></span>' : '';
	endforeach;
	foreach($detail_kbs as $k => $v):
		echo $k != 'unspecified_kb' ? '<span class="display_inline_block"><span class="text_icon schedule '.$k.'"></span><span></span></span>' : '';
	endforeach;
//	if(!\Request::is_hmvc()): //重要度 //to,ダッシュボードの施設予約
		foreach($importance_kbs as $k => $v):
			echo '<span class="display_inline_block"><span class="icon mark_importance kb_'.$k.'"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$k.'.png" alt=""></span><span></span></span>';
		endforeach;
//	endif;
	echo $locomo['controller']['name'] === "\Controller_Scdl" ? '<span class="display_inline_block"><span class="icon mark_private"><img src="'.Uri::base().'lcm_assets/img/system/mark_private.png" alt=""></span><span></span></span>' : '';
?>
</div><!-- /.legend.calendar -->