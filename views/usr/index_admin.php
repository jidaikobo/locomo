<div class="index_wrapper">
<div class="left_column sub_column" style="width: 260px;">
<!-- 各インデックスのサブカラムは適宜幅を与える。設定しなければ300px -->
<?php /*
	// search form
	echo \Form::open(array('method' => 'get', 'class' => 'index_search_form'));
	echo \Form::input(array('name' => 'all', 'type' => 'text', 'value' => \Input::get('all') ?: '',));
	echo \Form::submit('submit', '検索', array('class' => 'button primary'));
	echo \Form::close();
	*/
?>

<!--
<div class="lcm_focus">
	<h3 class="skip">インデックス</h3>
	<?php
		// index menu
	echo \Actionset::generate_menu_html($actionset['index'], array('class'=>'index_list'));
	?>
</div>
-->
<div class="form_group lcm_focus">
<h3 class="skip">検索</h3>
	<form class="search">
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
<?php
	// index information
	if((\Pagination::get('total_items') != 0)):
		echo '<div class="index_info lcm_focus">';
		echo '<p class="sort_info">'.\Pagination::sort_info('\Model_Usr');
		echo '<span class="add_bracket" style="margin-right: .5em;"> ○〜○件 / 全'.\Pagination::get('total_items').'件 </span></p>';
		echo '<p class="pagenation_info"><a href="" class="start_page" title="先頭のページへ">&laquo;<span class="skip">先頭のページへ</span></a><a href="" class="prev_page" title="前のページへ">&lsaquo;<span class="skip">前のページへ</span></a><input type="text" size="2"> / 10ページ<a href="" class="next_page" title="次のページへ">&rsaquo;<span class="skip">次のページへ</span></a><a href="" class="last_page" title="最後のページへ">&raquo;<span class="skip">最後のページへ</span></a></p>';
		echo '</div>';
	endif;
?>
<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus">
	<thead>
		<tr>
			<!--
			<th style="width: 10px; padding-right: 3px; padding-left: 3px;"><a role="button" class="button" style="padding: 4px 4px 2px; margin: 0;">選択</a></th>
			-->
			<th class="minimum"><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th style="width:7em;"><?php echo \Pagination::sort('username', 'ユーザ名', false);?></th>
			<th style="width:7em;"><?php echo \Pagination::sort('username', '表示名'); ?></th>
			<th><?php echo \Pagination::sort('email', 'Email'); ?></th>
			<th><?php echo \Pagination::sort('last_login_at', '最後のログイン日時'); ?></th>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<th>削除された日</th>
			<?php endif; ?>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1" title="<?php echo $item->display_name ?>">
<!--			<td style="text-align: center;"><input type="checkbox"></td>-->
			<td><?php echo $item->id; ?></td>
			<th style="min-width: 6em;"><div class="col_scrollable" tabindex="-1"><?php echo $item->display_name; ?></div></th>
			<td style="min-width: 6em;"><div class="col_scrollable" tabindex="-1"><?php echo $item->username; ?></div>
			</td>
			<td style="min-width: 12em;"><div class="col_scrollable" tabindex="-1"><?php echo $item->email; ?></div></td>
			<td><?php echo $item->last_login_at != '0000-00-00 00:00:00' ? $item->last_login_at : ''; ?></td>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<td><?php echo $item->deleted_at; ?></td>
			<?php endif; ?>
			<td>
				<div class="btn_group">
					<?php
					if (\Auth::has_access('\Controller_Usr/view')):
						echo Html::anchor('usr/view'.'/'.$item->id, '閲覧', array('class' => 'view'));
					endif;
					if (\Auth::has_access('\Controller_Usr/edit')):
						echo Html::anchor('usr/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_Usr/delete')):
						echo Html::anchor('usr/delete'.'/'.$item->id, '削除', array('class' => 'delete confirm'));
					endif;
					?>
				</div>
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