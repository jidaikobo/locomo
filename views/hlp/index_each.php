<h1><?php echo $title ?></h1>
<ul>
<?php foreach($items as $k => $v): ?>
	<li><a href="<?php echo \Uri::create('hlp/view?action=').\Inflector::ctrl_to_safestr($k) ?>"><?php echo $v ?></a></li>
<?php endforeach; ?>
</ul>
