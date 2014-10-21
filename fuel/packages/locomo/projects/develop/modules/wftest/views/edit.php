<?php echo render('inc_admin_header'); ?>

<h2>Editing <span class='muted'>Wftest</span></h2>

<?php echo render('_form'); ?>

<p>
	<?php
	if(@$is_revision):
		echo Html::anchor('wftest/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
		echo Html::anchor('wftest/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
	else:
		//コントローラがリビジョンをサポートしていない場合、この箇所だけで十分です。
		echo Html::anchor('wftest/view/'.$item->id, '表示',array('class'=>'button'));
		echo Html::anchor('wftest', '一覧に戻る',array('class'=>'button'));
	endif;
	?>
</p>

<?php echo render('inc_admin_footer'); ?>
