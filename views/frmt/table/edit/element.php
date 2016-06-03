<?php echo \Form::open(array('method' => 'post', 'action' => '', 'class' => 'lcm_form form_group')); ?>

	<div id="controller_bar">
		<a href="#" class="show_display_name     button small" title="要素名を表示">name</a>
		<a href="#" class="add_element           button small" title="要素の追加">要素の追加</a>
		<a href="#" class="delete_element        button small" title="要素の削除">要素の削除</a>
		<input      class="save                  button small" title="保存" type="submit" value="保存">

		<a href="#" class="show_controller       button small" title="要素の編集を表示">要素の編集</a>
	</div>

<div class="input_group">
<ul id="elements">
	<?php echo \Locomo\Presenter_Frmt_Table_Edit_Element::setElements($item->element); ?>
</ul>
</div>

<?php
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>

<div id="controllers_wrapper">
	<h1 class="title">要素の編集</h1>
	<div id="controller">
		<?php echo \Locomo\Presenter_Frmt_Table_Edit_Element::setController($model_properties); ?>
	</div>
</div>


<?php /*
<div id="controllers_wrapper">

	<div class="input_group">
		<h2><label for="controller_name" id="label_controller_name">列ラベル</label></h2>
		<div class="field">
			<input type="text" value="" name="name" id="controller_name" title="列のラベルを変更" class="name tabindex_ctrl" size="20">
		</div>
	</div>

	<div class="input_group">
		<h2><label for="controller_txt" id="label_txt">表示データ</label></h2>
		<div class="field">
			<textarea name="txt" id="controller_txt" class="txt tabindex_ctrl"></textarea>
		</div>
	</div>

	<h2><label for="controller_txt" id="label_txt">使用可能データ</label></h2>
	<div id="model_properties_wrapper">
		<h1 class="title"></h1>
		<?php echo \Locomo\Presenter_Frmt_Table_Edit_Element::modelPropertiesForm($model_properties); ?>
	</div>
</div>
 */ ?>


<ul id="element_template">
	<?php echo \Locomo\Presenter_Frmt_Table_Edit_Element::templateElement(); ?>
</ul>



