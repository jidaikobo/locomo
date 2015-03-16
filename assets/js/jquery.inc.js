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
		if(host == 'www.kyoto-lighthouse.org'){
			body.addClass('testserver');
			str = '--- テスト環境です　改造要望等はまずこちらで実験します　データは頻繁にリセットされます　動作テストなどご自由に操作いただけます ---';
		}else if(host!='kyoto-lighthouse.org'){
			str = '--- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ';
		}
		if(str){
			info = $('<p class="develop_info">').prepend(str);
			body.append(info);
			if($('body').hasClass('loggedin testserver')){
				topinfo = info.clone().addClass("top").css('top', $('#adminbar').outerHeight()+'px');
				$('#main_content').prepend(topinfo);
			}
		}
	})();
});


//チェックボックス全選択
$(function() {
	$('.check_all').on('click', function(e) {
		e.preventDefault();
		$('.checkbox_binded').prop('checked', true);
	});
	$('.uncheck_all').on('click', function(e) {
		e.preventDefault();
		$('.checkbox_binded').prop('checked', false);
	});

});
// ヘルプ呼び出し
function show_help(e){
	e = e ? e : event;
	if(e) e.preventDefault();//クリックイベント以外(アクセスキー等)の場合を除外
	var help_preparation = false;//重複読み込みの防止
	$(function(){
		if(!help_preparation){
			var uri = $('#lcm_help').data('uri');
			$.ajax({
				url: uri,
				dataType: 'html',
			})
			.success(function(data) {
				$('#help_txt').html(data);
//				$('#lcm_help').after($('#help_window'));
				help_preparation = true;
			})
		}
		/*閉じたり開いたり、フォーカス対象を変えたり、位置や大きさのリセットをしたり*/
		$('#help_window').show();
		$('#help_title_anchor').focus();
	});
}
$(function(){
	$('#lcm_help').click(show_help);
});

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

//UA //php側は？
var userAgent = window.navigator.userAgent;
isNetReader = userAgent.indexOf('NetReader') > 0 ? true : false;
tabindexCtrl = isNetReader ? false : true;//この条件は増えたり減ったりするのかも。
$('body').addClass(isNetReader ? 'netreader' : '');

