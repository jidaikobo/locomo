<select class="schedule_narrow" id="narrow_user_group_id" title="ユーザーグループ">
	<option value="">-- ユーザーグループ --
	<?php foreach($narrow_user_group_list as $key => $value) { ?>
		<option value="<?php print $key; ?>" <?php if ($key == \Session::get($kind_name . "narrow_ugid")) { print "selected"; } ?>><?php  print $value; ?>
	<?php } ?>
</select>

<select class="schedule_narrow" id="narrow_user_id" name="narrow_user_list" title="ユーザー">
	<option value="">-- ユーザー --
	<?php foreach($narrow_user_list as $row) { ?>
		<option value="<?php print $row['id']; ?>" <?php if ($row['id'] == \Session::get($kind_name . "narrow_uid")) { print "selected"; } ?>><?php  print $row['display_name']; ?>
	<?php } ?>
</select>
<input class="schedule_narrow button small primary" id="btn_user" type="button" value="絞り込み" onclick="javascript:location.href='?uid=' + $('#narrow_user_id').val() + '&ugid=' + $('#narrow_user_group_id').val()" />
<input class="schedule_narrow button small" id="btn_user_reset" type="button" value="絞り込みを解除" onclick="javascript:location.href='?uid=&ugid='" />
<script>
var base_uri = $('body').data('uri');

$("#narrow_user_group_id").change(function(event) {
	get_group_user(event);
});

$("#narrow_building_group_id").change(function(event) {
	get_group_building(event);
});
function get_group_user(e) {

	var group_id = $("#narrow_user_group_id").val();
	$.ajax({
		url: base_uri + 'scdl/get_user_list.json',
		type: 'post',
		data: 'gid=' + group_id,

		success: function(res) {
			exists = JSON.parse(res);
			document.getElementById("narrow_user_id").options.length=0;
			$("#narrow_user_id").append($('<option>').html('-- ユーザー --').val(""));
			for(var i in exists) {
				$("#narrow_user_id").append($('<option>').html(exists[i]['display_name']).val(exists[i]['id']));
			}
		
		}
	});
}

function get_group_building(e) {

	var group_id = $("#narrow_building_group_id").val();

	$.ajax({
		url: base_uri + 'scdl/get_building_list.json',
		type: 'post',
		data: 'bid=' + group_id,
		success: function(res) {
			exists = JSON.parse(res);
			document.getElementById("narrow_building_id").options.length=0;
			$("#narrow_building_id").append($('<option>').html('-- 施設 --').val(""));
			for(var i in exists) {
				$("#narrow_building_id").append($('<option>').html(exists[i]['item_name']).val(exists[i]['item_id']));
			}
		
		}
	});
}
</script>