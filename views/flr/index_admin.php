<div class="index_wrapper">
<div class="left_column sub_column" style="width: 260px;"><!-- 各インデックスのサブカラムは適宜幅を与える。初期値は300px -->
<?php /*
	// search form
	echo \Form::open(array('method' => 'get', 'class' => 'index_search_form'));
	echo \Form::input(array('name' => 'all', 'type' => 'text', 'value' => \Input::get('all') ?: '',));
	echo \Form::submit('submit', '検索', array('class' => 'button primary'));
	echo \Form::close();
	*/
?>
<div class="lcm_focus">
	<h3 class="skip">インデックス</h3>
	<?php
		// index menu
	echo \Actionset::generate_menu_html($actionset['index'], array('class'=>'index_list'));
	?>
</div>
<div class="form_group lcm_focus">
<h3 class="skip">検索</h3>
	<form style="text-align: left;">
		<h4><label for="keyword">キーワード</label></h4>
		<input type="text" name="all" id="keyword" size="20" value="<?php echo \Input::get('all') ?>" />
		<h4>登録日</label></h4>
			<input type="date" name="from" id="registration_date_start" value="<?php echo \Input::get('from') ?>" />&nbsp;〜&nbsp;
			<input type="date" name="to" id="registration_date_end" value="<?php echo \Input::get('to') ?>" />
		<?php echo \Form::submit('submit', '検索', array('class' => 'button primary')); ?>
	</form>
</div><!-- /.form_group -->
</div><!-- /.left_column -->

<?php 
//ここまで検索フォームとりあえず
 ?>

<!-- .right_column -->
<div class="right_column main_column index_table">
<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus">
	<thead>
		<tr>
			<th>ディレクトリ名</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td style="min-width: 6em;" ><div class="col_scrollable" tabindex="-1">
			<?php
				$dirname = substr(dirname($item->path), strlen(LOCOMOUPLOADPATH) - 1).DS;
				echo Html::anchor('flr/index_files'.'/'.$item->id, $dirname.$item->name, array('class' => 'view'));
			?>
			</div></td>
			<td>
			<?php if (\Controller_Flr::check_auth($item->path)): ?>
				<div class="btn_group">
					<?php
					if (\Controller_Flr::check_auth($item->path)):
						echo Html::anchor('flr/upload'.'/'.$item->id, 'アップロード', array('class' => 'edit'));
					endif;
					if (\Controller_Flr::check_auth($item->path, $writable = true)):
						echo Html::anchor('flr/move_dir'.'/'.$item->id, '移動', array('class' => 'edit'));
						echo Html::anchor('flr/rename_dir'.'/'.$item->id, 'リネーム', array('class' => 'edit'));
						echo Html::anchor('flr/permission_dir'.'/'.$item->id, '権限', array('class' => 'edit'));
						echo Html::anchor('flr/purge_dir'.'/'.$item->id, '削除', array('class' => 'edit'));
					endif;
					?>
				</div>
			<?php else: ?>
				<div>このディレクトリを操作する権限がありません。</div>
			<?php endif; ?>
			</td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ユーザが存在しません。</p>

<?php endif; ?>
</div><!-- /.right_column -->

</div><!-- /.index_wrapper -->