//スクロールバーのサイズ取得//table_scrollableのために用意したけど止めているので今のところ不使用
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
(function (){
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


/* 不要なh2を削除するためのいったんのスタイル 
$('h2').first().each(function(){
	if(!$(this).prevAll('h1')[0])
		$(this).css('background-color', '#fcc');
});
*/

//管理バーの高さ+αのヘッダーの高さを確保
function add_body_padding(headerheight){
	$('body').css('padding-top', headerheight+'px' );
}
var adminbar = $('#adminbar');
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

/*================================▼▼▼===============================*/

//tabindex制御
$.fn.set_tabindex = function(){
	//tabindexを一旦dataに格納し、現在の要素のみtabindex制御をリセットする。
	$(document).find(':focusable').each(function(){
		var tabindex, dataTabindex;
		if($(this).is('#esc_focus_wrapper')) return; //tabindex制御しないものはここ。どうしよう……
		dataTabindex = $(this).data('tabindex');
	
		//dataにtabindexの値を格納
		if(!dataTabindex){
			tabindex = $(this).attr('tabindex');
			if( tabindex ){//tabindexがあるばあい
				$(this).data('tabindex',tabindex);
			}else{
				$(this).data('tabindex', 'none');
			}
		}

		//thisが表示されていれば、tab移動不可にする。
		if($(this).is(':visible')){
			$(this).attr('tabindex','-1');
		}
	});

	//thisのなかはtabindexを有効に
	$(this).find(':focusable').removeAttr('tabindex');

	return this;
}
$.fn.reset_tabindex = function(){
	//data-tabindexの値を見つつ、tabindexをリセットする
	$(document).find(':focusable').each(function(){
		var dataTabindex = $(this).data('tabindex');
		if(dataTabindex && dataTabindex !== 'none'){
			$(this).attr('tabindex', dataTabindex);
		}else{
			$(this).removeAttr('tabindex');
		}
	});
	
	return this;
}


//.lcm_focus フォーカス枠の設定 //フォーカス制御がむずかしいブラウザは対象外にする。たとえば、スマートフォンなども。
if(tabindexCtrl && $('.lcm_focus')[0]){
	set_lcm_focus();
}

function set_lcm_focus(){
	var lcm_focus, esc;
	lcm_focus    = $('.lcm_focus');

	if(!$('#esc_focus_wrapper')[0]){//初回だけにするもの。とりあえず抜けるリンクで判定(ので、removeはX)
		var esc = $('<div id="esc_focus_wrapper" class="skip show_if_focus" style="display: none;" tabindex="0"><a id="esc_focus" href="javascript: void(0);" tabindex="-1" >抜ける</a></div>').appendTo($('body'));
		lcm_focus.each(function(){
			var title_str = $(this).attr('title') ? $(this).attr('title')+' ' : '';
			$(this).attr('title', title_str+'エンターで入ります')
		});
	}

	/*=== set_focus ===*/
	//フォーカス対象を指定して実行されている場合はそれを、なければlcm_focusを相手にする。
	//?ない場合?：初回と、抜ける時親にlcm_focusがない場合。
	var set_focus = function(target){
		var target, parent, t; 

		$('.currentfocus').removeClass('.currentfocus');
		if(target){
			parents = target.parents('.lcm_focus').addClass('focusparent');
			target.addClass('currentfocus').set_tabindex();
		}

		//lcm_focusをもとにtabindexの設定を行う
		if(!$.isWindow(this)){
			$(this).addClass('currentfocus').css('position', 'relative').set_tabindex();
		}
		t = target ? target.find('.lcm_focus') : lcm_focus;
		
		set_focus_wrapper(t);
		if($('.currentfocus')[0]){
			esc.show().attr('tabindex','0');
			esc.css({
				'top'   : $('.currentfocus').offset().top,
				'left'  : $('.currentfocus').offset().left,
				'width' : $('.currentfocus').width(),
				'height': $('.currentfocus').height(),
				});
		}else{
			esc.hide();
		}
	
	}
	function set_focus_wrapper(t){//与えられなかった時のことは？
		t.attr('tabindex', '0');
		t.find(':tabbable').attr('tabindex', '-1');
	}
	

	/*=== esc_focus ===*/
	//フォーカス有効時にESCや「抜けるリンク」でフォーカスを抜ける。
	//多重のフォーカスは、親を見ながら戻していく。
	var esc_focus = function(e){
		e = e ? e : event;//この場合、抜けるリンクはeがclickイベントになり、tが#esc_focusになる
		e.preventDefault();
		var t, current, parent;
		t = $(e.target);
		
		current = $($('.currentfocus')[0]);

		$(current).removeClass('currentfocus').set_tabindex().focus();
		esc.hide();
		parent = current.closest('.focusparent')[0] ? current.closest('.focusparent'): null;
		$(document).reset_tabindex();
		set_focus(parent);
	}

	//ひとまず実行
	setTimeout(function(){
		set_focus();//lcm_focusが入れ子になっていてもここで一旦-1
	}, 100);

	//lcm_focus上でのキーボードイベント。
	$('.lcm_focus').on('keydown', function(e){
		e = e ? e : event;
		var t, k, parent;
		t = $(e.target);
		k = e.which;
		
		if(t.hasClass('currentfocus') && k == 9 && e.shiftKey){ //現在のフォーカス枠上でshift+tabの場合、escに移動
			esc.focus();
			e.preventDefault();
		}
		//IE6-9の場合、radioには-1を与えていても移動してしまうので、lcm_fous上では次のtabbableに飛ばす
		//逆タブでは、focus枠より先に中身にtabがあたるので、radio-1の場合の処理を書く
		var body = $('body');
		if(k == 9 && body.hasClass('lcm_ieversion_8') || body.hasClass('lcm_ieversion_7') || body.hasClass('lcm_ieversion_6')){
			var tabbable = $(':tabbable');
			var index = tabbable.index(t);
			if(!e.shiftKey){
				tabbable.eq(index+1).focus();
				e.preventDefault();
			}
		}

		if( k == 13 ){//enter
			e.preventDefault();
			//とりあえず、デフォルトのイベントをキャンセルしてしまう
			//(IEはイベントの伝播の順番がほぼ逆のようなので。ので、もうすこし条件を絞り込んだほうが良さそう。lcm_focus内のアイテム上でのエンター(送信)の有効：無効？？)
			if($(this).hasClass('currentfocus')){//currentfocus上は除外
				e.stopPropagation();
				return;
			}
			set_focus($(this));
			e.stopPropagation();
		}
	});
	
	//IEの6~9では、tabindex-1のinput要素(radioのみ？)にタブ移動できてしまう。ここでは逆順の移動で枠より先に中の要素にフォーカスする際の処理をする。移動してしまってからの処理でよい？？
	if($('body').hasClass('lcm_ieversion_8') || $('body').hasClass('lcm_ieversion_7') || $('body').hasClass('lcm_ieversion_6')){
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
	$('#esc_focus').on('focus', esc_focus);
	$(document).on('keydown', function(e){//他のセミモーダルなどの閉じるESCとのかねあい。モーダル系が出ている時はこちらのESCは動かさない、向こう側のreset_tabindexもcurrentfocusを除外する。keydownとkeyup:focusの違いを見てもよいのかなあ
		e = e ? e : event;
		var t, k;
		t = $(e.target);
		k = e.which;
		
//		console.log($('.modal.on, .semimodal.on'));

		if((t.is('#esc_focus_wrapper') && k == 13) ||(!t.is(':input') && !$('.modal.on, .semimodal.on')[0] && k == 27 )){
			esc_focus(e);
			e.stopPropagation();
		}
	});
	$(document).on('click',function(e){
	//フォーカスを解除して、targetの親のlcm_focusにフォーカス、自分自身がフォーカシブルだったらちゃんとフォーカス。
		e = e ? e : event;
		var t, parent;
		t = $(e.target);
		if(t.is(':focusable')){

		}
		//		
		parent = t.closest('.lcm_focus')[0];
		parent = parent ? $(parent) : null;//document相手にはできない。
		if(parent){
			set_focus(parent);
		}
		if(t.is(':focusable')){
			t.focus();
		}
	})

	$(document).on('click', ':input',function(e){
		e = e ? e : event;
		var t, parent;
		t = $(e.target);
		parent = t.closest('.lcm_focus');
		if(parent[0]){
			parent.set_tabindex();
		}
	});
}

/*================================▲▲▲===============================*/





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


/*
//リサイズの検知(フォント基準) //ひとまずadminbarを対象に行う。確実にサイト内に表示されている要素でサイズが変化するもの。
//
var fontsize_h, fontsize_ratio, window_resized;
fontsize_h =  adminbar.height();
var font_resize = setInterval(function(){
	if(!window_resized && fontsize_h != adminbar.height()){
		 fontsize_ratio = adminbar.height()/fontsize_h;
		 fontsize_h = adminbar.height();
//		 console.log(fontsize_ratio);
		if(fontsize_ratio != 1 && !window_resized){
//			console.log('フォントリサイズ')
		}
		window_resized = false;
	}else
	if(window_resized){
//		console.log('ウィンドウのリサイズ');
	}
}, 200);
//window.resizeもそのうちまとめたい。リサイズ終了待ちと、随時処理されたいものをわける。
//モーダルウィンドウも同じことになる？
var resize_timer = false;
$(window).resize(function(){
	if (resize_timer !== false) clearTimeout(resize_timer);
	resize_timer = setTimeout(function(){
	//リサイズ終了待ちの処理
	
	}, 200);
	$(document).find('#help_window:visible').each(function(e){
		var pw, w, l, r;
		w  = parseInt($(this).width());
		pw = $(document).width();
		l  = parseInt(this.offsetLeft);
		r  = pw-l-w;
		if(pw < w){
//			console.log('pw < w');
			$(this).css({'width': pw, 'left' : 0});
		}else if(r < 0){
			$(this).css({'left' : pw-w});
		}
	});
	window_resize = true;
});

*/


//モーダルの外制御//キーボードのことを考えてdisabled制御をするならclick処理は重複？
$('#modal_wrapper').on('click', function(){
	return false;
});

//親を閉じる
$('.lcm_close_parent').on('click', function(){
	var parent = $(this).parent();
	parent.hide();
	if($(this).hasClass('lcm_reset_style')){
		parent.removeAttr('style').removeClass('on');
	}
});



//Focusまわりのテスト（NetReaderでFocus移動を検知したい）
//setActiveとか、activeElementとか、なにかIE7で使えるものでないと行けない
//が、最新版のNetReaderはIEが7でなくなったので、古い環境の動作確認はできない(再インストール？)


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
	}else if(e.isDefaultPrevented()){ //#でイベントを設定されている場合に抑止？ return？？ 
		e.preventDefault();
	}
});


