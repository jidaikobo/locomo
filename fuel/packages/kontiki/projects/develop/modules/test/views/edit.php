<?php echo render('inc_admin_header'); ?>

<h2>Editing <span class='muted'>Test</span></h2>

<?php echo render('_form'); ?>

<p>
	<?php
	if(@$is_revision):
		echo Html::anchor('test/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
		echo Html::anchor('test/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
	else:
		//コントローラがリビジョンをサポートしていない場合、この箇所だけで十分です。
		echo Html::anchor('test/view/'.$item->id, '表示',array('class'=>'button'));
		echo Html::anchor('test', '一覧に戻る',array('class'=>'button'));
	endif;
	?>
</p>

<?php echo render('inc_admin_footer'); ?>
