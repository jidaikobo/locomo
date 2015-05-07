<h1><?php echo $title ?></h1>
<?php
echo \Form::open(array('action' => \Uri::create(\Uri::current(), array(''), \Input::get()), 'class'=>'lcm_form form_group'));
echo $form;
?>

<!--checkboxes for noscript-->
<!--<noscript class="show_if_no_js">-->
<div class="input_group hide_if_no_js">
	<h2>ユーザ選択</h2>
	<div class="field label_fb lcm_focus" title="ユーザ選択">
	<?php
	foreach (\Model_Usr::find_options('display_name') as $uid => $v):
		echo '<label>'.\Form::checkbox('user[]', $uid, array_key_exists($uid, $item->user)).$v.'</label>';
	endforeach;
	?>
	</div>
</div>
<!--</noscript>-->
<!--/checkboxes for noscript-->

<!--multiple selector-->
<div class="input_group hide_if_no_js">
<h2>ユーザ選択</h2>
<div id="member_panel" class="lcm_focus field" title="メンバーの選択">
	<select id="group_list" title="グループ絞り込み">
		<option value="">絞り込み：全グループ</option>
		<?php foreach(\Model_Usrgrp::find_options('name') as $gid => $name): ?>
		<option value="<?php print $gid; ?>"><?php  print $name; ?></option>
		<?php endforeach; ?>
	</select>
	<div class="select_multiple_wrapper">
		<div class="select_multiple_content select_kizon">
			<h3 class="ac">選択済み</h3>
			<select id="member_kizon" name="user[]" size="2" style="width:11em;height:200px;" title="選択済みメンバー" multiple disabled>
			<?php foreach($item->user as $row) { ?>
				<option value="<?php echo $row->id; ?>"><?php echo $row->display_name; ?></option>
			<?php } ?>
			</select>
		</div><!-- /.select_multiple_content -->
		<div class="select_multiple_content button_group">
			<input type="button" value="解除" class="button small" onclick="javascript:select_member('minus');">
			<input type="button" value="選択" class="button small primary" onclick="javascript:select_member('plus');">
		</div><!-- /.select_multiple_content -->
		<div class="select_multiple_content select_new">
			<h3 class="ac">ここから選択</h3>
			<select id="member_new" name="member_new" size="2" style="width:11em;height:200px;" title="メンバー選択肢" multiple>
			<?php
				foreach(\Model_Usr::find_options('display_name') as $uid => $name) :
				if (array_key_exists($uid, $item->user)) continue;
			?>
				<option value="<?php echo $uid; ?>"><?php echo $name; ?></option>
			<?php endforeach; ?>
			</select>
		</div><!-- /.select_multiple_content -->
	</div><!-- /.select_multiple_wrapper -->
</div>
</div>
<!--/multiple selector-->

<?php
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>