//全体に対するクリックイベント。
$(document).click(function(e){
	e = e ? e : event;
	var t = e.target;
//リストの開け閉め
	close_semimodal(t);
	replace_info();//開く・閉じる説明文切り替え
} );

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
		t.slideToggle(125);//ここでターゲットにフォーカスする？
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
	$(document).find('.toggle_item').each(function(){
		var title, skip;
		title = $(this).attr('title');
		skip = $(this).find('.skip').text();
		if($(this).hasClass('on')){
			title = title ? title.replace('開く', '閉じる') : null;
			skip  = skip  ? skip.replace('開きます', '閉じます') : null;
		}else{
			title = title ? title.replace('閉じる', '開く') : null;
			skip  = skip  ? skip.replace('閉じます', '開きます') : null;
		}
		if(title) $(this).attr('title', title);
		if(skip)  $(this).find('.skip').text(skip);
	});
}


//キーボード操作の制御

//NetReaderでうまく取得できないので、なにか考える
//.lcm_focusのようにまず枠にフォーカスを当てる場合のShift+Tabの動作のことも
//フォーカス枠のある時の表示位置の調整もかんがえる(ページ内リンクのスクロールと同じ)
/*
$(document).on('keyup',function(e){
	console.log(e.which);
	if(e.which == 27) $('<p>').text('up_esc').prependTo('.container');
});
$(document).on('keydown',function(e){
	console.log(e.which);
	if(e.which == 27) $('<p>').text('down_esc').prependTo('.container');
});
$(document).on('keypress',function(e){
	console.log(e.which);
	if(e.which == 27) $('<p>').text('press_esc').prependTo('.container');
});
*/


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




