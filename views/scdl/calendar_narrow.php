<div class="narrow_user lcm_focus" title="絞り込み">
<select class="schedule_narrow select_narrow_down" id="narrow_user_group_id" title="ユーザーグループ" data-target-id="narrow_user_id">
	<option value="">-- ユーザーグループ --</option>
	<?php foreach ($narrow_user_group_list as $k => $v): ?>
		<option value="<?php print $k; ?>" <?php if ($k == \Session::get($kind_name . "narrow_ugid")) { print "selected"; } ?>><?php  print $v; ?></option>
	<?php endforeach; ?>
</select>

<select class="schedule_narrow" id="narrow_user_id" name="narrow_user_list" title="ユーザー">
	<option value="">-- ユーザー --
	<?php foreach($narrow_user_list as $row):?>
		<option value="<?php print $row['id']; ?>" <?php if ($row['id'] == \Session::get($kind_name . "narrow_uid")) { print "selected"; } ?>><?php  print $row['display_name']; ?>
	<?php endforeach; ?>
</select>
<input class="schedule_narrow button small primary" id="btn_user" type="button" value="絞り込み" onclick="javascript:location.href='?uid=' + $('#narrow_user_id').val() + '&ugid=' + $('#narrow_user_group_id').val()" />
<input class="schedule_narrow button small" id="btn_user_reset" type="button" value="絞り込みを解除" onclick="javascript:location.href='?uid=&ugid='" />

<?php 
// 非表示のとき以外
if (!(isset($day) && $day && $mode != "week")):
	if (\Session::get('scdl_display_time') == "1"):
		echo '<input type="button" class="schedule_narrow button small" value="時間を非表示" id="scdl_time_button">';
	else:
		echo '<input type="button" class="schedule_narrow button small" value="時間を表示" id="scdl_time_button">';
	endif;
endif;
?>
<?php /*
if($mode == "week"):
	if (\Session::get('view_empty_row') == "1"):
		echo '&nbsp;<input type="button" class="schedule_narrow button small" value="予定のないユーザを非表示" id="show_empty_row">';
	else:
		echo '&nbsp;<input type="button" class="schedule_narrow button small" value="予定のないユーザを表示" id="show_empty_row">';
	endif;
endif;
*/ ?>
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
$("#show_empty_row").click(function(event) {
	var show_empty_row = '<?php print \Session::get('show_empty_row'); ?>';
	var url = location.href;
	// 以前を削除
	url = url.replace("&show_empty_row=1", "");
	url = url.replace("&show_empty_row=0", "");
	url = url.replace("?show_empty_row=1", "");
	url = url.replace("?show_empty_row=0", "");
	if (url.match(/\?/)) {
		url += "&show_empty_row=";
	} else {
		url += "?show_empty_row=";
	}
	if (show_empty_row == 1) {
		location.href = url + "0";
	} else {
		location.href = url + "1";
	}
});



</script>