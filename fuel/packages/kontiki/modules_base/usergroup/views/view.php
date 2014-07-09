<?php echo \View::forge(PKGPATH.'kontiki/views/inc_header.php'); ?>

<h2>Viewing <span class='muted'>#<?php echo $item->id; ?></span></h2>

<p>
	<strong>Usergroup name:</strong>
	<?php echo $item->usergroup_name; ?></p>
	<strong>Delete date:</strong>
	<?php echo $item->deleted_at; ?></p>

<?php
$ctrl_sfx = isset($is_deleted) ? '_deleted' : '' ;
echo Html::anchor('usergroup/edit'.$ctrl_sfx.'/'.$item->id, 'Edit');
echo ' | ';
echo Html::anchor('usergroup/index'.$ctrl_sfx.'/', 'Back');
if(isset($is_deleted)||isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('usergroup/undelete/'.$item->id, 'Undelete');
endif;
if(isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('usergroup/delete_deleted/'.$item->id, 'Delete Completely');
endif;
?>

<?php echo \View::forge(PKGPATH.'kontiki/views/inc_footer.php'); ?>
