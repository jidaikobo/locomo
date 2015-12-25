// トリガー要素にぶら下がるフロートウィンドウ
// トリガーのクリックもしくはアクセスキーによる表示・非表示・フォーカス移動
// 移動・拡大縮小はjquery-uiのdraggable、resizableで行うのがよい？
// 閉じた際に移動・変形前の状態に戻す場合はトリガーに.lcm_reset_styleを与えておく
// .lcm_floatwindow, .toggle_floatwindow, .lcm_floatwindow_parent, lcm_load_txt, .lcm_floatwindow_title, .lcm_close_window
// .toggle_floatwindowのID+'_window'もしくは、data-target-windowの値をIDに持つ要素を相手にする。
// アクセスキーを付与する場合は、トリガーに設定する
if(typeof(lcm_env)=='undefined') var lcm_env = new Object();

function lcm_floatwindow(elm){
	e = elm;
	if(!$.isPlainObject(e)) e.preventDefault();//クリックイベント以外(アクセスキー等)の場合を除外 ？再確認必要？
	var $trigger = $(e.target).closest('.toggle_floatwindow');
	if(!$trigger[0]) $trigger = $('#'+e.trigger);  // ヘルプのタイトルクリック時
	var $parent = $(e.target).closest('.lcm_floatwindow_parent').eq(0);
	var $target = $('#'+$trigger[0].id+'_window');
	if(!$target[0]) $target = $('#'+$trigger.data('targetWindow'));
	if(!$target[0]) return; // ウィンドウが用意されていなければ終了

	var $load_txt = $target.find('.lcm_load_txt');
	if($load_txt[0]) load_window($trigger, $target, $load_txt); //中身をAjaxで取得する場合

	$target.appendTo($parent);
	if($target.is(':visible')){
		if(!e.trigger){ // タイトルをクリックした場合を除外
			$target.lcm_close_window($target);
			$trigger.focus();
		}
		return;
	} else {
		$target.show();
	}
	setTimeout(function($target){//アクセスキーの場合、キーを設定した要素にフォーカスするのでその後に実行
		$target.find('.lcm_floatwindow_title a').focus();
	},0 ,$target);

}

//ウィンドウの内容読み込み。？分ける必要？
//triggerは呼び出すボタン、このうしろに呼び出したウィンドウを表示する。targetはウィンドウそのもの。
function load_window($trigger, $target, $load_txt) {
	var is_prepare = [];
	var id = $target[0].id;
	is_prepare[id] = false;// 重複読み込みの防止。idがあること前提で良いのかなあ……
	if(!is_prepare[id]){
		var uri = $trigger.data('uri');
		$.ajax({
			url: uri,
			dataType: 'html',
		})
		.success(function(data) {
			$load_txt.html(data);
			is_prepare[id] = true;
		});
	}
}

// ウィンドウを表示する
$('.toggle_floatwindow').on('click', function(){
	var e = e ? e : event;
	e.preventDefault();
	e.stopPropagation();
	lcm_floatwindow(e);
	return false;
});

// ウィンドウを閉じる
$('.lcm_close_window').on('click', function(e){
	e = e ? e : event;
	$(this).lcm_close_window($(this).parent());
	e.preventDefault();//抑止しておかないとIEでページ遷移前の警告が出る
});

$.fn.lcm_close_window = function($w){
	$w.hide();
	if($w.find('.lcm_close_window').hasClass('lcm_reset_style')){
		$w.removeAttr('style').hide(); //display: none;も消えるので改めてhide。hideの前にremoveattrをしてしまうと、変形が見えてよろしくない。
	}
}

	
// くわえて、内容に合わせて表示前にリサイズしたい
