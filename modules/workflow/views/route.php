<?php echo \Form::open(); ?>

<ul class="nlm">
<?php foreach($items as $item): ?>
	<li>
	<label>
	<?php
		echo \Form::radio('route', $item->id, (\Input::post('route') == $item->id || $route_id == $item->id));
		echo $item->name;
	?>
	</label>
	</li>
<?php endforeach; ?>
</ul>

<p>
<?php
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo Html::anchor($controller.'/edit/'.$item_id, '戻る',array('class'=>'button'));
echo \Form::submit('submit', '経路設定する', array('class' => 'button primary'));
?>
</p>

<?php echo \Form::close(); ?>
