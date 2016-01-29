<div class="legend calendar" aria-hidden=true>
<?php /* 音声読み上げ用に文字を削除する */
	// 外部表示
	echo \Request::active()->controller !== "\Controller_Scdl" ? '<span class="display_inline_block"><span class="text_icon reserve public"></span><span></span></span>' : '';


	foreach($repeat_kbs as $k => $v):
		echo $k != 0 ? '<span class="display_inline_block"><span class="text_icon schedule repeat_kb_'.$k.'"></span><span></span></span>' : '';
	endforeach;
	echo '<span class="display_inline_block"><span class="text_icon schedule dairi"></span><span></span></span>';

	foreach($detail_kbs as $k => $v):
		echo $k != 'unspecified_kb' ? '<span class="display_inline_block"><span class="text_icon schedule '.$k.'"></span><span></span></span>' : '';
	endforeach;

		
//	if(!\Request::is_hmvc()): //重要度 //to,ダッシュボードの施設予約
/*		foreach($importance_kbs as $k => $v):
			echo '<span class="display_inline_block"><span class="icon mark_importance kb_'.$k.'"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$k.'.png" alt=""></span><span></span></span>';
		endforeach;
*/
//	endif;

	echo \Request::active()->controller === "\Controller_Scdl" ? '<span class="display_inline_block"><span class="icon mark_private"><img src="'.Uri::base().'lcm_assets/img/system/mark_private.png" alt=""></span><span></span></span>' : '';
?>
</div><!-- /.legend.calendar -->