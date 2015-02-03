<?php echo \Form::open(); ?>


<!--form_group-->
<div class="form_group">
<?php
	// use model's form definition instead of raw-like html
	//echo $form;
?>

<?php
if (isset($overlap_result) && count($overlap_result)) {
?>
<table>
	<tr>
		<th>
			氏名
		</th>
		<th>
			日時
		</th>
		<th>
			タイトル
		</th>
	</tr>
<?php 
	foreach ($overlap_result as $v) {
?>
	<tr>
		<td>
		<?php print $v['user_data']->username; ?>
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
<?php
	}
}
?>
</table>
<table class="formtable">
<tr>
	<th><?php echo $form->field('repeat_kb')->set_template('{label}{required}'); ?></th>
	<td>
		<?php echo $form->field('repeat_kb')->set_template('{error_msg}{field}'); ?>
	</td>
</tr>
<tr>
	<th>予定日指定</th>
	<td>
	<span id="span_target_month"><?php echo $form->field('target_month')->set_template('{error_msg}{field}'); ?>月</span>
	<span id="span_target_day"><?php echo $form->field('target_day')->set_template('{error_msg}{field}'); ?>日</span>
	<span id="span_week_kb"><?php echo $form->field('week_kb')->set_template('{error_msg}{field}'); ?>曜日</span>
		<table id="">
			<tr>
				<th>期間</th>
				<td>
					<?php echo $form->field('start_date')->set_template('{error_msg}{field}'); ?>
					〜
					<?php echo $form->field('end_date')->set_template('{error_msg}{field}'); ?>
				</td>
			</tr>
			<tr>
				<th>時刻</th>
				<td>
					<?php echo $form->field('start_time')->set_template('{error_msg}{field}'); ?>
					〜
					<?php echo $form->field('end_time')->set_template('{error_msg}{field}'); ?>
				</td>
			</tr>
		</table>

	</td>
</tr>

<tr>
	<th><?php echo $form->field('title_text')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('title_text')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('title_importance_kb')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('title_importance_kb')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('title_kb')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('title_kb')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th>詳細設定 </th>
	<td>
	<?php echo $form->field('provisional_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<?php echo $form->field('unspecified_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<?php echo $form->field('allday_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<?php echo $form->field('private_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	<?php echo $form->field('overlap_kb')->set_template('{error_msg}<label>{field} {label}</label>'); ?>
	</td>
</tr>

<tr>
	<th><?php echo $form->field('message')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('message')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('group_kb')->set_template('{label}{required}'); ?></th>
	<td>
		<?php echo $form->field('group_kb')->set_template('{error_msg}{fields}<label>{field} {label}</label> {fields}'); ?>
		<?php echo $form->field('group_detail')->set_template('{error_msg}{field}'); ?>
	</td>
</tr>

<tr>
	<th><?php echo $form->field('purpose_kb')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('purpose_kb')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('purpose_text')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('purpose_text')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('user_num')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('user_num')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('user_id')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('user_id')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('created_at')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('created_at')->set_template('{error_msg}{field}'); ?></td>
</tr>

<tr>
	<th><?php echo $form->field('is_visible')->set_template('{label}{required}'); ?></th>
	<td><?php echo $form->field('is_visible')->set_template('{error_msg}{field}'); ?></td>
</tr>

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

<div id="member_panel">
	<table>
		<tr>
			<td>
				<select id="member_kizon" name="member_kizon" size="2" style="width:100px;height:200px;">
				<?php foreach($select_user_list as $row) { ?>
					<option value="<?php echo $row->id; ?>"><?php echo $row->username; ?></option>
				<?php } ?>
				</select>
			</td>
			<td><input type="button" value="選択" onclick="javascript:select_member('plus');" /><br /><input type="button" value="解除" onclick="javascript:select_member('minus');" /></td>
			<td>
				<select id="member_new" name="member_new" size="2" style="width:100px;height:200px;">
					<?php 
					foreach($non_selected_user_list as $row) {
					?>
					<option value="<?php echo $row->id; ?>"><?php echo $row->username; ?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
	</table>
	<?php echo $form->field('attend_flg')->set_template('{error_msg}{field}'); ?>出席確認を取る
</div>

<div id="building_panel">
	<table>
		<tr>
			<td>
				<select id="building_kizon" name="building_kizon" size="2" style="width:100px;height:200px;">
				<?php foreach($select_building_list as $row) { ?>
					<option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
				<?php } ?>
				</select>
			</td>
			<td><input type="button" value="選択" onclick="javascript:select_building('plus');" /><br /><input type="button" value="解除" onclick="javascript:select_building('minus');" /></td>
			<td>
				<select id="building_new" name="building_new" size="2" style="width:100px;height:200px;">
				<?php foreach($non_select_building_list as $row) { ?>
					<option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
	</table>
</div>


<script>
<!-- JSに移す -->
change_repeat_kb_area();
$("#form_repeat_kb").change(function(event){
	change_repeat_kb_area();
});


/**
 * [change_repeat_kb_area description]
 * @return {[type]} [description]
 */
function change_repeat_kb_area() {
	if ($("#form_repeat_kb").val() == 0 || $("#form_repeat_kb").val() == 1 || $("#form_repeat_kb").val() == 2) {
		// なし
		$("#span_week_kb").css({'display': 'none'});
		$("#span_target_day").css({'display': 'none'});
		$("#span_target_month").css({'display': 'none'});
	} else if ($("#form_repeat_kb").val() == 3) {
		$("#span_week_kb").css({'display': 'block'});
		$("#span_target_day").css({'display': 'none'});
		$("#span_target_month").css({'display': 'none'});
	} else if ($("#form_repeat_kb").val() == 4) {
		$("#span_week_kb").css({'display': 'none'});
		$("#span_target_day").css({'display': 'block'});
		$("#span_target_month").css({'display': 'none'});
	} else if ($("#form_repeat_kb").val() == 5) {
		$("#span_week_kb").css({'display': 'none'});
		$("#span_target_day").css({'display': 'block'});
		$("#span_target_month").css({'display': 'block'});
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
	if ($("#member_" + from).val() == "" || !$("#member_" + from).val()) { return; }
	$("#member_" + to).append($('<option>').html($("#member_" + from + " option:selected").text()).val($("#member_" + from).val()));
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
/**
 * [select_building description]
 * @param  {[type]} target [description]
 * @return {[type]}        [description]
 */
function select_building(target) {
	var from = (target == "plus" ? "new" : "kizon");
	var to = (target == "plus" ? "kizon" : "new");
	if ($("#building_" + from).val() == "" || !$("#building_" + from).val()) { return; }
	$("#building_" + to).append($('<option>').html($("#building_" + from + " option:selected").text()).val($("#building_" + from).val()));
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
</script>


<?php echo \Form::close(); ?>
