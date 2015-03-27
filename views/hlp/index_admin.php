<h1><?php echo $title ?></h1>
<table class="tbl">
<?php foreach($locomo['controllers'] as $k => $v): ?>
<?php if (\Arr::get($v, 'show_at_menu') == false) continue; ?>
<tr>
	<th><a href="<?php echo \Uri::create('hlp/view?action=').\Inflector::ctrl_to_safestr($k) ?>"><?php echo $v['nicename'] ?></a></th>
	<td><?php echo @$v['explanation'] ?></td>
</tr>
<?php endforeach; ?>
</table>