//表内スクロール - 各ブラウザでの挙動が怪しいのでもうちょっと
/*
if( !isNetReader && $('.tbl_scrollable')[0] ){ //複数ある時のことを考える。
//ここで、対象内のrowspan・colspanの有無を判定して、なければdisplay:block等を設定する方法に分岐してみる？ それ用のクラスを与える。
//ということは、そもそも簡単な表ではそのクラスを与えてもらうようにするとよい？
//……rosplan・colspanがあってscrollableでなければいけない表は少なそう。
//…………ということは、デフォルトのcssのはtbl_scrollableに対して設定してしまって、jslcmで分岐した際にclass名を変更するのがよいのかも。
//noscript環境のことを考えておく

//スクロールバーの幅ぶん調整したい
//現状だと右端は最終列にかぶり、下端はスクロールバー分はみ出る（margin-bottm: -{スクロールバー};）
//おなじく、ボーダーの幅
//wrapperに枠を表示できる？
	var tbl_scrollable = function(){
		var thead, tfoot, h, tbl_wrapper, thead_wrapper, tbody_wrapper, tfoot_wrapper, fixed_thead, fixed_tfoot;
		thead = $(this).find('thead').clone();
		tfoot = $(this).find('tfoot').clone();
		if(thead[0] || tfoot[0]){
			tbl_wrapper = $('<div>').addClass('jslcm_tbl_wrapper');
			tbody_wrapper = $('<div>').addClass('jslcm_tbody_wrapper');
			if(thead[0]){
				thead_wrapper = $('<div>').addClass('jslcm_thead_wrapper');
				fixed_thead = $('<table>').addClass($(this).attr('class')+' jslcm_fixed_thead').removeClass('tbl_scrollable').removeClass('lcm_focus').attr('aria-hidden','true').append(thead);
				$(fixed_thead).find('a, button').attr('tabindex', '-1');
			}
			if(tfoot[0]){
				tfoot_wrapper = $('<div>').addClass('jslcm_tfoot_wrapper');
				fixed_tfoot = $('<table>').addClass($(this).attr('class')+' jslcm_fixed_tfoot').removeClass('tbl_scrollable').removeClass('lcm_focus').attr('aria-hidden','true').append(tfoot);
				$(fixed_tfoot).find(':tabbable').attr('tabindex', '-1');
			}
			$(this).addClass('jslcm_tbl_scrollable')
				.wrap(tbl_wrapper)
				.after(fixed_tfoot)
				.after(fixed_thead)
				.wrap(tbody_wrapper);
			adjust_columns(this);
		}
	}
	
	var  adjust_columns = function(tbl, ws){
		//exresizeで変更を取得しているときには、そちらのサイズを使う……のでなければならなかったのかは、要確認。
		//フォントサイズの変更はどうにか取れなかったかなあ……も要確認
		//読み込み時に動いていないのも要確認
		var thead, tfoot, fixed_thead, fixed_tfoot, thead_cols, tfoot_cols, fixed_thead_cols, fixed_tfoot_cols, thead_len, tfoot_len, w;
		thead = $(tbl).find('thead');
		tfoot = $(tbl).find('tfoot');
		//重複を整理したい、というより一回でできる？
		if(thead[0]){
			thead_cols = $(tbl).children('thead').find('th, td');
			thead_len = thead_cols[0];
			fixed_thead = $(tbl).closest('.jslcm_tbl_wrapper').find('.jslcm_fixed_thead');
			fixed_thead_cols = $(fixed_thead).find('th, td');
			set_colswidth(thead_cols, thead_len, fixed_thead_cols, ws);
		}
		if(tfoot[0]){
			tfoot_cols = $(tbl).children('tfoot').find('th, td');
			tfoot_len = tfoot_cols[0];
			fixed_tfoot = $(tbl).closest('.jslcm_tbl_wrapper').find('.jslcm_fixed_tfoot');
			fixed_tfoot_cols = $(fixed_tfoot).find('th, td');
			set_colswidth(tfoot_cols, tfoot_len, fixed_tfoot_cols, ws);
		}
	}
	var  set_colswidth = function(cols, len, fixed_cols, ws){
		for(i=0; i<len-1; i++){
			if(ws){
				w = ws[i];
			}else{
				w = $(cols[i]).width();
			}
			$(fixed_cols[i]).width(w+1);//borderの太さを足す。とりあえず1pxで
		}
	}
	*/
