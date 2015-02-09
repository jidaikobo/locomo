
<select id="narrow_user_group_id" onchange="location.href= '?ugid=' + this.value">
	<option value="-1">--- 全て ---
	<?php foreach($narrow_user_group_list as $key => $value) { ?>
		<option value="<?php print $key; ?>" <?php if ($key == \Session::get($model_name . "narrow_ugid")) { print "selected"; } ?>><?php  print $value; ?>
	<?php } ?>
</select>
<select id="narrow_user_id" name="narrow_user_list" onchange="location.href='?uid=' + this.value">
	<option value="">--- 全て ---
	<?php foreach($narrow_user_list as $row) { ?>
		<option value="<?php print $row['id']; ?>" <?php if ($row['id'] == \Session::get($model_name . "narrow_uid")) { print "selected"; } ?>><?php  print $row['display_name']; ?>
	<?php } ?>
</select>

