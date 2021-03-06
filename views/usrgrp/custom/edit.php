<h1><?php echo $title ?></h1>
<?php
echo \Form::open(array('action' => \Uri::create(\Uri::current(), array(''), \Input::get()), 'class'=>'lcm_form form_group'));
echo $form;
?>

<!--multiple selector-->
<div class="input_group hide_if_no_js ">
<h2>ユーザ選択</h2>
	<div class="field">
			<div id="member_panel" class="lcm_focus" title="ユーザの選択">
				<select id="group_list" name="group_list" class="multiple_select_narrow_down" data-target-id="user_group_selects" title="グループ絞り込み">
					<option value="">絞り込み：全グループ</option>
					<?php foreach(\Model_Usrgrp::find_options('name',
						array(
							'where' => array(
								array(
									array('is_available', true),
									array('is_for_acl', false),
									array('customgroup_uid', 'is', null)
								),
							),
						)
						) as $gid => $name): ?>
					<option value="<?php print $gid; ?>"><?php  print $name; ?></option>
					<?php endforeach; ?>
				</select>

				<div id="user_group_selects" class="lcm_multiple_select">
					<div class="multiple_select_content">
						<label for="member_kizon">選択済み</label>
						<select id="form_member_kizon" name="member_kizon" class="selected" multiple size="2" title="選択済みユーザ">
						<?php foreach($item->user as $row): ?>
							<option value="<?php echo $row->id; ?>"><?php echo $row->display_name; ?></option>
						<?php endforeach; ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="解除" class="remove_item button small">
					<div class="multiple_select_content">
						<label for="member_new">ここから選択</label>
						<select id="form_member_new" name="member_new" class="select_from" multiple size="2" title="ユーザ選択肢">
						<?php
							foreach(\Model_Usr::find_options( 'display_name', array()) as $uid => $name) :
							if (array_key_exists($uid, $item->user)) continue;
						?>
							<option value="<?php echo $uid; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="選択" class="button small add_item primary">
				</div><!-- /.lcm_multiple_select -->
			</div>
	</div><!-- /.field -->
</div>
<!--/multiple selector-->
<!--checkboxes for noscript-->
<div class="input_group show_if_no_js">
	<h2>ユーザ選択</h2>
	<div class="field label_fb lcm_focus" title="ユーザ選択">
	<?php
	foreach (\Model_Usr::find_options('display_name', array()) as $uid => $v):
		echo '<label>'.\Form::checkbox('user[]', $uid, array_key_exists($uid, $item->user)).$v.'</label>';
	endforeach;
	?>
	</div>
</div>

<!--/checkboxes for noscript-->
<?php
$usrgrps = \Model_Usrgrp::find_options('name',
																			 array(
																				 'where' => array(
																					 array(
																						 array('is_available', true),
																						 array('is_for_acl', false),
																						 array('customgroup_uid', 'is', null)
																					 )
																				 ),
																			 ));
?>

<!--checkboxes for noscript-->
<div class="input_group show_if_no_js1">
	<h2>ユーザグループ選択</h2>
	<div class="field label_fb lcm_focus" title="ユーザグループ選択">
	<?php
	foreach ($usrgrps as $ugid => $v):
		echo '<label>'.\Form::checkbox('usergroup[]', $ugid, array_key_exists($ugid, $item->usergroup)).$v.'</label>';
	endforeach;
	?>
	</div>
</div>

<!--/checkboxes for noscript-->
<?php /* ?>
<div class="input_group hide_if_no_js nd">
<h2>ユーザグループ選択</h2>
	<div class="field">
			<div id="group_panel" class="lcm_focus" title="ユーザグループの選択">
				<div class="lcm_multiple_select" data-hidden-item-id="hidden_group">
					<div class="multiple_select_content">
						<label for="group_kizon">選択済み</label>
						<select id="form_group_kizon" name="group_kizon" multiple size="2" title="選択済みユーザ">
						<?php foreach($item->usergroup as $row): ?>
							<option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
						<?php endforeach; ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="解除" class="remove_item button small">
					<div class="multiple_select_content">
						<label for="group_new">ここから選択</label>
						<select id="form_group_new" name="group_new" class="select_from" multiple size="2" title="ユーザ選択肢">
						<?php foreach($usrgrps as $gid => $name): ?>
						<option value="<?php print $gid; ?>"><?php  print $name; ?></option>
						<?php endforeach; ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="選択" class="button small add_item primary">
				</div><!-- /.lcm_multiple_select -->
			</div>
	</div><!-- /.field -->
</div>
<!--/multiple selector-->
<?php */ ?>
<?php
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>
