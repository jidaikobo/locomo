<?php echo render('inc_header'); ?>

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
	<th>user_id</th>
	<td><?php echo $item->user_id; ?></td>
</tr>


</table>

<?php
$ctrl_sfx = isset($is_deleted) ? '_deleted' : '' ;
echo Html::anchor('post/edit'.$ctrl_sfx.'/'.$item->id, 'Edit');
echo ' | ';
echo Html::anchor('post/index'.$ctrl_sfx.'/', 'Back');
if(isset($is_deleted)||isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('post/undelete/'.$item->id, 'Undelete');
endif;
if(isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('post/delete_deleted/'.$item->id, 'Delete Completely');
endif;
?>

<?php echo render('inc_footer'); ?>
