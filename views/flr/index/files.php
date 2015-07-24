<h1><?php echo $search_form ?></h1>

<!--ファイラはページネーションしない-->
<!--
<div class="index_toolbar clearfix">
<?php echo \Pagination::create_links(); ?>
</div>
-->
<?php if ( ! \Input::get('submit')) echo $breadcrumbs ;?>

<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus" title="ファイル一覧">
	<thead>
		<tr>
			<th>名称</th>
<?php if (\Input::get('submit')): ?>
			<th>パス</th>
<?php endif; ?>
			<th class="min">操作</th>
			<th class="min hide_if_smalldisplay">種類</th>
			<th>説明</th>
			<th class="min hide_if_smalldisplay">登録日</th>
			<th class="min hide_if_smalldisplay">作成者</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr title="<?php echo $item->name.' (登録日:'.date('Y年n月j日G時i分', strtotime($item->created_at)).')' ?>" tabindex="-1">
			<th<?php if ($item->genre == 'dir') echo ' class="dir"' ?>>
			<?php
				if ($item->genre == 'dir'):
					echo Html::anchor('flr/index_files'.DS.$item->id, $item->name, array('class' => 'icon dir'));
				else:
					if (\Controller_Flr::check_auth($item->path, 'read') || $item->depth == 1):
						echo Html::anchor('flr/file/view'.DS.$item->id, '<span class="skip">'.$item->genre.' </span>'.$item->name, array('class' => 'hide_if_smalldisplay icon '.$item->genre));

						echo Html::anchor('flr/file/view'.DS.$item->id, '<div class="col_scrollable" style="min-width: 12em;"><span class=" icon '.$item->genre.'"></span><span class="skip">'.$item->genre.' </span>'.$item->name.'</div>', array('class' => 'show_if_smalldisplay'));
					else:
						echo '<span class="icon '.$item->genre.'">'.$item->name.'</span>';
					endif;
				endif;
			?>
			</th>
<?php if (\Input::get('submit')): ?>
			<td><div class="col_scrollable" style="min-width: 6em;"><?php echo dirname(urldecode($item->path)) ?></div></td>
<?php endif; ?>
			<td class="ac"><?php
				if ($item->genre !== 'dir'):
					if (\Controller_Flr::check_auth($item->path, 'read') || $item->depth == 1):
						echo \Html::anchor(\Uri::create('flr/file/dl/?p='.\Model_Flr::enc_url($item->path, true)), '<span class="icon" style="font-size: 1em; width: 1.5em; height: 1.5em;"><img src="'.\Uri::base().'lcm_assets/img/system/mark_download.png" alt="ダウンロード"></span>', array('class' => 'show_if_smalldisplay'));
						echo \Html::anchor(\Uri::create('flr/file/dl/?p='.\Model_Flr::enc_url($item->path, true)), 'ダウンロード', array('class' => 'button small hide_if_smalldisplay'));
					endif;
				endif;
			?></td>
			<td class="hide_if_smalldisplay"><div class="col_scrollable" style="min-width:3.5em;"><?php echo $item->genre; ?></div></td>
			<td><div class="col_scrollable" style="min-width:12em;"><?php echo $item->explanation; ?></div></td>
			<td class="hide_if_smalldisplay"><?php echo date('Y年m月d日', strtotime($item->created_at)); ?></td>
			<td class="hide_if_smalldisplay"><?php echo \Model_Usr::get_display_name($item->creator_id); ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
	<tfoot class="thead">
		<tr>
			<th>名称</th>
<?php if (\Input::get('submit')): ?>
			<th>パス</th>
<?php endif; ?>
			<th class="min">操作</th>
			<th class="min hide_if_smalldisplay">種類</th>
			<th>説明</th>
			<th class="min hide_if_smalldisplay">登録日</th>
			<th class="min hide_if_smalldisplay">作成者</th>
		</tr>
	</tfoot>
</table>

<?php else: ?>
<p>ファイルおよびディレクトリが存在しません。</p>

<?php endif; ?>
