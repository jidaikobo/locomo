<?php echo $include_tpl('inc_header.php'); ?>

<h2>Viewing <span class='muted'>#<?php echo $item->id; ?></span></h2>

<table class="tbl">
<tr>
	<th>title</th>
	<td><?php echo $item->title; ?></td>
</tr>

<tr>
	<th>body</th>
	<td><?php echo $item->body; ?></td>
</tr>

<tr>
	<th>status</th>
	<td><?php echo $item->status; ?></td>
</tr>

<tr>
	<th>created_at</th>
	<td><?php echo $item->created_at; ?></td>
</tr>

<tr>
	<th>expired_at</th>
	<td><?php echo $item->expired_at; ?></td>
</tr>

<tr>
	<th>deleted_at</th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>


</table>

<?php
$ctrl_sfx = isset($is_deleted) ? '_deleted' : '' ;
echo Html::anchor('post2/edit'.$ctrl_sfx.'/'.$item->id, 'Edit');
echo ' | ';
echo Html::anchor('post2/index'.$ctrl_sfx.'/', 'Back');
if(isset($is_deleted)||isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('post2/undelete/'.$item->id, 'Undelete');
endif;
if(isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('post2/delete_deleted/'.$item->id, 'Delete Completely');
endif;
?>

<?php echo $include_tpl('inc_footer.php'); ?>
