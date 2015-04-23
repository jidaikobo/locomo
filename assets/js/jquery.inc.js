/*
//console非対応ブラウザのための用意 うまく動かないので、要見直し
// consoleが使えない場合は空のオブジェクトを設定しておく
if( typeof window.console === undefined ){
 window.console = {};
}
// console.logがメソッドでない場合は空のメソッドを用意する
if( typeof window.console.log !== "function" ){
 window.console.log = function(){};
}
*/
//loggedin
//テスト環境やローカル開発環境での表示
$(function(){
	(function(){
		var host, body, str, info, topinfo;
		host = location.host;
		body = $('body');
		if(!str && host!='kyoto-lighthouse.org'){
			if(host == 'www.kyoto-lighthouse.org'){
				body.addClass('testserver');
				str = '--- テスト環境です　改造要望等はまずこちらで実験します　データは頻繁にリセットされます　動作テストなどご自由に操作いただけます ---';
			}else{
				str = '--- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ';
			}
			if(str){
				info = $('<p class="develop_info"/>').prepend(str);
				body.append(info);
				if($('body').hasClass('loggedin testserver')){
					topinfo = info.clone().addClass("top").css('top', $('#adminbar').outerHeight()+'px');
					$('#main_content').prepend(topinfo);
				}
			}
		}
	})();
});


//チェックボックス全選択
$(function() {
	var checkboxes = $('.checkbox_binded');
	$('.check_all').on('click', function(e) {
		e.preventDefault();
		checkboxes.prop('checked', true).trigger('change');
	});
	$('.uncheck_all').on('click', function(e) {
		e.preventDefault();
		checkboxes.prop('checked', false).trigger('change');
	});
	$(document).on('click', '.has_checkbox tr' ,function(e){
		var t, tr, checkbox, prop;
		e = e ? e : event;
		t = $(e.target);
		tr = $(t.closest('tr'));
//		if(t.closest('table').hasClass('tekitou')) return;//なにか適当なクラスをつけておけばキャンセルできるように
		if(!t || (t.is('label'))){
			e.preventDefault();
		}
		if(t.is('input') || t.is('a')){
			return;
		}

		checkbox = tr.find('.checkbox_binded');
		if(!$(checkbox).prop('checked')){
			prop = true; 
		}else{
			prop = false;
		}
		$(checkbox).prop('checked', prop).trigger('change');
	});

	function set_class(){
		var t = $.isWindow(this) ? $('.checkbox_binded:checked') : this;
		if(!$.isWindow(this)){
			$(this).closest('tr').toggleClass('checked');
		}else{
			$(document).find('tr').has($('input[type="checkbox"]:checked')).toggleClass('checked');
		}
	}
	set_class();
	checkboxes.change(set_class);
});


// ヘルプ呼び出し
function show_help(e){
	e = e ? e : event;
	if(!$.isPlainObject(e)) e.preventDefault();//クリックイベント以外(アクセスキー等)の場合を除外
	var prepare_help = false;//重複読み込みの防止
	$(function(){
		if(!prepare_help){
			var uri = $('#lcm_help').data('uri');
			$.ajax({
				url: uri,
				dataType: 'html',
			})
			.success(function(data) {
				$('#help_txt').html(data);
				prepare_help = true;
			})
		}

		if($('#help_window').is(':visible')){
			if(!e.flg){
				$('#help_window').lcm_close_window($('#help_window'));
				$('#lcm_help').focus();
			}
			return;
		} else {
			$('#help_window').show()
		}
		setTimeout(function(){//アクセスキーの場合、キーを設定した要素にフォーカスするのでその後に実行
			$('#help_title_anchor').focus();
		},0);
	});
}
$(function(){
	$('#lcm_help').click(show_help);
});
// ヘルプウィンドウリサイズ



//モーダル
//書き直す。
//modalを呼ぶ側にdata-jslcm-modal-id?を書き、相手を指定する。モーダル表示中はフォーカスを制御し、それ以外の部分については操作不可とする。
//例えばNetReaderで制御を抜けた時はどうするの、とか、考えておくとよいのかも。(NetReader側では一時的にmodal外をdisplay: none;にするとか？　その場合、うまくイベントを抜けられなかったときが不安)
/*function lcm_modal(id){
	var el, wrapper, closelink;
	el = document.getElementById(id);
	if(el){
		$(function(){
			el = $('#'+id);
			wrapper = document.createElement('div');
			closelink = document.createElement('a');
			wrapper.id = 'modal_wrapper';
			wrapper.dataset.modalid = id;
			closelink.id = 'close_modal';
			closelink.href = 'javascript: void(0);';
			closelink.innerHTML = '閉じる';
			closelink.setAttribute('tabindex','0');
			
			$(event.target).addClass('modal_parent');
			el.addClass('on')
			//画面中央表示のためにbodyのはじめに移動、その前に元に戻す時のため#modal_wrapperを追加 戻す必要ある？appendのほうがよい？ 読み上げ的に冒頭にあったほうがよいのかなあ
				.after(wrapper)
				.prependTo(document.body)
				.prepend(closelink);
			$(document).find('#close_modal').focus();
			$(el).set_center().set_tabindex();
		});
	}
	event.stopPropagation();
	return false;
}
*/



$(function(){
/*=== 基本的な設定 ===*/
//JavaScript有効時に表示、無効時にはCSSで非表示
$('.hide_if_no_js').removeClass('hide_if_no_js');
$('.hide_if_no_js').find(':disabled').prop("disabled", false);

//.show_if_no_js noscript的な扱い?
$('.show_if_no_js').remove();

//for NetReader
//NetReaderで付与されたスタイルに負けることがあるので、.hidden_itemをインラインスタイルでdisplay: none;
$('.hidden_item').hide();

//アクセスキーをもつ要素へのタイトル付与
//accessKeyLabelが取得できないブラウザではaccessKeyを表示する。
function add_accesskey_title(){
	var str, txt, label;
	label = this.accessKeyLabel;
	label = label ? label : this.accessKey;
	if(label){
		txt = $(this).clone(false);
		txt.find('.skip').remove();
		str = ( $(this).attr('title') || txt.text() || $(this).children().attr('alt') );
		$(this).attr('title',str+'['+label+']');
	}
}
$(document).find('[accesskey]').each(add_accesskey_title);
});


