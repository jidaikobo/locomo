<?php
// モジュール／コントローラトップ
if ( ! isset($is_main_action)):
	$html = '';
	$html.= '<h1>'.$title.'</h1>';
	if ($actionsets):
		foreach($actionsets as $ctrl => $actionset):
			// 表示できるようなアクションセットがあるかどうかはまわしてみるまでわからないので、一度まわす。
			$table = '';
			foreach($actionset as $action => $v):
				if ($action == 'order') continue;
				if (\Arr::get($v, 'show_at_top') == false) continue;
				if (\Arr::get($v, 'urls') == false) continue;
					$table.= '<tr>';
						$table.= '<th class="ctrl">';
						$table.= '<ul style="text-align: left;"><li>'.join('</li><li>',$v['urls']).'</ul>';
						$table.= '</th>';
						$table.= '<td>'.$v['explanation'].'</td>';
					$table.= '</tr>';
			endforeach;

			// 表示できるようなアクションセットがあれば、見出しを付けて表示
			$html.= $table ? '<h2>'.\Util::get_locomo($ctrl, 'nicename').'</h2><table class="tbl2">'.$table.'</table>' : '';
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
