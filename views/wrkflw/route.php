<h1><?php echo $obj->$subject ?>のルート設定</h1>

<?php echo \Form::open(); ?>

<fieldset>
	<legend>ルート候補</legend>
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
</fieldset>

<div class="submit_button">
	<?php
	echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
	echo \Form::submit('submit', '経路設定する', array('class' => 'button primary'));
	?>
</div>

<?php echo \Form::close(); ?>
