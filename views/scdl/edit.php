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

<!--form_group-->
<div class="form_group lcm_form">
<?php
	// use model's form definition instead of raw-like html
	//echo $form;
?>

<table class="formtable">
<tbody>
<tr>
	<th class="ar"><?php echo $form->field('title_text')->set_template('{required}{label}'); ?></th>
	<td>
		<div class="input_group">
			<div class="field">
				<?php echo $form->field('title_text')->set_template('{error_msg}{field}'); ?>
			</div>
			<div class="field nowrap">
				<?php echo $form->field('title_importance_kb')->set_template('{label}'); ?>
				<?php echo $form->field('title_importance_kb')->set_template('{error_msg}{field}'); ?>
			</div>
			<div class="field nowrap">
				<?php echo $form->field('title_kb')->set_template('{label}'); ?>
				<?php echo $form->field('title_kb')->set_template('{error_msg}{field}'); ?>
			</div>
		</div>
	</td>
</tr>

<tr>
	<th class="ar"><?php echo $form->field('repeat_kb')->set_template('{required}{label}'); ?></th>
	<td>
		<div id="field_repeat_kb">
			<?php echo $form->field('repeat_kb')->set_template('{error_msg}{field}'); ?>
			<span id="span_target_month"><?php echo $form->field('target_month')->set_template('{error_msg}{field}'); ?>月</span>
			<span id="span_target_day"><?php echo $form->field('target_day')->set_template('{error_msg}{field}'); ?>日</span>
			<span id="span_week_kb"><?php echo $form->field('week_kb')->set_template('{error_msg}{field}'); ?>曜日</span>  <span id="span_week_number">第<?php echo $form->field('week_index')->set_template('{error_msg}{field}'); ?>週目</span>
		</div>
		<div id="field_set_time" style="display: none;"> から </div>
	</td>
</tr>
<tr>
	<th class="ar">期間</th>
	<td>
		<div id="" class="lcm_focus" title="必須 期間">
			<div>
				<span id="span_date_start" class="display_inline_block">
				<?php echo $form->field('start_date')->set_template('{error_msg}{field}'); ?>
				<?php echo $form->field('start_time')->set_template('{error_msg}{field}'); ?>
				</span> から <span id="span_date_end" class="display_inline_block">
				<?php echo $form->field('end_date')->set_template('{error_msg}{field}'); ?>
				<?php echo $form->field('end_time')->set_template('{error_msg}{field}'); ?>
				</span>
			</div>
		</div>
	</td>
</tr>

