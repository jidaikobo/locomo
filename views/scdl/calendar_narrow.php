
<select class="schedule_narrow" id="narrow_user_group_id" title="ユーザーグループ">
	<option value="-1">-- ユーザーグループ --
	<?php foreach($narrow_user_group_list as $key => $value) { ?>
		<option value="<?php print $key; ?>" <?php if ($key == \Session::get($model_name . "narrow_ugid")) { print "selected"; } ?>><?php  print $value; ?>
	<?php } ?>
</select>
<input class="schedule_narrow button small" id="btn_user_group" type="button" value="絞り込み" onclick="javascript:location.href='?ugid=' + $('#narrow_user_group_id').val()" style="margin-right: 1em;" />

<select class="schedule_narrow" id="narrow_user_id" name="narrow_user_list" title="ユーザー">
	<option value="">-- ユーザー --
	<?php foreach($narrow_user_list as $row) { ?>
		<option value="<?php print $row['id']; ?>" <?php if ($row['id'] == \Session::get($model_name . "narrow_uid")) { print "selected"; } ?>><?php  print $row['display_name']; ?>
	<?php } ?>
</select>
<input class="schedule_narrow button small" id="btn_user" type="button" value="絞り込み" onclick="javascript:location.href='?uid=' + $('#narrow_user_id').val()" />
