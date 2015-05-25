<?php
if (isset($overlap_result) && count($overlap_result)) {
?>
<table class="tbl datatable" tabindex="0">
	<thead>
	<tr>
		<th>
			対象
		</th>
		<th>
			日時
		</th>
		<th>
			タイトル
		</th>
	</tr>
	</thead>
	<tbody>
<?php 
	foreach ($overlap_result as $v) {
?>
	<tr>
		<td>
		<?php print $v['targetdata']; ?>
		</td>
		<td>
		<?php
			print $v['target_date'];
		?>
		</td>
		<td>
		<?php print $v['title_text']; ?>
		</td>
	</tr>
<?php	} ;?>
	</tbody>
</table>
<?php } ;?>
<h1><?php echo $title ?></h1>
<?php echo \Form::open(); ?>

<div class="form_group lcm_form">
<?php
	// use model's form definition instead of raw-like html
	//echo $form;
?>
	<div class="input_group">
		<h2><?php echo $form->field('title_text')->set_template('{required}{label}'); ?></h2>
		<div class="field">
			<?php echo $form->field('title_text')->set_template('{error_msg}{field}'); ?>
			<span class="nowrap">
				<?php echo $form->field('title_importance_kb')->set_template('{label}'); ?>
				<?php echo $form->field('title_importance_kb')->set_template('{error_msg}{field}'); ?>
			</span>
			<span class="nowrap">
				<?php echo $form->field('title_kb')->set_template('{label}'); ?>
				<?php echo $form->field('title_kb')->set_template('{error_msg}{field}'); ?>
			</span>
		</div>
	</div><!-- /.input_group -->
	
	<div class="input_group">
		<h2><?php echo $form->field('repeat_kb')->set_template('{required}{label}'); ?></h2>
		<div id="field_repeat_kb" class="field">
			<?php if (isset($is_someedit)) { print '<p>なし</p><input type="hidden" name="repeat_kb" value="0" />'; } ?>
			<?php echo $form->field('repeat_kb')->set_template('{error_msg}{field}'); ?>
			<span id="span_target_month"><?php echo $form->field('target_month')->set_template('{error_msg}{field}'); ?>月</span>
			<span id="span_target_day"><?php echo $form->field('target_day')->set_template('{error_msg}{field}'); ?>日</span>
			<span id="span_week_kb"><?php echo $form->field('week_kb')->set_template('{error_msg}{field}'); ?>曜日</span>  <span id="span_week_number">第<?php echo $form->field('week_index')->set_template('{error_msg}{field}'); ?>週目</span>
			<div id="field_set_time" style="display: none;"> から </div>
		</div>
	</div><!-- /.input_group -->
	<div class="input_group">
		<h2>期間</h2>
		<div id="field_term" class="lcm_focus field" title="必須 期間">
			<span id="span_date_start" class="display_inline_block">
			<?php echo $form->field('start_date')->set_template('{error_msg}{field}'); ?>
			<?php echo $form->field('start_time')->set_template('{error_msg}{field}'); ?>
			</span> から <span id="span_date_end" class="display_inline_block">
			<?php echo $form->field('end_date')->set_template('{error_msg}{field}'); ?>
			<?php echo $form->field('end_time')->set_template('{error_msg}{field}'); ?>
			</span>
		</div>
	</div><!-- /.input_group -->
	<div class="input_group lcm_focus" title="詳細設定">
		<h2>詳細設定</h2>
		<div class="field">
			<?php echo $form->field('provisional_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
			<?php echo $form->field('unspecified_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
			<?php echo $form->field('allday_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
			<?php echo $form->field('private_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
			<?php echo $form->field('overlap_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
			<em class="exp" style="display: inline-block;">過去の予定は重複チェックの対象になりません。</em>
		</div>
	</div><!-- /.input_group -->
	<div class="input_group">
		<h2><?php echo $form->field('message')->set_template('{required}{label}'); ?></h2>
		<div class="field"><?php echo $form->field('message')->set_template('{error_msg}{field}'); ?></div>
	</div>
	
	<?php if( $locomo['controller']['name'] === "\Controller_Scdl"): //施設選択の時は下に ?>
	<div class="input_group">
	<h2><span class="label_required">必須</span>メンバー</h2>
		<div class="field">
			<div id="member_panel" class="lcm_focus" title="必須 メンバーの選択">
				<select id="group_list" title="グループ絞り込み" onchange="$(function(){get_group_user($('#group_list').val(), 'form_member_new');})">
					<option value="">絞り込み：全グループ
					<?php foreach($group_list as $key => $value) { ?>
						<option value="<?php print $key; ?>" <?php if (\Session::get($kind_name . "narrow_ugid") == $key && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $value; ?>
					<?php } ?>
				</select>
				<div class="lcm_multiple_select" data-hidden-item-id="hidden_members">
					<div class="multiple_select_content">
						<label for="member_kizon">選択済み</label>
						<select id="form_member_kizon" name="member_kizon" class="selected" multiple size="2" title="選択済みメンバー">
						<?php foreach($select_user_list as $row): ?>
							<option value="<?php echo $row->id; ?>"><?php echo $row->display_name; ?></option>
						<?php endforeach; ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="解除" class="remove_item button small">
					<div class="multiple_select_content">
						<label for="member_new">ここから選択</label>
						<select id="form_member_new" name="member_new" class="select_from" multiple size="2" title="メンバー選択肢">
						<?php foreach($non_selected_user_list as $row): ?>
							<option value="<?php echo $row->id; ?>"><?php echo $row->display_name; ?></option>
						<?php endforeach; ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="選択" class="add_item button small primary">
				</div><!-- /.lcm_multiple_select -->
				<label for="form_attend_flg_0"><?php echo $form->field('attend_flg')->set_template('{error_msg}{field}'); ?>出席確認を取る</label>
			</div>
		</div>
	</div><!-- /.input_group -->
	<?php endif; ?>
	<div class="input_group">
		<h2><?php echo $locomo['controller']['name'] === "\Controller_Scdl" ? '' : '<span class="label_required">必須</span>';?>施設選択</h2>
		<div class="field">
			<div id="building_panel" class="lcm_focus" title="<?php echo $locomo['controller']['name'] === "\Controller_Scdl" ? '' : '必須 ';?>施設の選択">
				<div id="building_select_wrapper">
				<select id="building_group_list" title="施設グループ絞り込み" onchange="get_group_building()">
					<option value="">絞り込み：全施設
					<?php foreach($building_group_list as $row) { ?>
						<option value="<?php print $row['item_group2']; ?>" <?php if (\Session::get($kind_name . "narrow_bgid") == $row['item_group2'] && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $row['item_group2']; ?>
					<?php } ?>
				</select>
				</div>
				<div class="lcm_multiple_select" data-hidden-item-id="hidden_buildings">
					<div class="multiple_select_content">
						<label for="building_kizon">選択済み</label>
						<select id="form_building_kizon" name="building_kizon" class="selected" size="2" title="選択済み施設" multiple>
						<?php foreach($select_building_list as $row) { ?>
							<option value="<?php echo $row->item_id; ?>"><?php echo $row->item_name; ?></option>
						<?php } ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="解除" class="button small remove_item">
					<div class="multiple_select_content select_new">
						<label for="building_new">ここから選択</label>
						<select id="form_building_new" name="building_new" class="select_from" size="2" multiple title="施設選択肢">
						<?php foreach($non_select_building_list as $row) { ?>
							<option value="<?php echo $row->item_id; ?>"><?php echo $row->item_name; ?></option>
						<?php } ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="選択" class="button small primary add_item">
				</div><!-- /.lcm_multiple_select -->
			</div>
		</div>
	</div><!-- /.input_group -->
	<div class="input_group">
		<h2><?php echo $form->field('group_kb')->set_template('{required}{label}'); ?></h2>
		<div class="field">
			<?php echo $form->field('group_kb')->set_template('{error_msg}{fields}<label>{field} {label}</label> {fields}'); ?>
			<?php echo $form->field('group_detail')->set_template('{error_msg}{field}'); ?>
		</div>
	</div><!-- /.input_group -->
	<div class="input_group">
		<h2><?php echo $form->field('purpose_kb')->set_template('{required}{label}'); ?></h2>
		<div class="field"><?php echo $form->field('purpose_kb')->set_template('{error_msg}{field}'); ?></div>
	</div><!-- /.input_group -->
	<?php /* ?>
	<div class="input_group">
		<h2><?php echo $form->field('purpose_text')->set_template('{required}{label}'); ?></h2>
		<div class="field"><?php echo $form->field('purpose_text')->set_template('{error_msg}{field}'); ?></div>
	</div>
	<?php */ ?>
	<?php echo $form->field('purpose_text')->set_type('hidden'); ?>
	<div class="input_group">
		<h2><?php echo $form->field('user_num')->set_template('{required}{label}'); ?></h2>
		<div class="field"><?php echo $form->field('user_num')->set_template('{error_msg}{field}'); ?>人</div>
	</div><!-- /.input_group -->
	<div class="input_group">
		<h2><?php echo $form->field('user_id')->set_template('{required}{label}'); ?></h2>
		<div class="field">
			<select id="group_list_create_user" title="グループ絞り込み" onchange="$(function(){get_group_user($('#group_list_create_user').val(), 'form_user_id');})">
				<option value="">絞り込み：全グループ
				<?php foreach($group_list as $key => $value) { ?>
					<option value="<?php print $key; ?>"><?php  print $value; ?>
				<?php } ?>
			</select>
			<?php echo $form->field('user_id')->set_template('{error_msg}{field}'); ?>
		</div>
	</div><!-- /.input_group -->
	<?php if( $locomo['controller']['name'] !== "\Controller_Scdl"):?>
	<div class="input_group">
		<h2 class="ar">メンバー</h2>
		<div class="field">
			<div id="member_panel" class="lcm_focus" title="メンバーの選択">
				<select id="group_list" title="グループ絞り込み" onchange="$(function(){get_group_user($('#group_list').val(), 'form_member_new');})">
					<option value="">絞り込み：全グループ
				<?php foreach($group_list as $key => $value): ?>
					<option value="<?php print $key; ?>" <?php if (\Session::get($kind_name . "narrow_ugid") == $key && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $value; ?>
				<?php endforeach; ?>
				</select>
				<div class="lcm_multiple_select" data-hidden-item-id="hidden_members">
					<div class="multiple_select_content">
						<label for="member_kizon">選択済み</label>
						<select id="form_member_kizon" name="member_kizon" class="selected" multiple size="2" title="選択済みメンバー">
						<?php foreach($select_user_list as $row): ?>
							<option value="<?php echo $row->id; ?>"><?php echo $row->display_name; ?></option>
						<?php endforeach; ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="解除" class="remove_item button small">
					<div class="multiple_select_content">
						<label for="member_new">ここから選択</label>
						<select id="form_member_new" name="member_new" class="select_from" multiple size="2" title="メンバー選択肢">
						<?php foreach($non_selected_user_list as $row): ?>
							<option value="<?php echo $row->id; ?>"><?php echo $row->display_name; ?></option>
						<?php endforeach; ?>
						</select>
					</div><!-- /.multiple_select_content -->
					<input type="button" value="選択" class="button small primary add_item">
				</div><!-- /.lcm_multiple_select -->
				<label for="form_attend_flg_0"><?php echo $form->field('attend_flg')->set_template('{error_msg}{field}'); ?>出席確認を取る</label>
			</div>
		</div>
	</div><!-- /.input_group -->
	<?php endif; ?>
	
	<?php echo $form->field('created_at')->set_template('{error_msg}{field}'); ?>
	<?php echo $form->field('is_visible')->set_template('{error_msg}{field}'); ?>
	<?php echo $form->field('kind_flg')->set_template('{error_msg}{field}'); ?>
	<input type="hidden" id="is_someedit" name="is_someedit" value="<?php echo isset($is_someedit) ? $is_someedit : 0; ?>" />
	<?php
		// revision memo template - optional
		//echo render(LOCOMOPATH.'views/revision/inc_revision_memo.php');
	?>
	
	<div class="submit_button">
		<?php
		if( ! @$is_revision): 
			echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
			echo \Form::submit('submit', '保存する', array('class' => 'button primary'));
		endif;
		?>
	</div>

</div><!--/.form_group-->


<script>
<!-- JSに移す -->
change_repeat_kb_area();
/* いったんonchangeに変更。でも.attachEvent('onchange', ...)とかで書き直せる？
$("#form_repeat_kb").change(function(event){
	change_repeat_kb_area();
});
$("#group_list").change(function(event) {
	get_group_user(event, $("#group_list").val(), "form_member_new");
});
$("#group_list_create_user").change(function(event) {
	get_group_user(event, $("#group_list_create_user").val(), "form_user_id");
});

$("#building_group_list").change(function(event) {
	get_group_building(event);
});
$("#form_group_detail").change(function(event) {
	form_group_detail_change(event);
});
*/


function form_group_detail_change(e) {
	$("#form_group_kb_1").val(['2']);
}

// 部分編集の場合
if ($("#is_someedit").val() == 1) {
	$("#form_repeat_kb").val(0);
	//$("#form_start_date").prop("disabled", true);
	//$("#form_end_date").prop("disabled", true);
	$("#form_start_date").css("visibility", "hidden");
	$("#form_end_date").css("visibility", "hidden");
	$("#form_start_date").css("width", "0px");
	$("#form_end_date").css("width", "0px");
	change_repeat_kb_area();
	$("#form_repeat_kb").css("display", "none");
}


/**
 * [change_repeat_kb_area description]
 * @return {[type]} [description]
 */
function change_repeat_kb_area() {
	var repeat_kb = $("#form_repeat_kb").val();
	if (repeat_kb == 0 || repeat_kb == 1 || repeat_kb == 2) {
		// なし
		$("#span_week_kb").hide();
		$("#span_week_number").hide();
		$("#span_target_day").hide();
		$("#span_target_month").hide();
	} else if (repeat_kb == 3) {
		$("#span_week_kb").css({'display': 'inline-block'});
		$("#span_week_number").hide();
		$("#span_target_day").hide();
		$("#span_target_month").hide();
	} else if (repeat_kb == 4) {
		$("#span_week_kb").hide();
		$("#span_week_number").hide();
		$("#span_target_day").css({'display': 'inline-block'});
		$("#span_target_month").hide();
	} else if (repeat_kb == 5) {
		$("#span_week_kb").hide();
		$("#span_week_number").hide();
		$("#span_target_day").css({'display': 'inline-block'});
		$("#span_target_month").css({'display': 'inline-block'});
	} else if (repeat_kb == 6) {
		$("#span_week_kb").css({'display': 'inline-block'});
		$("#span_week_number").css({'display': 'inline-block'});
		$("#span_target_day").hide();
		$("#span_target_month").hide();
	}

	//区分選択により時間入力欄を移動 tabindex制御されているときに別のブロックに移動する時のふるまいは個別に設定しないといけない？
	//lcm_focusのフォーカス制御があるので、遅延させる(//NetReaderの実行は遅延させなくてよい)
	//ひとまず外側で遅延させておく
	//タイムラグの間にキーボード操作(tab)を受け付けるとちょっとまずい？
	//ひとまず値を全部持ってくる。あとでせいり
	var userAgent = window.navigator.userAgent;
	var isNetReader   = userAgent.indexOf('NetReader') > 0 ? true : false;
	var isTouchDevice = userAgent.indexOf('iPhone') > 0 || userAgent.indexOf('iPod') > 0 || userAgent.indexOf('iPad') > 0 || userAgent.indexOf('Android') > 0 ? true : false;
	var isie          = !$('body').hasClass('lcm_ieversion_0') ? true : false;
	var isLtie9       = $('body').hasClass('lcm_ieversion_8') || $('body').hasClass('lcm_ieversion_7') || $('body').hasClass('lcm_ieversion_6') ? true : false;
	var tabindexCtrl  = isNetReader || isLtie9 || isTouchDevice ? false : true;//この条件は増えたり減ったりするのかも。

	if(isNetReader){
		move_time_inputfield();
	}else{
		setTimeout(move_time_inputfield, 250);
	}
	function move_time_inputfield(){
		var start_time, end_time, field, input;
		start_time = $('#form_start_time');
		end_time   = $('#form_end_time');
		field      = $('#field_set_time');
		input     = start_time.add(end_time);
		if(repeat_kb == 0){
			field.hide();
			start_time.appendTo('#span_date_start');
			end_time.appendTo('#span_date_end');
		}else{
			field.prepend(start_time).append(end_time).show();
		}
		if(tabindexCtrl){
			input.each(function(){
				if(repeat_kb == 0){
					$(this).attr('tabindex', -1);
				}else{
					$(this).attr('tabindex', 0);
				}
			});
		}
	}

	//区分選択により、期間の入力欄の種類を変更 //入力が未対応なのでコメントアウト
/*	if(repeat_kb < 4){
		$('#form_start_date, #form_end_date').removeClass('month');
		//入力欄の値もyy-mmに変更したい。datepicker上の値は"1日"が補完される
	}else{
		$('#form_start_date, #form_end_date').addClass('month');
	}
*/
}

//終日選択反映
//終日選択されている時のinput.timeでtimepickerをよびだしたくない
is_allday();
//$('#form_allday_kb').change(is_allday);
function is_allday(){
	if($('#form_allday_kb').prop('checked')){
		$('#form_start_time').val('0:00');
		$('#form_end_time').val('23:59');
		$('#form_start_time, #form_end_time').attr('readonly',true);
	}else{
		$('#form_start_time, #form_end_time').attr('readonly',false);
	}
}
/*
	複数選択はjquery.inc.jsに移動。
*/


var base_uri = $('body').data('uri');

function get_group_user(groupId, targetEle) {

	var targetEle = targetEle;
	var group_id = groupId;

	var now_members = new Object();
	var kizon_options = document.getElementById('form_member_kizon').options;
	for(var i = 0; i < kizon_options.length; i++){
		now_members['member' + kizon_options[i].value] = 1;
	};

	$.ajax({
		url: base_uri + 'usr/user_list.json',
		type: 'post',
		data: 'gid=' + group_id,
		success: function(res) {
			exists = JSON.parse(res);

			document.getElementById(targetEle).options.length=0;

			for(var i in exists) {
				if (targetEle == "member_new") {
					if (!now_members['member' + exists[i]['id']]) {
						$("#" + targetEle).append($('<option>').html(exists[i]['display_name']).val(exists[i]['id']));
					}
				} else {
					$("#" + targetEle).append($('<option>').html(exists[i]['display_name']).val(exists[i]['id']));
				}
			}
		
		}
	});
}

function get_group_building() {

	var group_id = $("#building_group_list").val();

	var now_buildings = new Object();
	var kizon_options = document.getElementById('form_building_kizon').options;
	for(var i = 0; i < kizon_options.length; i++){
		now_buildings['building' + kizon_options[i].value] = 1;
	};

	$.ajax({
		url: base_uri + 'scdl/get_building_list.json',
		type: 'post',
		data: 'bid=' + group_id,
		success: function(res) {

			exists = JSON.parse(res);

			document.getElementById("form_building_new").options.length=0;

			for(var i in exists) {
				if (!now_buildings['building' + exists[i]['item_id']]) {
					$("#form_building_new").append($('<option>').html(exists[i]['item_name']).val(exists[i]['item_id']));
				}
			}
		
		}
	});
}
</script>


<?php echo \Form::close(); ?>
