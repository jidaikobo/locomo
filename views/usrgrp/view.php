<h1><?php echo $title ?></h1>
<div class="lcm_form view">
<?php
echo $plain;
?>

<!--所属ユーザ-->
<div class="input_group">
	<h2>所属ユーザ</h2>
	<div class="field">
	<?php
	// get users
	$where = isset($item->id) ? array(array('usergroup.id', '=', $item->id)) : array(array('usergroup.id', '=', 0)) ;
	$users = \Model_Usr::find('all',
		array(
			'related' => count($where) ? array('usergroup') : array(),
			'where'=> $where,
			'order_by' => array('username' => 'asc')
			)
		);
	$users_lists = array();
	foreach ($users as $user):
		$users_lists[] = '<li style="float:left; margin-right: 30px;">'.\Html::anchor(\Uri::create('usr/view/'.$user->id), $user->display_name).'</li>';
	endforeach;
	if ($users_lists):
		echo '<ul>'.join($users_lists).'</ul>';
	else:
		echo '<p>所属ユーザはいません</p>';
	endif;
	?>
	
	</div>
</div><!--/所属ユーザ-->

</div><!-- /.lcm_form.view -->