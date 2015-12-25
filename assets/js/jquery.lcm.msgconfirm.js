// 確認メッセージ
// :input.confirm, a.confirm, data-jslcm-msg
// data-jslcm-msgの値がなければ、リンクの文字列もしくはalt、またはinputの値を元に確認メッセージを表示する
// .confirmはlcm_つけたほうがいいかなあ
$('a.confirm, :input.confirm').click(function(){
	var msg = $(this).data('jslcmMsg');
	if(msg){
		msg = msg.replace(/\\n/g, "\n");
	}else if($(this).text()){
		msg = $(this).text()+'しますか？';
	}else if($(this).children('img')[0]){
		msg = $(this).children('img').attr('alt')+'しますか？';
	}else if($(this).is(':input')){
		msg = $(this).val()+'しますか？';
	}else{
		msg = 'よろしいですか？';
	}
	if (!confirm(msg)){
		return false;
	}
});
//ページ遷移警告抑止
$('.confirm').click(function(){
		$(window).off('beforeunload');
});


// フォーム

//ページ遷移時の警告
//エラー時には必ず。//フォームと無関係のエラーは？
//login画面とsubmitがない場合(編集履歴など)では出さない。編集履歴はむしろdisableにするほうがよい？
function check_formchange(){
	var $input_time, len, $el;
	$input_time = $('.datetime','.time');//datetimeの枠にフォーカスした際のchange周りのなにか。あとでもういちど確認
	len = $input_time.length;
	for( var n = len; n > 0; n--){
		$el = $input_time.eq(0);
		$el.data('val', $el.val());
	}

	function confirm_beforeunload(){
		$(window).on('beforeunload', function(){
			return '変更内容は保存されていません。';
		});
	}

	$('form').change( function(e){
		e = e ? e : event;
		var $t = $(e.target);
		if(!( $t.closest('.search, .index_toolbar')[0]
			|| $t.hasClass('checkbox_binded')
			|| $t.hasClass('datetime') && $t.val() == $t.data('val') )
		){
		//変更のあった要素のうち、.search form内や、一括処理用のチェックボックス、datetimepickerは除外
			confirm_beforeunload();
		}
	});
	if($('#alert_error').children('ul.list')[0]
		|| $('.lcm_module_reserve #alert_error')[0]
		|| $('.lcm_ctrl_-controller_scdl #alert_error')[0]
	){
		confirm_beforeunload();
	}
}

if($('a:submit, input:submit')[0] && !$('body').hasClass('lcm_action_login')){
	check_formchange();
}

//ページ遷移警告抑止
$('a:submit, input:submit').click(function(){//該当する場合遷移警告しない
		$(window).off('beforeunload');
});



