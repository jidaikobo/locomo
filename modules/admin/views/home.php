<?php
//モジュール／コントローラトップ
if ( ! isset($is_admin_home)):
	$html = '';
	foreach($mod_or_ctrl as $k => $v):
		$html.= '<h2>'.$v['nicename'].'</h2>';
		$html.= '<table class="tbl2">';
		foreach($v['actionset'] as $kk => $vv):
			foreach($vv as $kkk => $vvv):
				if (\Arr::get($vvv, 'show_at_top') == false) continue;
					$html.= '<tr>';
						$html.= '<th class="ctrl">';
						$html.= '<ul><li>'.join('</li><li>',$vvv['urls']).'</ul>';
						$html.= '</th>';
						$html.= '<td>'.$vvv['explanation'].'</td>';
					$html.= '</tr>';
			endforeach;
		endforeach;
		$html.= '</table>';
	endforeach;
	echo $html;
else:
//管理ホーム
	$html = '';
	$html.= '<ul>';
	foreach($locomo['controllers'] as $k => $v):
		if (\Arr::get($v, 'show_at_menu') == false) continue;
		echo '<li><a href="'.\Uri::create('admin/home/').trim($k, '\\').'">'.$v['nicename'].'</a></li>';
	endforeach;
	$html.= '</ul>';
	echo $html;
endif;
?>