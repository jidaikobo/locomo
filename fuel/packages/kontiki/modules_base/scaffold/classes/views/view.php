<?php echo \View::forge(PKGPATH.'kontiki/views/inc_header.php'); ?>

<h2>Viewing <span class='muted'>#<?php echo $item->id; ?></span></h2>

<table class="tbl">
###fields###
</table>

<?php
$ctrl_sfx = isset($is_deleted) ? '_deleted' : '' ;
echo Html::anchor('xxx/edit'.$ctrl_sfx.'/'.$item->id, 'Edit');
echo ' | ';
echo Html::anchor('xxx/index'.$ctrl_sfx.'/', 'Back');
if(isset($is_deleted)||isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('xxx/undelete/'.$item->id, 'Undelete');
endif;
if(isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('xxx/delete_deleted/'.$item->id, 'Delete Completely');
endif;
?>

<?php echo \View::forge(PKGPATH.'kontiki/views/inc_footer.php'); ?>
