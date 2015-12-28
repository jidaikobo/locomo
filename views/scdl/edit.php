<?php
if (isset($overlap_result) && count($overlap_result)) {
	$display_results = array();
	foreach($overlap_result as $each_result):
		// ？時間表示の整形？
		$each_result->display_startdate = date('Y年n月j日', strtotime($each_result->start_date . " " . $each_result->start_time));
		$each_result->display_enddate = date('Y年n月j日', strtotime($each_result->end_date . " " . $each_result->end_time));
		$each_result->display_starttime = date('i', strtotime($each_result->start_time))==0 ?
			date('G時', strtotime($each_result->start_date . " " . $each_result->start_time)) :
			preg_replace("/時0/", "時", date('G時i分', strtotime($each_result->start_date . " " . $each_result->start_time)));
		$each_result->display_endtime = date('i', strtotime($each_result->end_time))==0 ?
			date('G時', strtotime($each_result->end_date . " " . $each_result->end_time)) :
			preg_replace("/時0/", "時", date('G時i分', strtotime($each_result->start_date . " " . $each_result->end_time)));

		if ($each_result->repeat_kb == 0 && $each_result->display_startdate != $each_result->display_enddate) { //開始日終了日が異なる場合は連続した期間扱い
	/*
		//開始日〜終了日 (何時〜何時）開始日と終了日を比較しつつ、同年や同月の表示省略
			if(date('Y', strtotime($each_result->start_date)) == date('Y', strtotime($each_result->end_date))) : //年が同じかどうか
				$each_result->display_startdate = intval(date("Y")) == $year ? //現在と同年なら省略
					date('n月j日', strtotime($each_result->start_date)) :
					date('Y年n月j日', strtotime($each_result->start_date));
				$each_result->display_enddate = date('n', strtotime($each_result->start_date)) == date('n', strtotime($each_result->end_date)) ? //同月なら省略
					date('j日', strtotime($each_result->end_date)) :
					date('n月j日', strtotime($each_result->end_date));
			endif;
	*/
			if($each_result->allday_kb): // 終日は時間を省略
				$result_str = '<span class="nowrap">'.$each_result->display_startdate.' <span class="sr_replace to"><span>から</span></span></span> <span class="nowrap">'.$each_result->display_enddate.'</span>';
			else:
				$result_str = '<span class="nowrap">'.$each_result->display_startdate.' '.$each_result->display_starttime.'<span class="sr_replace to"><span>から</span></span></span> <span class="nowrap">'.$each_result->display_enddate.' '.$each_result->display_endtime.'</span>';
			endif;
		} else { //期間でないならば繰り返し
			if($each_result->allday_kb){
				$result_str = $each_result->display_startdate.'<span class="sr_replace to"><span>から</span></span>'.$each_result->display_startdate.'<span class="nowrap">終日</span>';
			}else{
				$result_str = $each_result->display_startdate.'<span class="sr_replace to"><span>から</span></span>'.$each_result->display_startdate.'<span class="nowrap">'.$each_result->display_starttime . '<span class="sr_replace to"><span>から</span></span></span> <span class="nowrap">' . $each_result->display_endtime.'</span>';
			}
		}
	$display_results[] = $result_str;
	echo '#'.$each_result['id'].': '.$each_result['targetdata'].' '.$result_str.' '.$each_result['title_text'].'<br>';

	endforeach;
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
			<span id="span_public_display" class="display_inline_block">
				<?php echo $form->field('public_display')->set_template('{error_msg}{fields}<label>{field} {label}</label> {fields}'); ?>
			</span>

<?php /* ?>
<span class="nowrap">
	<?php echo $form->field('title_importance_kb')->set_template('{label}'); ?>
		<?php echo $form->field('title_importance_kb')->set_template('{error_msg}{field}'); ?>
		</span>
			<span class="nowrap">
				<?php echo $form->field('title_kb')->set_template('{label}'); ?>
				<?php echo $form->field('title_kb')->set_template('{error_msg}{field}'); ?>
			</span>
<?php */ ?>
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
<?php if( $locomo['controller']['name'] !== "\Controller_Scdl"): // 施設予約では、公開用の設定をする ?>
	<div class="input_group">
		<h2>実使用時間</h2>
		<div id="field_term" class="lcm_focus field" title="実使用時間">
			<span id="span_public_time_start" class="">
			<?php echo $form->field('public_start_time')->set_template('{error_msg}{field}'); ?>
			</span> から <span id="span_public_time_end" class="display_inline_block" style="margin-right: 1em;">
			<?php echo $form->field('public_end_time')->set_template('{error_msg}{field}'); ?>
			</span>
		</div>
	</div><!-- /.input_group -->
<?php endif; ?>

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
				<select id="group_list" class="multiple_select_narrow_down" data-target-id="user_group_selects" title="グループ絞り込み">
					<option value="">絞り込み：全グループ</option>
					<?php foreach($group_list as $key => $value) { ?>
						<option value="<?php print $key; ?>" <?php if (\Session::get($kind_name . "narrow_ugid") == $key && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $value; ?>
					<?php } ?>
				</select>
				<div id="user_group_selects" class="lcm_multiple_select" data-hidden-item-id="hidden_members">
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
				<select id="building_group_list" class="multiple_select_narrow_down" data-uri="scdl/building_list.json" data-target-id="building_group_selects" title="施設グループ絞り込み">
					<option value="">絞り込み：全施設</option>
					<?php foreach($building_group_list as $row) { ?>
						<option value="<?php print $row['item_group2']; ?>" <?php if (\Session::get($kind_name . "narrow_bgid") == $row['item_group2'] && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $row['item_group2']; ?>
					<?php } ?>
				</select>
				</div>
				<div id="building_group_selects" class="lcm_multiple_select" data-hidden-item-id="hidden_buildings">
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
				<select id="group_list" class="multiple_select_narrow_down" data-target-id="user_group_selects" title="グループ絞り込み">
					<option value="">絞り込み：全グループ
				<?php foreach($group_list as $key => $value): ?>
					<option value="<?php print $key; ?>" <?php if (\Session::get($kind_name . "narrow_ugid") == $key && count(\Input::post()) == 0) { print "selected"; } ?>><?php  print $value; ?>
				<?php endforeach; ?>
				</select>
				<div id="user_group_selects" class="lcm_multiple_select" data-hidden-item-id="hidden_members">
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
<!-- jsに移す -->
change_repeat_kb_area();

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

	//区分選択により、期間の入力欄の種類を変更
	if(repeat_kb == 5){
		$('#form_start_date, #form_end_date').removeClass('month').addClass('year');
	}else if(repeat_kb == 4 || repeat_kb == 6){
		$('#form_start_date, #form_end_date').removeClass('year').addClass('month');
	}else{
		$('#form_start_date, #form_end_date').removeClass('month year');
	}

}

//終日選択反映
is_allday();
function is_allday(){
	if($('#form_allday_kb').prop('checked')){
		$('#form_start_time').val('0:00');
		$('#form_end_time').val('23:59');
		$('#form_start_time, #form_end_time').attr('readonly',true);
	}else{
		$('#form_start_time, #form_end_time').attr('readonly',false);
	}
}
//start_timeを変更した際にend_timeに+1時間を入れる // 時間を変更していても引っ張られて良い？
$('#form_start_time').on('change', function(){
	if($('#form_start_time').val()){
		var hour   = $('#form_start_time').val().slice(0, 2)-0;
		var minute = $('#form_start_time').val().slice(-2);
		hour = ((hour+1)+'').slice(-2);
		if(hour==24) hour = 23; //23:59?
		$('#form_end_time').val(hour+':'+minute);
	}
});

//時間の設定を外部表示のplaceholderに
$('#form_start_time, #form_end_time').on('change', function(){
	if($(this).is('#form_start_time')){ //すでに値が入っている場合どうする？ placeholderだからよい？ //選択時のtimepickerの開始値とか、ずっとplaceholderにいれてていいの？とか
		if($('#form_public_start_time').val()==''){
			$('#form_public_start_time').attr('placeholder', $('#form_start_time').val());
		}
	}else{
		if($('#form_public_end_time').val()==''){
			$('#form_public_end_time').attr('placeholder', $('#form_end_time').val());
		}
	}
});

//実使用時間の片方のみに入力した場合に、もう一方に設定時間の値を入力
$('#form_public_start_time, #form_public_end_time').on('change', function(){
	$from = $(this).is('#form_public_start_time') ? $('#form_public_start_time') : $('#form_public_end_time');
	$to = $(this).is('#form_public_start_time') ? $('#form_public_end_time') :  $('#form_public_start_time');
	time_completion($from,$to);

	function time_completion($from,$to){
		if($from.val()){ //値が入力された場合
			if(!$to.val()){
				if($from.is('#form_public_start_time')){
					$to.val($('#form_end_time').val());
				}else{
					$to.val($('#form_start_time').val());
				}
			}
		}else{ //値が削除された場合
			if(!$to.val()){
				if($from.is('#form_public_start_time')){
					$from.val($('#form_start_time').val());
				}else{
					$from.val($('#form_end_time').val());
				}
			}
		}
		$from.focus();
	}

});

</script>


<?php echo \Form::close(); ?>
