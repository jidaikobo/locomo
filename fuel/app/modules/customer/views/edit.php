<h2>Editing <span class='muted'>Customer</span></h2>

<?php echo $form->build(); ?>

<p>
	<?php
	if(@$is_revision):
		echo Html::anchor('customer/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
		echo Html::anchor('customer/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
	else:
		//コントローラがリビジョンをサポートしていない場合、この箇所だけで十分です。
		echo Html::anchor('customer/view/'.$item->id, '表示',array('class'=>'button'));
		echo Html::anchor('customer', '一覧に戻る',array('class'=>'button'));
	endif;
	?>
</p>


<style>
table, tr, th,td {
border: 1px solid #ccc;
}
