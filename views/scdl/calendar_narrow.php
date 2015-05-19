<div class="narrow_user lcm_focus" title="絞り込み">
<select class="schedule_narrow select_narrow_down" id="narrow_user_group_id" title="ユーザーグループ" data-target-id="narrow_user_id">
	<option value="">-- ユーザーグループ --
	<?php foreach(\Model_Usrgrp::find_options('name',
		array(
			'where' => array(
				array(
					array('is_available', true),
					array('is_for_acl', false),
					array('customgroup_uid', 'is', null)
				),
				'or' => array(array('customgroup_uid', \Auth::get('id')))
			),
		)
		) as $gid => $name): ?>
		<option value="<?php print $gid; ?>" <?php if ($gid == \Session::get($kind_name . "narrow_ugid")) { print "selected"; } ?>><?php  print $name; ?></option>
	<?php endforeach; ?>
</select>

<select class="schedule_narrow" id="narrow_user_id" name="narrow_user_list" title="ユーザー">
	<option value="">-- ユーザー --
	<?php foreach($narrow_user_list as $row) { ?>
		<option value="<?php print $row['id']; ?>" <?php if ($row['id'] == \Session::get($kind_name . "narrow_uid")) { print "selected"; } ?>><?php  print $row['display_name']; ?>
	<?php } ?>
</select>
<input class="schedule_narrow button small primary" id="btn_user" type="button" value="絞り込み" onclick="javascript:location.href='?uid=' + $('#narrow_user_id').val() + '&ugid=' + $('#narrow_user_group_id').val()" />
<input class="schedule_narrow button small" id="btn_user_reset" type="button" value="絞り込みを解除" onclick="javascript:location.href='?uid=&ugid='" />

<?php 
// 非表示のとき以外
if (!(isset($day) && $day && $mode != "week")) {
	if (\Session::get('scdl_display_time') == "1") { ?>
<input type="button" class="schedule_narrow button small" value="時間表示をしない" id="scdl_time_button" />
<?php } else { ?>
<input type="button" class="schedule_narrow button small" value="時間表示をする" id="scdl_time_button" />
<?php
	}
}
?>
</div><!-- /.narrow_user -->

<script>
$("#scdl_time_button").click(function(event) {

	var scdl_display_time = '<?php print \Session::get('scdl_display_time'); ?>';
	var url = location.href;
	// 以前を削除
	url = url.replace("&scdl_display_time=1", "");
	url = url.replace("&scdl_display_time=0", "");
	url = url.replace("?scdl_display_time=1", "");
	url = url.replace("?scdl_display_time=0", "");
	if (url.match(/\?/)) {
		url += "&scdl_display_time=";
	} else {
		url += "?scdl_display_time=";
	}
	if (scdl_display_time == 1) {
		location.href = url + "0";
	} else {
		location.href = url + "1";
	}
});


</script>