<tr>
	<th class="ar">詳細設定</th>
	<td class="lcm_focus" title="詳細設定">
	<?php echo $form->field('provisional_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<?php echo $form->field('unspecified_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<?php echo $form->field('allday_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<?php echo $form->field('private_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<?php echo $form->field('overlap_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<em class="exp" style="display: inline-block;">過去の予定は重複チェックの対象になりません。</em>
	</td>
</tr>

<tr>
	<th class="ar"><?php echo $form->field('message')->set_template('{required}{label}'); ?></th>
	<td><?php echo $form->field('message')->set_template('{error_msg}{field}'); ?></td>
</tr>

<?php if( $locomo['controller']['name'] === "\Controller_Scdl"): //施設選択の時は下に ?>
<tr>
<th class="ar"><span class="label_required">必須</span>メンバー</th>
<td>
	<div id="member_panel" class="lcm_focus" title="メンバーの選択">
		<select id="group_list" title="グループ絞り込み">
			<option value="">絞り込み：全グループ
			<?php foreach($group_list as $key => $value) { ?>
				<option value="<?php print $key; ?>" <?php if (\Session::get($kind_name . "narrow_ugid") == $key && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $value; ?>
			<?php } ?>
		</select>
		<div class="select_multiple_wrapper">
			<div class="select_multiple_content select_kizon">
				<h3 class="ac">選択済み</h3>
				<select id="member_kizon" name="member_kizon" size="2" style="width:11em;height:200px;" title="選択済みメンバー" multiple>
				<?php foreach($select_user_list as $row) { ?>
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
					foreach($non_selected_user_list as $row) {
					?>
					<option value="<?php echo $row->id; ?>"><?php echo $row->display_name; ?></option>
				<?php } ?>
				</select>
			</div><!-- /.select_multiple_content -->
		</div><!-- /.select_multiple_wrapper -->
		<label for="form_attend_flg_0"><?php echo $form->field('attend_flg')->set_template('{error_msg}{field}'); ?>出席確認を取る</label>
	</div>
</td>
</tr>
<?php endif; ?>
<tr>
<th class="ar"><?php echo $locomo['controller']['name'] === "\Controller_Scdl" ? '' : '<span class="label_required">必須</span>';?>施設選択</th>
<td>
	<div id="building_panel" class="lcm_focus" title="施設の選択">
		<select id="building_group_list" title="施設グループ絞り込み">
			<option value="">絞り込み：全施設
			<?php foreach($building_group_list as $row) { ?>
				<option value="<?php print $row['item_group2']; ?>" <?php if (\Session::get($kind_name . "narrow_bgid") == $row['item_group2'] && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $row['item_group2']; ?>
			<?php } ?>
		</select>
		<div class="select_multiple_wrapper">
			<div class="select_multiple_content select_kizon">
				<h3 class="ac">選択済み</h3>
				<select id="building_kizon" name="building_kizon" size="2" style="width:11em;height:200px;" title="選択済み施設" multiple>
				<?php foreach($select_building_list as $row) { ?>
					<option value="<?php echo $row->item_id; ?>"><?php echo $row->item_name; ?></option>
				<?php } ?>
				</select>

			</div><!-- /.select_multiple_content -->
			<div class="select_multiple_content button_group">
				<input type="button" value="解除"  class="button small" onclick="javascript:select_building('minus');">
				<input type="button" value="選択" class="button small primary" onclick="javascript:select_building('plus');">
			</div><!-- /.select_multiple_content -->
			<div class="select_multiple_content select_new">
				<h3 class="ac">ここから選択</h3>
				<select id="building_new" name="building_new" size="2" style="width:11em;height:200px;" title="施設選択肢" multiple>
				<?php foreach($non_select_building_list as $row) { ?>
					<option value="<?php echo $row->item_id; ?>"><?php echo $row->item_name; ?></option>
				<?php } ?>
				</select>
			</div><!-- /.select_multiple_content -->
		</div><!-- /.select_multiple_wrapper -->
	</div>
</td>
</tr>

<tr>
	<th class="ar min"><?php echo $form->field('group_kb')->set_template('{required}{label}'); ?></th>
	<td>
		<?php echo $form->field('group_kb')->set_template('{error_msg}{fields}<label>{field} {label}</label> {fields}'); ?>
		<?php echo $form->field('group_detail')->set_template('{error_msg}{field}'); ?>
	</td>
</tr>

<tr>
	<th class="ar"><?php echo $form->field('purpose_kb')->set_template('{required}{label}'); ?></th>
	<td><?php echo $form->field('purpose_kb')->set_template('{error_msg}{field}'); ?></td>
</tr>
<?php /* ?>
<tr>
	<th><?php echo $form->field('purpose_text')->set_template('{required}{label}'); ?></th>
	<td><?php echo $form->field('purpose_text')->set_template('{error_msg}{field}'); ?></td>
</tr>
<?php */ ?>
<?php echo $form->field('purpose_text')->set_type('hidden'); ?>
<tr>
	<th class="ar"><?php echo $form->field('user_num')->set_template('{required}{label}'); ?></th>
	<td><?php echo $form->field('user_num')->set_template('{error_msg}{field}'); ?>人</td>
</tr>

<tr>
	<th class="ar"><?php echo $form->field('user_id')->set_template('{required}{label}'); ?></th>
	<td>
		<select id="group_list_create_user" title="グループ絞り込み">
			<option value="">絞り込み：全グループ
			<?php foreach($group_list as $key => $value) { ?>
				<option value="<?php print $key; ?>"><?php  print $value; ?>
			<?php } ?>
		</select>
		<?php echo $form->field('user_id')->set_template('{error_msg}{field}'); ?>
	</td>
</tr>
<?php if( $locomo['controller']['name'] !== "\Controller_Scdl"):?>
<tr>
<th class="ar">メンバー</th>
<td>
	<div id="member_panel" class="lcm_focus" title="メンバーの選択">
		<select id="group_list" title="グループ絞り込み">
			<option value="">絞り込み：全グループ
			<?php foreach($group_list as $key => $value) { ?>
				<option value="<?php print $key; ?>" <?php if (\Session::get($kind_name . "narrow_ugid") == $key && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $value; ?>
			<?php } ?>
		</select>
		<div class="select_multiple_wrapper">
			<div class="select_multiple_content select_kizon">
				<h3 class="ac">選択済み</h3>
				<select id="member_kizon" name="member_kizon" size="2" style="width:11em;height:200px;" title="選択済みメンバー" multiple>
				<?php foreach($select_user_list as $row) { ?>
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
					foreach($non_selected_user_list as $row) {
					?>
					<option value="<?php echo $row->id; ?>"><?php echo $row->display_name; ?></option>
				<?php } ?>
				</select>
			</div><!-- /.select_multiple_content -->
		</div><!-- /.select_multiple_wrapper -->
		<label for="form_attend_flg_0"><?php echo $form->field('attend_flg')->set_template('{error_msg}{field}'); ?>出席確認を取る</label>
	</div>
</td>
</tr>
<?php endif; ?>

<?php echo $form->field('created_at')->set_template('{error_msg}{field}'); ?>
<?php echo $form->field('is_visible')->set_template('{error_msg}{field}'); ?>
</tbody>
</table>

<?php echo $form->field('kind_flg')->set_template('{error_msg}{field}'); ?>

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

</div><!--/form_group-->


<script>
<!-- JSに移す -->
change_repeat_kb_area();
$("#form_repeat_kb").change(function(event){
	change_repeat_kb_area();
});
$("#group_list").change(function(event) {
	get_group_user(event, $("#group_list").val(), "member_new");
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

function form_group_detail_change(e) {
	$("#form_group_kb_1").val(['2']);
}

/**
 * [change_repeat_kb_area description]
 * @return {[type]} [description]
 */
function change_repeat_kb_area() {
	if ($("#form_repeat_kb").val() == 0 || $("#form_repeat_kb").val() == 1 || $("#form_repeat_kb").val() == 2) {
		// なし
		$("#span_week_kb").css({'display': 'none'});
		$("#span_week_number").css({'display': 'none'});
		$("#span_target_day").css({'display': 'none'});
		$("#span_target_month").css({'display': 'none'});
	} else if ($("#form_repeat_kb").val() == 3) {
		$("#span_week_kb").css({'display': 'inline-block'});
		$("#span_week_number").css({'display': 'none'});
		$("#span_target_day").css({'display': 'none'});
		$("#span_target_month").css({'display': 'none'});
	} else if ($("#form_repeat_kb").val() == 4) {
		$("#span_week_kb").css({'display': 'none'});
		$("#span_week_number").css({'display': 'none'});
		$("#span_target_day").css({'display': 'inline-block'});
		$("#span_target_month").css({'display': 'none'});
	} else if ($("#form_repeat_kb").val() == 5) {
		$("#span_week_kb").css({'display': 'none'});
		$("#span_week_number").css({'display': 'none'});
		$("#span_target_day").css({'display': 'inline-block'});
		$("#span_target_month").css({'display': 'inline-block'});
	} else if ($("#form_repeat_kb").val() == 6) {
		$("#span_week_kb").css({'display': 'inline-block'});
		$("#span_week_number").css({'display': 'inline-block'});
		$("#span_target_day").css({'display': 'none'});
		$("#span_target_month").css({'display': 'none'});
	}
	
	//区分選択により時間入力欄を移動
	if($("#form_repeat_kb").val() == 0){
		$('#field_set_time').hide();
		$('#form_start_time').appendTo('#span_date_start');
		$('#form_end_time').appendTo('#span_date_end');
	}else{
		$('#field_set_time').prepend($('#form_start_time')).append($('#form_end_time')).show();
	}

	//区分選択により、期間の入力欄の種類を変更 //まだ入力が未対応なのでコメントアウト
/*	if($("#form_repeat_kb").val() < 4){
		$('#form_start_date, #form_end_date').removeClass('month');
		//入力欄の値もyy-mmに変更したい。datepicker上の値は"1日"が補完される
	}else{
		$('#form_start_date, #form_end_date').addClass('month');
	}
*/
}
is_allday();
$('#form_allday_kb').change(function(){
	is_allday()
});
function is_allday(){
	if($('#form_allday_kb').prop('checked')){
		$('#form_start_time').val('0:00');
		$('#form_end_time').val('23:59');
		$('#form_start_time, #form_end_time').attr('readonly',true);
	}else{
		$('#form_start_time, #form_end_time').attr('readonly',false);
	}
}

make_hidden_members();
make_hidden_buildings();
/**
 * 
 * @param  {[type]} target [description]
 * @return {[type]}        [description]
 */
function select_member(target) {
	var from = (target == "plus" ? "new" : "kizon");
	var to = (target == "plus" ? "kizon" : "new");
	var val = $("#member_" + from).val();
	if ( val == "" || !val) { return; }
	for(var i=0; i < val.length; i++){
		$("#member_" + to).append($('<option>').html($("#member_" + from + " option[value="+val[i]+"]").text()).val(val[i]));
	}
	$("#member_" + from + " > option:selected").remove();
	$("#member_" + from).selectedIndex = 0;
	make_hidden_members();
}
function make_hidden_members() {
	if (!$("#hidden_members")[0]) {
		$('<input>').attr({
		    type: 'hidden',
		    id: 'hidden_members',
		    name: 'hidden_members',
		    value: ''
		}).appendTo('form');
	}
	var hidden_str = "";
	// 配列に入れる
	$("#member_kizon option").each(function() {
		hidden_str += "/" + $(this).val();
    });
	$("#hidden_members").val(hidden_str);
}

//ダブルクリックで実行
$('#member_new, #member_kizon').dblclick(function(){
	var target = this.id == 'member_new' ? 'plus' : 'kizon';
	select_member(target);
});


/**
 * [select_building description]
 * @param  {[type]} target [description]
 * @return {[type]}        [description]
 */
function select_building(target) {
	var from = (target == "plus" ? "new" : "kizon");
	var to = (target == "plus" ? "kizon" : "new");
	var val = $("#building_" + from).val();
	if ( val == "" || !val) { return; }
	for(var i=0; i < val.length; i++){
		$("#building_" + to).append($('<option>').html($("#building_" + from + " option[value="+val[i]+"]").text()).val(val[i]));
	}
	$("#building_" + from + " > option:selected").remove();
	$("#building_" + from).selectedIndex = 0;
	make_hidden_buildings();
}
function make_hidden_buildings() {
	if (!$("#hidden_buildings")[0]) {
		$('<input>').attr({
		    type: 'hidden',
		    id: 'hidden_buildings',
		    name: 'hidden_buildings',
		    value: ''
		}).appendTo('form');
	}
	var hidden_str = "";
	// 配列に入れる
	$("#building_kizon option").each(function() {
		hidden_str += "/" + $(this).val();
    });
	$("#hidden_buildings").val(hidden_str);
}
//ダブルクリックで実行
$('#building_new, #building_kizon').dblclick(function(){
	var target = this.id == 'building_new' ? 'plus' : 'kizon';
	select_building(target);
});




var base_uri = $('body').data('uri');

function get_group_user(e, groupId, targetEle) {

	var targetEle = targetEle;
	var group_id = groupId;

	var now_members = new Object();
	var kizon_options = document.getElementById('member_kizon').options;
	for(var i = 0; i < kizon_options.length; i++){
		now_members['member' + kizon_options[i].value] = 1;
	};

	$.ajax({
		url: base_uri + 'scdl/get_user_list.json',
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

function get_group_building(e) {

	var group_id = $("#building_group_list").val();

	var now_buildings = new Object();
	var kizon_options = document.getElementById('building_kizon').options;
	for(var i = 0; i < kizon_options.length; i++){
		now_buildings['building' + kizon_options[i].value] = 1;
	};

	$.ajax({
		url: base_uri + 'scdl/get_building_list.json',
		type: 'post',
		data: 'bid=' + group_id,
		success: function(res) {

			exists = JSON.parse(res);

			document.getElementById("building_new").options.length=0;

			for(var i in exists) {
				if (!now_buildings['building' + exists[i]['item_id']]) {
					$("#building_new").append($('<option>').html(exists[i]['item_name']).val(exists[i]['item_id']));
				}
			}
		
		}
	});
}
</script>


<?php echo \Form::close(); ?>