//大きなくくり。あとでバラス
$(function(){

/*=== 環境の取得 ===*/
//UA
userAgent = window.navigator.userAgent;
isNetReader   = userAgent.indexOf('NetReader') > 0 ? true : false;
$('body').addClass(isNetReader ? 'netreader' : '');
isTouchDevice = userAgent.indexOf('iPhone') > 0 || userAgent.indexOf('iPod') > 0 || userAgent.indexOf('iPad') > 0 || userAgent.indexOf('Android') > 0 ? true : false;
isie          = !$('body').hasClass('lcm_ieversion_0') ? true : false;
isLtie9       = $('body').hasClass('lcm_ieversion_8') || $('body').hasClass('lcm_ieversion_7') || $('body').hasClass('lcm_ieversion_6') ? true : false;

/*=== フォーカス制御の是否 ===*/
tabindexCtrl  = true;
query = window.location.search.substring(1);
if(query!=''){
	var params = query.split('&');
	for(var len = params.length, n = len-1  ; n > 0; n--){
		var param = params[n];
		if( param.indexOf('limit') == 0 ){
			param_val = param.split('=')[1]
			if(param_val >= 50) tabindexCtrl = false;
		}
	}
}
tabindexCtrl  = isNetReader || isLtie9 || isTouchDevice || $('body').hasClass('nofocusctrl') ? false : tabindexCtrl;


//スクロールバーのサイズ取得
//table_scrollableのために用意したけど止めているので今のところ不使用
/*var scrollbar_s = (function(){
	var testdiv, rs;
	testdiv = document.createElement('div');
	testdiv.style.width = '100px';
	testdiv.style.height = '100px';
	testdiv.style.overflow = 'scroll';
	document.body.appendChild(testdiv);
	rs = testdiv.offsetWidth - testdiv.clientWidth;
	$(testdiv).remove();
	return rs;
})();
*/
//フォーカスするついでに場合によってはセレクトもする
function set_focus(t){
	$(t).focus();
	if($(t).is(':input') && !isNetReader){
		$(t).select();
	}
}

//ページ読み込み直後の動作
(function(){
//フォーカス初期位置
	if($('.flash_alert')[0]){
		var firstFocus = $('.flash_alert a.skip').first();
	}else if($('body').hasClass('lcm_action_login')){
		var firstFocus = $('input:visible').first();
	}
	if(firstFocus){
		set_focus(firstFocus);
	}else{
		var container = $('#main_content');
		container.focus();
		document.body.scrollTop = 0; //描画が遅れるとカクカクしちゃうので、containerの描画位置自体を考えられたらよいのかも？
	}
	
	//見出しのh1に最初のフォーカスを与える
	$('h1').first().not(':has(>a)').attr('tabindex', '0');
})();


//管理バーの高さ+αのヘッダーの高さを確保
function add_body_padding(headerheight){
	$('body').css('padding-top', headerheight+'px' );
}
var adminbar = $('#adminbar');
headerheight = 0;
if(adminbar[0]){
	headerheight = adminbar.outerHeight();
	add_body_padding(headerheight);

	adminbar.exResize(function(){
		headerheight = $(this).outerHeight();
		add_body_padding(headerheight);
	});
}

//非表示の要素の設定
$('.hidden_item').each(function(){
	var query, params, v, trigger ; 
	//hidden_itemでも検索条件のある場合、中に値がある場合は展開しておく
	if($(this).find('form.search')[0]){
	//検索フォームの場合、get値を見る。
		query = window.location.search.substring(1);
		if(query!=''){
			params = query.split('&');
			for(var i=0, len = params.length ; i < len; i++){
				if( params[i].indexOf('orders') !== 0 ){
					v = true;
					break;
				}
			}
		}
	}else if($(this).find(':input')[0]){
		$(this).find(':input').each(function(){
			v = $(this).val()!='' ? true : v;
		});
	}
	if(!v) return;
	trigger = $('.toggle_item').eq($('.hidden_item').index(this));
	$(this).addClass('on').show();
	trigger.addClass('on');
});

//テーブルにあわせたcontent幅 //ウィンドウ幅で表示サイズが左右される端末のことも考える
/*
var container = $('.container');
var container_w = container.width();
var overflow = 0;
$(document).find('table').each(function(){
console.log(container_w);
console.log($(this).outerWidth());
	var o = container_w - $(this).outerWidth();
	if(o < overflow ) overflow = o;
	return overflow;
});
var c_w = container.outerWidth() - overflow;
console.log(c_w);
$('.container').css({'cssText':'width: '+c_w+'px ; max-width : auto;'});
*/



//ページ内リンク ヘッダー分位置調整とスムーズスクロール
//html要素がスクロール対象であるか判定。
//http://www.webdesignleaves.com/wp/jquery/573/
var is_html_scrollable = (function(){
	var html, el, rs, top;
	html = $('html');
	top = html.scrollTop();
	el = $('<div>').height(10000).prependTo('body');
	html.scrollTop(10000);
	rs = !!html.scrollTop();
	html.scrollTop(top);
	el.remove();
	return rs;
})();

//スクロール
$(document).on('click', 'a[href^=#]', function(e){
	e = e ? e : event;
	var href, t, position;
	$(window).off('beforeunload');//ページ内リンクでは画面遷移の警告をキャンセル

	href= $(this).attr("href");
	if(href!='#'){
		t = $(href == '' ? 'html' : href);
		position = t.offset().top - headerheight - 10;
		$(is_html_scrollable ? 'html' : 'body').animate({scrollTop:position}, 250, 'swing');
		set_focus(t);
		return false;
	}else if(e.isDefaultPrevented()){ //#でイベントを設定されている場合に抑止？ 
		e.preventDefault();
	}
});

//フォーカスしたものが画面外にある場合に位置を調節する。
//もう少し条件を整理したら、上のページ内リンクともまとめられる？ まとめたほうがいい？
$(document).on('keydown',function(e){
	e = e ? e : event;
	var t = $(e.target);
	var k = e.which;
	if(k==9){
		setTimeout(function(){
			var el = $(':focus');
			if(el[0]){
				var position = el.offset().top-$(window).scrollTop()-headerheight;
				if(el.closest($('#adminbar'))[0] || position > 10) return;
				$(is_html_scrollable ? 'html' : 'body').scrollTop(el.offset().top-headerheight-10);
			}
		}, 0);
	}

})

//全体に対するクリックイベント。
$(document).click(function(e){
	e = e ? e : event;
	var t = e.target;
//リストの開け閉め
	close_semimodal(t);
	replace_info();//開く・閉じる説明文切り替え
} );


/* ================================▼▼▼=============================== */

//tabindex制御
$.fn.set_tabindex = function(){
	//tabindexを一旦dataに格納し、現在の要素のみtabindex制御をリセットする。
	//毎回全体そうさすべきなのかなあ？？
	
	$(document).find(':focusable').each(function(){
			var tabindex;
	
		//dataにtabindexの値を格納
		if(!$(this).hasClass('tabindex_ctrl')){
			tabindex = $(this).attr('tabindex');
			if( tabindex ){
				$(this).data('tabindex',tabindex);
			}else{
				$(this).data('tabindex', 'none');
			}
		}
	
		//thisが表示されていれば、tab移動制御する
		if($(this).is(':visible')){
			$(this).attr('tabindex','-1').addClass('tabindex_ctrl');
		}
	});
//	thisのなかはtabindexを有効に
	$(this).reset_tabindex();
	return this;
}

$.fn.reset_tabindex = function(){
	//data-tabindexの値を見つつtabindexをリセットする
	$(this).find('.tabindex_ctrl').each(function(){

		var dataTabindex = $(this).data('tabindex');
		if(dataTabindex && dataTabindex !== 'none'){
			$(this).attr('tabindex', dataTabindex);
		}else{
			$(this).removeAttr('tabindex');
		}
	});
	return this;
}


//.lcm_focus フォーカス枠の設定 //フォーカス制御がむずかしい環境は除外
if(tabindexCtrl && $('.lcm_focus')[0]){
	/* 閲覧状態のフォーム内のlcm_focusを外す */
	if($('.lcm_form.view')[0]){
		$('.lcm_form.view .lcm_focus').removeClass('lcm_focus').removeAttr('tabindex');
		//ブロック説明用に、lcm_focusとは別にフォーカスを与えることを想定してlcm_form内のinput_groupはtabindexを持っている。そもそも妥当？
	}
	lcm_focus();
}

function lcm_focus(){
	var elm, esc;
	elm = $(document).find('.lcm_focus');

	/*=== set_focus ===*/
	//フォーカス対象を指定して実行されている場合はそれを、なければlcm_focusを相手にする。
	//?ない場合?：初回と、lcm_focus最上部で抜ける時。
	lcm_focus_set = function(target){
		var parent, t; 
		$('.currentfocus').removeClass('currentfocus');
		if(!esc){
			$(document).set_tabindex();
		}else if(target){
			parents = target.parents(elm).addClass('focusparent');
			target.addClass('currentfocus').css('position', 'relative').set_tabindex();
		}else{
			$(document).set_tabindex();//重いかなあ。
		}
		
		if(!esc){//抜けるリンクなどの準備
			esc = $('<div id="esc_focus_wrapper" class="skip show_if_focus" style="display: none;" tabindex="0"><a id="esc_focus"  class="boxshadow" href="javascript: void(0);" tabindex="-1">抜ける</a></div>').appendTo($('body'));
			var len = elm.length;
			for( var n = len ; n > 0 ; n-- ){
				el = elm.eq(n);
				var title_str = el.attr('title') ? el.attr('title') : '';
				el.attr('title', title_str+' エンターで入ります')
			}
		}

		//targetの中にlcm_focusがあれば中身のtabindexを-1にする
		t = target ? target.find('.lcm_focus') : elm;
		t.attr('tabindex', '0');
		t.find(':tabbable').attr('tabindex', '-1');

		//抜けるリンクの枠の表示領域をcurrentfocusを元に設定
		var current = $('.currentfocus');
		if(current[0]){
			esc.show().attr('tabindex','0');
			esc.css({
				'top'   : current.offset().top,
				'left'  : current.offset().left,
				'width' : current[0].scrollWidth,
				'height': current[0].scrollHeight - current.height() < 0 ? current[0].scrollHeight : current.height(),
			});
		}else{
			esc.hide();
		}
	}
	/*=== lcm_focus_esc ===*/
	//フォーカス有効時にESCや「抜けるリンク」でフォーカスを1階層抜ける。
	var lcm_focus_esc = function(e){
		e = e ? e : event;//抜けるリンクはeがclickイベントになり、tが#esc_focusになる
		e.preventDefault();
		e.stopPropagation();
		var t, current, parent;
		t = $(e.target);

		current = $('.currentfocus').eq(0).removeClass('currentfocus').set_tabindex().focus();
		esc.hide();
		parent = $('.focusparent')[0] ? $('.focusparent').last() : null;
		$(document).reset_tabindex();
		if(parent){
			parent.removeClass('focusparent').addClass('currentfocus');
		}
		lcm_focus_set(parent);
	}

	//ひとまず実行 //lcm_focusが入れ子になっていてもここで一旦-1
	setTimeout(lcm_focus_set, 0);

	//lcm_focus上でのキーボードイベント。
	elm.on('keydown', function(e){
		e = e ? e : event;
		var t, k, parent;
		t = $(e.target);
		k = e.which;
		
		if(k == 9 && e.shiftKey && t.hasClass('currentfocus')){ //現在のフォーカス枠上でshift+tabの場合、escに移動
			esc.focus();
			e.preventDefault();
		}
		
		if( k == 13 ){//Enter
			if(isie && !t.is('a') && !t.is(':input')){
				e.preventDefault();
			}
			//aやinputでない場合は、デフォルトのイベントをキャンセル
			//(IEはイベントの伝播の順番がほぼ逆のようなので。ので、もうすこし条件を絞り込んだほうが良さそう。lcm_focus内のアイテム上でのエンター(送信)の有効：無効？？)
			if($(this).hasClass('currentfocus')){//currentfocus上は除外
				e.stopPropagation();
				return;
			}
			lcm_focus_set($(this));
			e.stopPropagation();
		}
	});
	
	//スケジューラの期間の設定でうまくescにフォーカスが当たらない状況になっているので、一旦むりやりフォーカス（あとでみなおす）
	$('#form_end_time, #form_end_date').blur(function(){
		setTimeout(function(){
			var elm = $(':focus');
			if(!elm.is('input') && !elm.is('#esc_focus_wrapper')){
				esc.focus();
			}
		},0);
	});
	
	//フォーカスの取り直し。クリックのほか、チェックボックスをスペースキーでチェックした際などにも走るので除外
	$(document).on('click', lcm_focus_setparent);
	
	function lcm_focus_setparent(e){
		e = e ? e : event;
		var k, t, parent;
		k = e.witch;
		if(k == 32) return;
		t = $(e.target);
		parent = t.closest(elm)[0];
		if(!$(parent).hasClass('focusparent')){
			parent = parent ? $(parent) : $(document);
			lcm_focus_set(parent);
		}
	}
	
	//ページ内リンクの際のフォーカス //ページ内リンク以外の、外部からのidへのリンクに対応するなら、focusを取ったほうがよいのかも、でも重くなりそう。
	$(document).on('click', 'a[href^=#]', function(e){
		e = e ? e : event;
		var href, t;
		href= $(this).attr("href");
		if(href!='#'){
			t = $(href == '' ? 'html' : href);	
			lcm_focus_setparent({target:t});
		}
	});

	
	$(document).on('keydown', function(e){
	//他のセミモーダルなどの閉じるESCとのかねあい。モーダル系が出ている時はこちらのESCは動かさない、向こう側のreset_tabindexもcurrentfocusを除外する。keydownとkeyup:focusの違いを見てもよいのかなあ
		e = e ? e : event;
		var t, k;
		t = $(e.target);
		k = e.which;
		
//		console.log($('.modal.on, .semimodal.on'));
		if($('.currentfocus')[0]){
			if((t.is('#esc_focus_wrapper') && k == 13) || 
				(!t.is(':input') && !$('.modal.on, .semimodal.on')[0] && k == 27 )){
				lcm_focus_esc(e);
				e.stopPropagation();
			}
		}
	});
	
	$(document).on('focus', '#esc_focus', function(e){
		e = e ? e : event;
		lcm_focus_esc(e);
	});
	

/*
	//IEの6~9では、tabindex-1のinput要素(radioのみ？)にタブ移動できてしまう。ここでは逆順の移動で枠より先に中の要素にフォーカスする際の処理をする。移動してしまってからの処理でよい？？
	if(isLtie9){
		$(document).on('keydown',function(e){
			e = e ? e : event;
			var k, parent;
			k = e.which;
			if(k == 9 && e.shiftKey){
				setTimeout(function(){
					if($(':focus').attr('tabindex') == -1){
						$(':focus').closest('.lcm_focus').focus();
					}
				}, 0);
			}
		});
	}
*/
}
/* ================================▲▲▲=============================== */



//要素の中央配置
$.fn.set_center = function(){
	var left  = Math.floor(( $(window).width()-this.outerWidth() ) /2);
	var top   = Math.floor(( $(window).height()-this.outerHeight() ) /2);
	this.css({'left': left, 'top': top});
	return this;
}
$(window).resize(function(){
	var el = $('.set_center, .modal.on');
	if(el){
		el.set_center();
	}
});

//モーダルの外制御//キーボードのことを考えてdisabled制御をするならclick処理は重複？
$('#modal_wrapper').on('click', function(){
	return false;
});

//親を閉じる
$('.lcm_close_window').on('click', function(){
	var w = $(this).parent();
	$(this).lcm_close_window(w);
});

$.fn.lcm_close_window = function(w){
	w.hide();
	if($(w.find('.lcm_close_window')[0]).hasClass('lcm_reset_style')){
		w.removeAttr('style').hide();
	}
}


//Focusまわりのテスト（NetReaderでFocus移動を検知したい）
//setActiveとか、activeElementとか、なにかIE7で使えるものでないと行けない
//が、最新版のNetReaderはIEが7でなくなったので、古い環境の動作確認はできない(再インストール？)


//モーダル あとで。まだ使ってない
function close_modal(focus,t){
	//modalを閉じる機能、で、semimodalと併用できるように考える
	//現在のtabbableを取る？
	focus.focus();
	t.removeClass('on');
	$(document).reset_tabindex();
}

function close_semimodal(el){
	var t, trigger, focus;
	t = $(document).find('.semimodal.on');
	if(t[0]){
		trigger = $('.toggle_item').eq($(document).find('.hidden_item').index(t));
		focus = ($(el).is(':input')) ? el : trigger;
		trigger.removeClass('on');
		close_modal(focus,t);
	}
	return false;
}
$(document).on('click', '#close_modal' ,function(){
	close_modal($('.modal_parent'), $('.modal_on'));
});
$(document).on('click', '.semimodal.on, modal.on', function(e){
	e = e ? e : event;
	e.stopPropagation();
});

//表示・非表示切り替え
$('.toggle_item').on('click', function(e){
	e = e ? e : event;
	var t = $('.hidden_item').eq($('.toggle_item').index(this));//切り替えの相手

	if($(this).hasClass('disclosure')){//ディスクロージャならスライド
		t.slideToggle(125);
	}
	
	if($('.semimodal.on')[0] ){//モーダルが開いている場合は閉じる
		var itself = t.is('.semimodal.on');
		close_semimodal();
		replace_info();//開く・閉じる説明文切り替え
		if(itself) return;//モーダルが自分ならそこでおわり
	}
	t.toggleClass('on');
	$(this).toggleClass('on').focus();

	if(t.is('.semimodal.on')){//tabindex制御
		t.set_tabindex();
		//targetの中とtoggleの要素だけtabindexを元に。//data('tabindex')を見る？
		$(this).removeAttr('tabindex');
	}
	replace_info();//開く・閉じる説明文切り替え
	
	e.stopPropagation();
	return false;
});
function replace_info(){
	var els, len, el, title, skip;
	els = $(document).find('.toggle_item');
	len  = els.length;
	for(var n = len; n > 0; n--){
		el = els.eq(n);
		title = el.attr('title');
		skip  = el.find('.skip').text();
		if(el.hasClass('on')){
			title = title ? title.replace('開く', '閉じる') : null;
			skip  = skip  ? skip.replace('開きます', '閉じます') : null;
		}else{
			title = title ? title.replace('閉じる', '開く') : null;
			skip  = skip  ? skip.replace('閉じます', '開きます') : null;
		}
		if(title) el.attr('title', title);
		if(skip)  el.find('.skip').text(skip);
	}
}

//キーボード操作の制御

//NetReaderでうまく取得できないので、なにか考える

$(document).on('keydown',function(e){
	e = e ? e : event;
	var t, k, index, modal, tabbable, first, last;
	t = e.target;
	k = e.which;
	// k = 9:tab, 13:enter,16:shift 27:esc, 37:←, 38:↑, 40:↓, 39:→
	index = null;
	
	modal = $(document).find('.modal.on, .semimodal.on, .currentfocus')[0];//これらが混在することがある？
	if(modal){
		tabbable = $(document).find(':tabbable');
		first    = tabbable.first()[0];
		last     = tabbable.last()[0];
		switch(k){
			case 9://tab
				if( t === last && ! e.shiftKey){
					index = 0;
				}else if( t === first && e.shiftKey){
					index = -1;
				}
			break;
			case 27://esc
				close_semimodal();
			break;
		}
		if($(modal).hasClass('menulist')){//.menulistでのカーソルのふるまい
			switch(k){
				case 37 || 39://左右 //スクリーンリーダのことを考えるとShiftを除く他キーとの組み合わせは排除できたほうがいい？
					return false;
				break;
				case 40://下
					index = tabbable.index($(':focus'))+1;
					if( t === last){
						var index = 0;
					}
				break;
				case 38://上
					index = tabbable.index($(':focus'))-1;
				break;
			}
		}
		if(index !== null){
			tabbable.eq(index).focus();
			return false;
		}
	}
});


//確認メッセージ
$('.confirm').click(function(){
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


//=== form ===

//ページ遷移時の警告
//エラー時には必ず。//フォームと無関係のエラーは？
//login画面とsubmitがない場合(編集履歴など)では出さない。編集履歴はむしろdisableにするほうがよい？
function check_formchange(){
	var input_time, len, el;
	input_time = $('.datetime','.time');//datetimeの枠にフォーカスした際のchange周りのなにか。あとでもういちど確認
	len = input_time.length;
	for( var n = len; n > 0; n--){
		el = input_time.eq(0);
		el.data('val',el.val());
	}

	function confirm_beforeunload(){
		$(window).on('beforeunload', function(){
			return '変更内容は保存されていません。';
		});
	}

	$('form').change( function(e){
		e = e ? e : event;
		var t = $(e.target);
		if(!( t.closest('.search, .index_toolbar')[0] || t.hasClass('checkbox_binded') || t.hasClass('datetime') && t.val() == t.data('val') )){
		//変更のあった要素のうち、.search form内や、一括処理用のチェックボックス、datetimepickerは除外
			confirm_beforeunload();
		}
	});
	if($('#alert_error').children('ul.list')[0] || $('.lcm_module_reserve #alert_error')[0] || $('.lcm_ctrl_-controller_scdl #alert_error')[0]){
		confirm_beforeunload();
	}
}

if($('a:submit, input:submit')[0] && !$('body').hasClass('lcm_action_login')){
	check_formchange();
}

//ページ遷移警告抑止
$('a:submit, input:submit, .confirm').click(function(){//該当する場合遷移警告しない
		$(window).off('beforeunload');
});

//エラー時の入力エリアから一覧へのナビゲーション //複数の入力欄がある場合(開始・終了時刻等)はどうする？idを配列にしてしまって配列なら後者を参照できるようにして、その後ろに戻るリンクを作る、とか？
//とりあえずスケジューラについては一旦個別対応
//.validation_error が適切につくようならそれを見るとよいのかも
$('#alert_error .link').find('a').each(function(){
	var link = $('<a href="#anchor_alert_error" class="skip show_if_focus link_alert_error">エラー一覧にもどる</a>');
	var t = $($(this).attr('href'));
	if(t.closest('.lcm_multiple_select')[0]){
		t.closest('.lcm_multiple_select').eq(0).append(link);
		return;
	}else if(t.is('#form_start_date')){
		t = $('#form_end_date');
	}else if(t.is('#form_start_time')){
		t = $('#form_end_time');
	}
	t.after(link);
});


/*=== lcm_multiple_select ===*/

$('.lcm_multiple_select').each(function(){
	var select, selected, selects, to, from;
	select = $($(this).find('.select_from'));
	selected = $($(this).find('.selected'));
	selects = select.add(selected);
	
	var hidden_items_id = $(this).data('hiddenItemId');
	if(hidden_items_id){
		make_hidden_form_items(hidden_items_id, selected);
	}
	
	$(this).find(':button').click(function(e){
		e = e ? e : event;
		from = $(this).hasClass('add_item') ? select : selected;
		to = selects.not(from);
		lcm_multiple_select(from, to, hidden_items_id, selected);
	});
	selects.dblclick(function(){
		from = $(this);
		to = selects.not(from);
		lcm_multiple_select(from, to, hidden_items_id, selected);
	});
});

function lcm_multiple_select(from, to, hidden_items_id, selected){
	//引数selectedはhidden_itemがなくなれば不要
	var from, to, val, item, hidden_items_id;
	val = from.val();
	if ( val == "" || !val) return;
	for(var i=0, len = val.length; i < len; i++){
		item = from.find('option[value='+val[i]+']');
		item.appendTo(to).attr('selected',false);
	}

	if(hidden_items_id){
		make_hidden_form_items(hidden_items_id, selected)
	};
}

//スケジューラ用hidden
function make_hidden_form_items(hidden_items_id, selected){
	var hidden_item = $('#'+hidden_items_id);
	if (!hidden_item[0]) {
		hidden_item = $('<input>').attr({
		    type : 'hidden',
		    id   : hidden_items_id,
		    name : hidden_items_id,
		    value: '',
		}).appendTo('form');
	}
	var hidden_str = "";
	var els = $(selected).find('option');
	// 配列に入れる
	for( var len = els.length, n = 0; n < len ; n++){
		hidden_str += "/" + els.eq(n).val();
	}
	hidden_item.val(hidden_str);
}



/* Tiny MCE  */
tinymce.init({
	mode : "none",
//	selector: "textarea.tinymce",
	theme : "modern",
	theme_advanced_buttons3_add : "tablecontrols",
	plugins:"table code"	
});
$(':input.tinymce').each(function(){
	var id = this.id;
	$(this).before('<p class="cf" style="font-size:.8em;"><a id="switch_'+id+'" class="switch_mce is_text" href="javascript: void(0);">')
	$(document).find('.switch_mce').text('ビジュアルエディタを使用');
});
$(document).on('click', '.switch_mce', function(){
	var id = this.id.replace('switch_','');
	if( $(this).hasClass('is_visual') ){
		$(this).removeClass('is_visual').addClass('is_text').text('ビジュアルエディタを使用');
		tinymce.EditorManager.execCommand('mceRemoveEditor', false, id);
	} else {
		$(this).removeClass('is_text').addClass('is_visual').text('テキストエディタを使用');
		tinymce.EditorManager.execCommand('mceAddEditor', true, id);
	}
});


/* jQuery UI */

//calendar
//通常の年月の選択
$('input.month').datepicker({
	firstDay       : 1,
	dateFormat     : 'yy-mm',
	yearRange      : 'c-20:c+20',
	changeMonth    : true,
	changeYear     : true,
	showButtonPanel: true,
	currentText    : '今月',
	closeText      : '決定',
	beforeShow: function(input, inst) {
		$(inst.dpDiv).addClass('monthpicker');
		var currentDate = $(this).val();
		if(!currentDate){
			return;
		} else {
			currentDate = currentDate.replace('-', '/')+'/01';
			$(this).datepicker('option', 'defaultDate', new Date(currentDate));
			$(this).datepicker('setDate', new Date(currentDate));
		}
	},
	onChangeMonthYear: function(year, month){
		month = ("0"+month).slice(-2); 
		$(this).val(year+'-'+month);
	},
	onClose: function(dateText, inst) { 
		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
		var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
		$(this).datepicker('setDate', new Date(year, month, 1));
		setTimeout(function(){$(inst.dpDiv).removeClass('monthpicker')}, 150);//消える前に表示されてしまうので遅らせる
	}
});
//開始月と終了月 //ひとまずつくらない


//開始日と終了日
var jslcm_dates = $( '#form_start_date, #form_end_date' ).datepicker( {
	firstDay       : 1,
	dateFormat: 'yy-mm-dd',
	changeMonth: true,
	changeYear: true,
	showButtonPanel: true,
	onSelect: function( selectedDate ) {
		var option = this.id == 'form_start_date' ? 'minDate' : 'maxDate',
		inst = $(this).data('datepicker'),
		date = $.datepicker.parseDate(inst.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate,inst.settings );
		jslcm_dates.not(this).datepicker('option', option, date);
		//繰り返しなしの場合は開始日を入力したら自動的に終了日に同じ値が入るようにする。終了日は任意変更可。
		//むしろ、終了日入力なしで行けるようにできるとなにか解決する？
		if($("#form_repeat_kb")[0] && $("#form_repeat_kb").val() == 0){
			set_startdate_to_enddate(this);
		}
//		val_compare($('#form_start_date'), $('#form_end_date'));

	}
});

if($('#form_start_date, #form_end_date')[0]){
//	val_compare($('#form_start_date'), $('#form_end_date'));
	$('#form_start_date, #form_end_date').change(function(){
		set_startdate_to_enddate(this);
//		val_compare($('#form_start_date'), $('#form_end_date'));
	});
}
function set_startdate_to_enddate(el){
	if(el.id == 'form_start_date'){
		$('#form_end_date').val($(el).val());
	}
}

//通常の日付選択
$('input.date , input[type=date]').datepicker({
	firstDay       : 1,
	dateFormat     : 'yy-mm-dd',
	yearRange     : 'c-20:c+20',
	changeMonth    : true,
	changeYear     : true,
	showButtonPanel: true,
});

//通常の日付選択
$('input.birth_at').datepicker({
	firstDay       : 1,
	dateFormat     : 'yy-mm-dd',
	yearRange     : 'c-50:c+0',
	changeMonth    : true,
	changeYear     : true,
	showButtonPanel: true,
});

//日付＋時間
//15分区切り
$('input.datetime.min15, input[type=datetime].min15').datetimepicker({
	timeFormat: 'HH:mm',
	stepMinute: 15
});
//30分区切り
$('input.datetime.min30, input[type=datetime].min30').datetimepicker({
	timeFormat: 'HH:mm',
	stepMinute: 30
});
//通常の日付＋時間選択
$('input.datetime,  input[type=datetime]').datetimepicker({
		firstDay : 1,
		yearRange: 'c-20:c+20',
});

//時間選択
//開始時間と終了時間
/*
var jslcm_times = $( '#form_start_time, #form_end_time' ).timepicker( {
	timeFormat: 'HH:mm',
	stepMinute: 15,
*/
/*	beforeShow: function(){
		var option = null;
		option = $(this).hasClass('min15') ? 15 : option;
		option = $(this).hasClass('min30') ? 30 : option;
		if(option){
			$(this).timepicker({stepMinute: option});
			console.log(option);
		};
	},
*/
/*	onSelect: function( selectedtime ) {
		console.log(this);
		console.log(selectedtime);
		
		var option = this.id == 'form_start_time' ? 'minTime' : 'maxTime',
		time = selectedtime;
*/
/*		date = $.datepicker.parseDate(inst.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate,inst.settings );
*/
/*
		jslcm_times.not(this).datepicker('option', option, time);
	}
});
*/

//15分区切り
$('input.time.min15').timepicker({
	timeFormat: 'HH:mm',
	stepMinute: 15,
	beforeShow: function(input){
		if( $(input).attr('readonly') ) return false;
	}
});
//30分区切り
$('input.time.min30').timepicker({
	timeFormat: 'HH:mm',
	stepMinute: 30,
	beforeShow: function(){
		if( $(this).attr('readonly') ) return false;
	}
});
//通常の時間選択
$('input.time').timepicker({
	timeFormat: 'HH:mm',
	beforeShow: function(input){
		if( $(input).attr('readonly') ) return false;
	}
});

//tooltip //overflowしている対象にページ内リンクでスクロールして表示する場合、出る位置が狂う。
//title属性はブラウザの対応がまちまちなので、data-を対象にする
//エラー
$('.validation_error :input').tooltip({
	tooltipClass : 'lcm_tooltip',
	show         : 200,
	hide         : 'fade',
	position     : {
		             my : 'left bottom-8',
		             at : 'left top'
		            },
	items        : '[data-jslcm-tooltip]',
/*	content      : function(){
	                 return $(this).data('jslcmTooltip')
		           }*/
});

//通常のツールチップ
$('.lcm_tooltip_parent').tooltip({

	items: '[data-jslcm-tooltip-id]',
	content: function() {
		var el = document.getElementById($(this).data('jslcmTooltipId'));
		el = $(el).html();
		return el
	},
	open: function (event, ui) {
		ui.tooltip.css({'width': 'auto','max-width': '100%'});
	},
	tooltipClass : 'lcm_tooltip',
	show         : 200,
	delay        : 1500,
	hide         : 'fade',
	relative     : true,
	track        : false,
	position     : {
		             my : 'left bottom-8',
		             at : 'left top'
		            },
});

//スケジュールの1日詳細のグラフのcolspanひとまず。不要になれば削除
/*if($('body').hasClass('lcm_ctrl_-controller_scdl')){
	$('.schedule_day.graph').find('tr').each(function(){
		var bar = $(this).find('.active');
		if(bar[1]){
			bar.eq(0).attr('colspan',bar.length);
			bar.not(bar[0]).remove();
		}
	});
	$('.schedule_day.graph .lcm_tooltip_parent').tooltip({
		track: true,
	});
}
*/
//resizable, draggable //画面の上下はみ出してドラッグしたときのふるまい
$('.lcm_floatwindow').resizable({
	'handles' : 'all',
	'containment' : 'document',
	start:function(e, ui) {
		e = e ? e : event;
		var el = $(e.target);
		el.css( 'position','fixed');
	},
	stop:function(e, ui) {
		e = e ? e : event;
		var el = $(e.target);
	}
}).draggable({
	'handle'      : '.lcm_floatwindow_title',
	'containment' : 'document',
	'scroll' : true,
	stop:function(e, ui) {
		e = e ? e : event;
		var el = $(e.target);
	}
});

//テキストエリアのリサイズ非対応ブラウザへの処置。とりあえずIEのみ
var ta_unresizable = isie ;
//もともとの最大幅・最大高とのかねあいを解消できるようにしたい
//現在幅・高さをいったん明示的に与えて、max-widthとmax-heightをauto?にすればよい？？？
if(ta_unresizable){
	$('textarea').resizable({
		'maxWidth' : 800,
		'minWidth' : 60,
		'minHeight': 30,
		'contain'   : '#main_container'
}).parent().addClass('resizable_textarea');
}


//=== rollover ===
$('.bt a:has(img)').hover(function(){
	var imgsrc = $(this).find('img').attr('src');
	if(! $(this).hasClass("on") && imgsrc.indexOf('_ro.') == -1){
		var imgsrc = imgsrc.replace(/\.(gif|png|jpg|jpeg)$/i,'_ro\.$1');
		$(this).find('img').attr('src',imgsrc);
	}
},function(){
	if(! $(this).hasClass("on")){
		var imgsrc = $(this).find('img').attr('src').replace(/_ro\.(gif|png|jpg|jpeg)$/i,'\.$1');
		$(this).find('img').attr('src',imgsrc);
	}
});

//input.bt
$('input.bt').hover(function(){
	var imgsrc = $(this).attr('src');
	if(imgsrc.indexOf('_ro.') == -1){
		var imgsrc = imgsrc.replace(/\.(gif|png|jpg|jpeg)$/i,'_ro\.$1');
		$(this).attr('src',imgsrc);
	}
},function(){
	var imgsrc = $(this).attr('src').replace(/_ro\.(gif|png|jpg|jpeg)$/i,'\.$1');
	$(this).attr('src',imgsrc);
});

/*
////lowvision menuimg 
function replacemenuimg(a){
	imgalt = a.find('img').attr('alt');
	a.html(imgalt);
}

$('.lv_black #topmenus a').each(function(){
replacemenuimg($(this));
});
$('.lv_black #footmenu a').each(function(){
replacemenuimg($(this));
});

$('.lv_white #topmenus a').each(function(){
replacemenuimg($(this));
});
$('.lv_white #footmenu a').each(function(){
replacemenuimg($(this));
});


$('.bt a.on:has(img)').each(function(){
if($('img',this).attr('src').indexOf('_ro')==-1){
		var imgsrc = $(this).find('img').attr('src').replace(/\.(gif|png|jpg|jpeg)$/i,'_ro\.$1');
		$(this).find('img').attr('src',imgsrc);
}
});



if ((navigator.userAgent.indexOf('iPhone') > 0 && navigator.userAgent.indexOf('iPad') == -1) || navigator.userAgent.indexOf('iPod') > 0 || navigator.userAgent.indexOf('Android') > 0) {
}else{
//=== rollover ===
$('.bt a:has(img)').hover(function(){
	if(! $(this).hasClass("on")){
		var imgsrc = $(this).find('img').attr('src').replace(/\.(gif|png|jpg|jpeg)$/i,'_ro\.$1');
		$(this).find('img').attr('src',imgsrc);
	}
},function(){
	if(! $(this).hasClass("on")){
		var imgsrc = $(this).find('img').attr('src').replace(/_ro\.(gif|png|jpg|jpeg)$/i,'\.$1');
		$(this).find('img').attr('src',imgsrc);
	}
});


//input.bt
$('input.bt').hover(function(){
	var imgsrc = $(this).attr('src').replace(/\.(gif|png|jpg|jpeg)$/i,'_ro\.$1');
	$(this).attr('src',imgsrc);
	},function(){
	var imgsrc = $(this).attr('src').replace(/_ro\.(gif|png|jpg|jpeg)$/i,'\.$1');
	$(this).attr('src',imgsrc);
});


 }

//=== button ===
$("a.button, a.btn").each(function(){
	$(this).wrap('<span class="buttonwrapper">');
	if($(this).hasClass('main')){
		$(this).parent().addClass('main');
	}
});

//==== targetblank class ====
$('a.bl').each(function(){
		$(this).attr('target','_blank');
	if($(this).find('img').attr('src')){
		altdata = $(this).find('img').attr('alt');
		$(this).find('img').attr('alt',altdata+'（別ウィンドウを開きます）')
	}else{
		$(this).append('<span style="font-size:85%;">（別ウィンドウを開きます）</span>');
	}
});

//=== add tell tag ===
$(".is_mobile .phone span").each(function(){
//スマートフォンが自動的にtellスキームにしなかったら、変更する。ただ、現状では、自動的にtelスキームになる様子。
//var phonenum = $(this).html() ;
//alert(phonenum);
});

//=== Accessibility ===

//util_fontsize_large
$('#util_fontsize_large a').click(function(){
	if($.cookie('util_fontsize')=='normal' || ! $.cookie('util_fontsize')){
		if($('.lv_white').html()||$('.lv_black').html()){
			fontsize = 160;
		}else{
			fontsize = 115;
		}
	}else{
		var fontsize = parseInt($.cookie('util_fontsize'))+15;
	}
	$('body').css('font-size',fontsize+'%');
	$.cookie('util_fontsize',fontsize,{ path: '/', expires:30});
});

//util_fontsize_default
$('#util_fontsize_default a').click(function(){
	$('body').css('font-size','');
	$.cookie('util_fontsize','normal',{ path: '/', expires:30});
});

//util_fontsize_small
$('#util_fontsize_small a').click(function(){
	if($.cookie('util_fontsize')=='normal' || ! $.cookie('util_fontsize')){
		if($('.lv_white').html()||$('.lv_black').html()){
			fontsize = 115;
		}else{
			fontsize = 85;
		}
	}else{
		var fontsize = parseInt($.cookie('util_fontsize'));
		if( fontsize > 10 ){
			fontsize = parseInt($.cookie('util_fontsize'))-15;
		}
	}
	$('body').css('font-size',fontsize+'%');
	$.cookie('util_fontsize',fontsize,{ path: '/', expires:30});
});

//font size from cookie
if($.cookie('util_fontsize')=='normal' || ! $.cookie('util_fontsize')){
	$('body').css('font-size','');
}else if($.cookie('util_fontsize')!=''){
	$('body').css('font-size',parseInt($.cookie('util_fontsize'))+'%');
}

//printout
$('body.print #main_column a').each(function(){
	if(! $(this).parent().parent('#printmode').html()){
		var alink = $(this).attr('href');
		$(this).after('<span class="abody">('+decodeURI( alink )+'）</span>');
		$(this).contents().unwrap();
//		$(this).after('<span class="abody">'+abody+'（'+decodeURI( alink )+'）</span>');
//		$(this).remove();
	}
});

//alert to transit
$('.lv_mode_chg').click(function(){
	if( ! window.confirm('弱視モードでは、文字が大きめに表示され、シンプルな画面表示になります。「弱視モードをやめる」で、弱視モードを抜けることができます。')){
		return false;
	}
});

//=== captionblock ===
//DO NOT forget width of image
//caption str. (DO NOT use `"` for URL strings)
$('img.caption').each(function(){
	var captxt = $(this).attr('alt').replace(/\[\/url\]/g,'</a>').replace(/\]/g,'">').replace(/\[url\=/g,'<a class="noicon" href="');
	var capwidth = $(this).width()*1+10;
	$(this).wrap('<div class="caption" style="width:'+capwidth+'px;">');
	$(this).attr('alt','').after('<br />'+captxt);
	if($(this).hasClass("fr")){
		$(this).parent("div.caption").addClass('fr');
	}else if($(this).hasClass("fl")){
		$(this).parent("div.caption").addClass('fl');
	}else{
		$(this).parent("div.caption").css({'width':'100%'});
	}
	//lightbox
	if($(this).hasClass("lb")){
		var srcdata = $(this).attr('src');
		$(this).wrap('<a href="'+srcdata+'" class="lb">');
	}
	$(this).removeClass();
});

//=== Google Map ===
//<div id="ID STR(required)" class="gmapblock">lat,lng,title(required),text,width,height,zoom-level</div>
$('.gmapblock').each(function(){
	var gmid = $(this).attr('id');
	var gmdata = $(this).html().split(',');
	$(this).css({'width':'350px','height':'340px','margin':'10px 0'}).html('');
	
	if(gmdata[4]){$(this).css({'width':gmdata[4]});}
	if(gmdata[5]){$(this).css({'height':gmdata[5]});}
	if(gmdata[2]){gmdata[2] = gmdata[2];clickable=true;}else{gmdata[2]='';clickable=false;}
	if(gmdata[3]){gmdata[3] = gmdata[3];}else{gmdata[3]='';}
	if(gmdata[6]){gmdata[6] = parseInt(gmdata[6]);}else{gmdata[6]=16;}

	var marker = '';
	var latLng = new google.maps.LatLng(gmdata[0],gmdata[1]);
	var czoom = gmdata[6];
	var myOptions = {
		zoom: gmdata[6],
		center: latLng,
		scrollwheel: false,
//		disableDoubleClickZoom: true,
//		disableDefaultUI: true,
//		mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
		mapTypeControl: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var geocoder = new google.maps.Geocoder();
	var map = new google.maps.Map($('#'+gmid+'').get(0), myOptions);
	var marker1 = new google.maps.Marker( {
		position: new google.maps.LatLng(gmdata[0],gmdata[1]),
		map: map,
		title: gmdata[0],
		clickable: clickable,
		visible: true
	} ) ;
	google.maps.event.addListener( marker1 , 'click' , function() {
		var infoWindow = new google.maps.InfoWindow( {
			content: '<p style="margin:0;width:220px;font-size:90%;"><strong>'+gmdata[2]+'</strong><br />'+gmdata[3]+'</p>' ,
			disableAutoPan: false
		} ) ;
		infoWindow.open( map , marker1 ) ;
	} ) ;
});

//=== media query ===

//initial check
mediaQueryClass($(window).width());

//resize
$(window).resize(function(){
	mediaQueryClass($(window).width());
});

//mediaQueryClass
function mediaQueryClass(width) {
	if( width < 651) {
		$("body").addClass('smartphone');
	}else{
		$("body").removeClass('smartphone');
	}
}

//=== smartphone ===

//sm_menu

$('a#sm_mainmenu_btn').click(function(){
	$('#sm_mainmenu').hide().css({'top':'10px'}).fadeIn() ;
	$('#close_menu').fadeIn() ;
	$('.close_menu_button').show() ;
});

$('.close_menu_button a').click(function(){
	$('#sm_options').css({'top':'-1000px'}) ;
	$('#sm_mainmenu').css({'top':'-1000px'}) ;
	$('#close_menu').fadeOut() ;
	$('.close_menu_button').hide() ;
});

$('#close_menu').click(function(){
	$('#sm_options').css({'top':'-1000px'}) ;
	$('#sm_mainmenu').css({'top':'-1000px'}) ;
	$('#close_menu').fadeOut() ;
	$('.close_menu_button').hide() ;
});

//sm_config

$('a#sm_options_btn').click(function(){
	$('#sm_options').hide().css({'top':'10px'}).fadeIn() ;
	$('#close_menu').fadeIn() ;
	$('.close_menu_button').show() ;
});


//lightbox
	$('a').filter(function(){
		return /\.(jpe?g|png|gif)$/i.test(this.href);
	}).colorbox({
		title:function () {
			var alt = $(this).children('img').attr('alt') ? $(this).children('img').attr('alt') : '';
			var str = (alt+'(クリックで元の画像を表示)').link(this.href);
			return str;
		}
	});


*/
});
