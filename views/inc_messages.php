<?php if (Session::get_flash('message')): ?>
		<div id="alert_success" class="flash_alert alert_success" tabindex="0">
			<a id="anchor_alert_message" class="skip" tabindex="1" id="alert_message">インフォメーション:メッセージが次の行にあります</a>
			<p>
			<?php
				$flash_massages = Session::get_flash('message');
				if (is_array($flash_massages) && \Arr::is_multi($flash_massages)):
					foreach ($flash_massages as $v):
						echo '<a href="'.$v[1].'">'.$v[0].'</a>';
					endforeach;
				else:
					if ( ! is_array($flash_massages)):
						$flash_massages = array($flash_massages);
					endif;
					echo implode('</p><p>', e((array) $flash_massages));
				endif;
			?>
			</p>
		</div>
<?php endif; ?>

<?php if (Session::get_flash('success')): ?>
		<div id="alert_success" class="flash_alert alert_success" tabindex="0">
			<a id="anchor_alert_success" class="skip" tabindex="1" id="alert_success">インフォメーション:メッセージが次の行にあります</a>
			<p>
			<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
			</p>
		</div>
<?php endif; ?>

<?php if (Session::get_flash('error')): ?>
		<div id="alert_error" class="flash_alert alert_error" tabindex="0">
			<a id="anchor_alert_error" class="skip" tabindex="1">エラー:メッセージが次の行にあります</a>
			<?php $i = 0;
			foreach((array) Session::get_flash('error') as $id => $e):
			if ($id === 0):
				echo "<p>$e</p>" ;
			else:
				echo $i == 0 ? '<ul class="link">' : '';
				echo "<li><a href=\"#form_{$id}\" tabindex=\"1\">{$e}</a></li>";	$i ++;
			endif;
			endforeach;
			echo $i!=0 ? '</ul>': '' ;
			?>
		</div>
<?php endif; ?>
