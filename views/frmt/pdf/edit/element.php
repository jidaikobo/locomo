<?php echo \Form::open(array('method' => 'post', 'action' => '')); ?>

	<div id="controller_bar">
		<div class="btn_group">
			<a href="#" class="zoom_in           button small" title="拡大">＋</a>
			<a href="#" class="zoom_reset        button small" title="リセット">100%</a>
			<a href="#" class="zoom_out          button small" title="縮小">ー</a>
		</div>
		<a href="#" class="show_shade            button small" title="要素の外枠を表示">□</a>
		<a href="#" class="show_display_name     button small" title="要素名を表示">name</a>
		<a href="#" class="show_text             button small" title="内容を表示">内容</a>
		<a href="#" class="left_align_element    button small" title="要素を左寄せ">要素の左寄せ</a>
		<a href="#" class="centering_element     button small" title="要素を中央配置">要素の中央配置</a>
		<a href="#" class="right_align_element   button small" title="要素を右寄せ">要素の右寄せ</a>
		<a href="#" class="add_element           button small" title="要素の追加">要素の追加</a>
		<a href="#" class="delete_element        button small" title="要素の削除">要素の削除</a>
		<input      class="save                  button small" title="保存" type="submit" value="保存">

		<a href="#" class="show_element_seq      button small" title="印刷順を表示">印刷順</a>
		<a href="#" class="show_controller       button small" title="要素の編集を表示">要素の編集</a>
	</div>

	<div id="print_wrapper">

		<!-- 用紙 line 用 -->
		<svg class="print">
			<!--<line x1="0" y1="0" x2="840" y2="1188" stroke="black" stroke-width="1"/>-->
		</svg>

		<!-- 用紙 -->
		<div id="print_div" class="print">

			<input type="hidden" id="print_width"  value="<?php echo $print_width ?>">
			<input type="hidden" id="print_height" value="<?php echo $print_height ?>">

			<!-- form -->
			<?php echo $setElements($item->element); ?>
			<!-- // form -->

		</div>
	</div>

<?php echo \Form::close(); ?>

<div id="controllers_wrapper">
	<h1 class="title">要素の編集</h1>
	<div id="controller">
		<?php echo $setController($model_properties); ?>
	</div>
</div>

<div id="element_seq_wrapper">
	<h1 class="title">印刷順</h1>
	<ul id="element_seq">
	</ul>
</div>


<div id="element_template">
	<?php echo $templateElement(); ?>
</div>