/*
	if($('.tbl_scrollable').find('th[rowspan], th[colspan], td[rowspan], td[colspan]')){
		//colspan_rowspanの分岐。なんにしても中身の幅を指定する必要がありそうなので、元の値を見るadjust的な振る舞いは必要。その後addClassする。
		//要素が少なくなる分ちょっと軽かったり、フォーカス周りの処置が楽になる、といいなあ
		//でも、ヘッダ・フッタとボディのレイアウトが分かれてしまうので、セルの幅を取るのがむずかしくなる可能性あり。とくにリサイズ時注意
		adjust_columns($('.tbl_scrollable'));
		$('.tbl_scrollable').addClass('nocelspan');
	}else{
*/
//	$(document).find('.tbl_scrollable').each(tbl_scrollable);
/*	
	$.fn.el_overflow_y = function(){
		var parent, parent_h, parent_t, tbl, h, t, overflow, min_h;
		//ウィジェットや指定の枠がある場合は親にする。自分より小さな祖先ブロック要素を見つけてあわせる、ほうがいいのかなあ
		parent = $(this).closest('.widget, .parent_tbl_scrollable')[0] ? $(this).closest('.widget, .parent_tbl_scrollable')[0] : $('.container');
		tbl = $(this).find('.jslcm_tbl_scrollable');
		h = parseInt($(tbl)[0].scrollHeight, 10)+2;
		t = parseInt($(tbl).offset().top, 10);
		parent_h = parseInt($(parent).height(), 10);
		parent_t = parseInt($(parent).offset().top, 10);
		overflow = t - parent_t + h - parent_h;
		min_h = $(tbl).find('thead').height()+$(tbl).find('tfoot').height()+($(tbl).find('tbody tr').height()*2);
		if(overflow > 0){
			h = (h - overflow) > min_h ? h - overflow : min_h;
			$(tbl).height($(tbl).height()-scrollbar_s);//スクロールバー分引く
		}else{
		
		}
		$(this).height(h);
		return overflow ;
	}
	
	var resize_col = $('.jslcm_tbl_scrollable thead th, .jslcm_tbl_scrollable thead td, .jslcm_tbl_scrollable tfoot th .jslcm_tbl_scrollable tfoot td' ).exResize({
		api : true,
		callback :function(){
			var fixed_thead, tbl, ws, i;
			tbl = $(this).closest('table');
			var index = $(document).find('.jslcm_tbl_scrollable').index(tbl);
			fixed_thead = $(document).find('.jslcm_fixed_thead').eq(index);
			ws = new Array();
			i = 0;
			resize_col.each(function(){
				ws[i] = this.getSize().width;
				i++;
			});
			adjust_columns(tbl, ws);
		}
	});
	
	if($('.jslcm_tbody_wrapper')[0]){
		$('.jslcm_tbody_wrapper').el_overflow_y();	
		//ウィンドウリサイズ時やフォントサイズ変更時に追随したい（exResizeの挙動を再確認）
		//ブラウザによって、リサイズを捕捉できなかったりする？ ひとまず、Safariの拡大縮小要確認
		$(window).resize(function(){
			$('.jslcm_tbody_wrapper').el_overflow_y();
		});
		$('.jslcm_tbl_scrollable').exResize(function(){
			is_resize = true;
			$('.jslcm_tbody_wrapper').el_overflow_y(
				$(this).closest('.widget , .parent_tbl_scrollable')[0] ? $(this).closest('.widget , .parent_tbl_scrollable') : null
			);
		});
		$('.jslcm_tbl_wrapper').exResize(function(){
		//この辺もふだんの幅あわせでやることなのかも
			var tbl , tbody_wrapper, fixed_tbl, cols, fixed_cols, tbl_w, w;
			tbl = $(this).find('.jslcm_tbl_scrollable');
			tbody_wrapper = $(this).find('.jslcm_tbody_wrapper');
			fixed_tbl = $(this).find('.jslcm_fixed_thead, .jslcm_fixed_tfoot');
			if(tbl.width() - $('.jslcm_tbody_wrapper').width() > 0 ){
				cols = $(tbl).find('thead th, thead td, tfoot th, tfoot td');
				fixed_cols = $(fixed_tbl).find('th, td');
				tbl_w = $(tbl).width();
				$(fixed_tbl).css('min-width', tbl_w+scrollbar_s+'px');
				$(tbody_wrapper).css('min-width', tbl_w+scrollbar_s+'px');
				set_colswidth(cols, cols[0], fixed_cols);
			}
		});
	}
//	}//colspan, rowspan分岐終わり
}
*/

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
function confirm_beforeunload(){
	$(window).on('beforeunload', function(){
		return '変更内容は保存されていません。';
	});
}

