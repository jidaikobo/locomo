<?php
// モジュール／コントローラトップ
if ( ! isset($is_main_action)):
	$html = '';
	$html.= '<h1>'.$title.'</h1>';
	if ($actionset):
		foreach($actionset as $k => $v):
			$html.= '<h2>'.$k::$locomo['nicename'].'</h2>';
			$html.= '<table class="tbl2">';
			foreach($v as $kk => $vv):
			if ($kk == 'order') continue;
				foreach($vv as $kkk => $vvv):
					if (\Arr::get($vvv, 'show_at_top') == false) continue;
					if (\Arr::get($vvv, 'urls') == false) continue;
						$html.= '<tr>';
							$html.= '<th class="ctrl">';
							$html.= '<ul style="text-align: left;"><li>'.join('</li><li>',$vvv['urls']).'</ul>';
							$html.= '</th>';
							$html.= '<td>'.$vvv['explanation'].'</td>';
						$html.= '</tr>';
				endforeach;
			endforeach;
			$html.= '</table>';
		endforeach;
	else:
	endif;
	echo $html;
else:
// 管理ホーム
?>
	<?php if (\Request::is_hmvc()): ?>
		<ul>
		<?php foreach($locomo['controllers'] as $k => $v): ?>
		<?php if (\Arr::get($v, 'show_at_menu') == false) continue; ?>
		<li><a href="<?php echo \Uri::create('sys/admin/').\Inflector::ctrl_to_safestr($k) ?>"><?php echo $v['nicename'] ?></a></li>
		<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<h1>管理ホーム</h1>
		<table class="tbl2">
		<?php foreach($locomo['controllers'] as $k => $v): ?>
		<?php if (\Arr::get($v, 'show_at_menu') == false) continue; ?>
		<tr>
			<th><a href="<?php echo \Uri::create('sys/admin/').\Inflector::ctrl_to_safestr($k) ?>"><?php echo $v['nicename'] ?></a></th>
			<td><?php echo @$v['explanation'] ?></td>
		</tr>
		<?php endforeach; ?>
		</table>
	<?php endif; ?>
<?php endif; ?>
