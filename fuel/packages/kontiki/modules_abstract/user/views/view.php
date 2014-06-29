<?php echo \View::forge('inc_header'); ?>

<h2>Viewing <span class='muted'>#<?php echo $item->id; ?></span></h2>
<?php

//$item->userown= \Fieldset::forge('userown');
$result = \User\Model_User::find('all', array('related' => array('userown')));

echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
var_dump( $result[1] ) ;
echo '</textarea>' ;

?>
<p>
	<strong>User name:</strong>
	<?php echo $item->user_name; ?></p>
<p>
	<strong>Password:</strong>
	<?php echo $item->password; ?></p>
<p>
	<strong>Email:</strong>
	<?php echo $item->email; ?></p>
<p>
	<strong>Last login:</strong>
	<?php echo $item->last_login_at; ?></p>
<p>
	<strong>Delete date:</strong>
	<?php echo $item->deleted_at; ?></p>
<p>
	<strong>Activation key:</strong>
	<?php echo $item->activation_key; ?></p>
<p>
	<strong>Status:</strong>
	<?php echo $item->status; ?></p>

<?php
$ctrl_sfx = isset($is_deleted) ? '_deleted' : '' ;
echo Html::anchor('user/edit'.$ctrl_sfx.'/'.$item->id, 'Edit');
echo ' | ';
echo Html::anchor('user/index'.$ctrl_sfx.'/', 'Back');
if(isset($is_deleted)||isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('user/undelete/'.$item->id, 'Undelete');
endif;
if(isset($is_delete_deleted)):
	echo ' | ';
	echo Html::anchor('user/delete_deleted/'.$item->id, 'Delete Completely');
endif;
?>

<?php echo \View::forge('inc_footer');