var btn_submit = $('a:submit, input:submit');
if(btn_submit[0] && !$('body').hasClass('lcm_action_login')){
	var datetime = $('.datetime');
	datetime.each(function(){
		var val = $(this).val();
		$(this).data('val',val);
	});
	
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

//ページ遷移警告抑止
$('a:submit, input:submit, .confirm').click(function(){//該当する場合遷移警告しない
		$(window).off('beforeunload');
});

//エラー時の入力エリアから一覧へのナビゲーション
$('.validation_error :input').after('<a href="#anchor_alert_error" class="skip show_if_focus link_alert_error">エラー一覧にもどる</a>');

/*=== lcm_multiple_select ===*/

$('.lcm_multiple_select').each(function(){
	var select, selected, selects, to, from;
	select = $($(this).find('.select_from'));
	selected = $($(this).find('.selected'));
	selects = select.add(selected);
	
	//スケジューラ用hidden
	var hidden_items_id = $(this).data('hiddenItemId');
	if(hidden_items_id){
		make_hidden_form_items(hidden_items_id, selected);
	}
	
	
	$(this).find(':button').on('click', parent ,function(e){
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

	//スケジューラ用hidden
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
	// 配列に入れる
	$(selected).find('option').each(function() {
		hidden_str += "/" + $(this).val();
    });
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
/*function val_compare(el1, el2){
	if(el1.val()==el2.val()){
		el2.addClass('same_value');
	}else{
		el2.removeClass('same_value');
	}
}
*/

//通常の日付選択
$('input.date , input[type=date]').datepicker({
	firstDay       : 1,
	dateFormat: 'yy-mm-dd',
	changeMonth: true,
	changeYear: true,
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
		firstDay       : 1,
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
	beforeShow: function(){
		if( $(this).attr('readonly') ) return false;
	}
});
//30分区切り
$('input.time.min30').timepicker({
	timeFormat: 'HH:mm',
	stepMinute: 30,
	beforeShow: function(){
		if( $(this).attr('readonly') ) return;
	}
});
//通常の時間選択
$('input.time').timepicker({
	timeFormat: 'HH:mm',
	beforeShow: function(){
		if( $(this).attr('readonly') ) return;
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
	tooltipClass : 'lcm_tooltip',
	show         : 200,
	hide         : 'fade',
	relative     : true,
	position     : {
		             my : 'left bottom-8',
		             at : 'left top'
		            },
});



//resizable, draggable //画面の上下はみ出してドラッグしたときのふるまい
$('#help_window').resizable({
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
	'handle'      : '#help_title',
	'containment' : 'document',
	'scroll' : true,
	stop:function(e, ui) {
		e = e ? e : event;
		var el = $(e.target);
	}
});

//テキストエリアのリサイズ非対応ブラウザへの処置。とりあえずIEのみ
var ta_unresizable = $('.lcm_ieversion_0')[0] ? false : true ;
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
