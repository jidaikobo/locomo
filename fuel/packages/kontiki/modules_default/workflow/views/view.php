<?php echo $include_tpl('inc_header.php'); ?>

<h2>ワークフロー <span class='muted'><?php echo $item->name; ?></span></h2>


<?php
$ctrl_sfx = isset($is_deleted) ? '_deleted' : '' ;
echo Html::anchor('workflow/edit'.$ctrl_sfx.'/'.$item->id, 'Edit');
echo ' | ';
echo Html::anchor('workflow/index'.$ctrl_sfx.'/', 'Back');
if(isset($is_deleted)||isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('workflow/undelete/'.$item->id, 'Undelete');
endif;
if(isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('workflow/delete_deleted/'.$item->id, 'Delete Completely');
endif;
?>

<?php echo $include_tpl('inc_footer.php'); ?>
