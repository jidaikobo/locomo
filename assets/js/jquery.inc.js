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
// 環境についていろいろ格納するオブジェクトを持っておく
if(!lcm_env) var lcm_env = new Object();

jQuery(function ($){
	// ログインしているかどうか
	if(typeof(lcm_env.isloggedin) == 'undefined'){
		lcm_env.isloggedin = $('body').hasClass('loggedin') ? true : false;
	}
});


//テスト環境やローカル開発環境での表示
jQuery(function ($){
	(function(){
		var host, $body, str, $info, topinfo;
		host = location.host;
		$body = $('body');
		if(host == 'www.kyoto-lighthouse.org'){
			$body.addClass('testserver');
			str = '--- テスト環境です　改造要望等はまずこちらで実験します　データは頻繁にリセットされます　動作テストなどご自由に操作いただけます ---';
		}else if(host!='kyoto-lighthouse.org'){
			str = '--- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ローカル開発環境です --- ';
		}
		if(str){
			$info = $('<p class="develop_info through_click">').prepend(str);
			$body.append($info);
			if(lcm_env.isloggedin && $('body').hasClass('testserver')){
				$topinfo = $info.clone().addClass("top").css('top', $('#adminbar').outerHeight()+'px');
				$('#main_content').prepend($topinfo);
			}
		}
	})();
});

//クリックイベントを下の要素に適用する。
//.through_click
//selectやtextareaなどではうまくとれていないみたい。focus関係？
//:hover等の擬似要素はjsからは操作できないので対応していない。
//するなら、mousemoveを取得して.hoverのようなクラスを付与? でもいちいちの:hoverに.hoverを併記するのは非効率なので。
jQuery(function ($) {
	$('.through_click').on('click', function(e){
		e = e ? e : event;
		e.preventDefault();
		var x, y, el;
		x = e.clientX;
		y = e.clientY;
		
		$(this).hide();//いったん非表示にすることで、直下の要素を座標から取得
		el = document.elementFromPoint(x,y); 
		$(this).show();
		el.click();
	});
});


/* プラグインの有無をチェック */
/* http://webchoko-tips.com/2014/01/plugin-exists/ */
function pluginExists( pluginName ){
	return [pluginName] || $.fn[pluginName] ? true : false;
}

//モーダル
//書き直す。
//modalを呼ぶ側にdata-jslcm-modal-id?を書き、相手を指定する。モーダル表示中はフォーカスを制御し、それ以外の部分については操作不可とする。
//例えばNetReaderで制御を抜けた時はどうするの、とか、考えておくとよいのかも。(NetReader側では一時的にmodal外をdisplay: none;にするとか？　その場合、うまくイベントを抜けられなかったときが不安)
/*function lcm_modal(id){
	var el, wrapper, closelink;
	el = document.getElementById(id);
	if(el){
		jQuery(function ($){
			el = $('#'+id);
			wrapper = document.createElement('div');
			closelink = document.createElement('a');
			wrapper.id = 'modal_wrapper';
			wrapper.dataset.lcm_modalid = id;
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



jQuery(function ($){
/*=== 基本的な設定 ===*/
//JavaScript有効時に表示、無効時にはCSSで非表示
$('.hide_if_no_js').removeClass('hide_if_no_js').addClass('show_if_js');
$('.hide_if_no_js').find(':disabled').prop("disabled", false);

//.show_if_no_js noscript的な扱い?
$('.show_if_no_js').hide();

//for NetReader
//NetReaderで付与されたスタイルに負けることがあるようなので、.hidden_itemをインラインスタイルでdisplay: none;
$('.hidden_item').hide();

//アクセスキーをもつ要素へのタイトル付与
//accessKeyLabelが取得できないブラウザではaccessKeyを表示する。
function add_accesskey_title(){
	if($(this).hasClass('has_accesskey')) return; //すでに付与済みの場合終了。adminbar用jsと競合するため

	var str, txt, label;
	label = this.accessKeyLabel;
	label = label ? label : this.accessKey;
	if(label){
		txt = $(this).clone(false);
		txt.find('.skip').remove();
		str = ( $(this).attr('title') || txt.text() || $(this).children().attr('alt') );
		$(this).attr('title',str+'['+label+']').addClass('has_accesskey');
	}
}
$(document).find('[accesskey]').each(add_accesskey_title);
});

//大きなくくり。
jQuery(function ($){

/*=== 環境の取得 ===*/
//lcm_env.UAがなければいろいろ設定
if(!lcm_env.UA){
	lcm_env.UA = window.navigator.userAgent;
	lcm_env.isNetReader = lcm_env.UA.indexOf('NetReader') > 0 ? true : false;
	$('body').addClass(lcm_env.isNetReader ? 'netreader' : '');
	lcm_env.isTouchDevice = lcm_env.UA.indexOf('iPhone') > 0 || lcm_env.UA.indexOf('iPod') > 0 || lcm_env.UA.indexOf('iPad') > 0 || lcm_env.UA.indexOf('Android') > 0 ? true : false;
	lcm_env.isie          = !$('body').hasClass('lcm_ieversion_0') ? true : false;
	lcm_env.isLtie9       = $('body').hasClass('lcm_ieversion_8') || $('body').hasClass('lcm_ieversion_7') || $('body').hasClass('lcm_ieversion_6') ? true : false;
	
}

//フォーカスするついでに場合によってはセレクトもする
function set_focus(t){
	var $t = $(t);
	$t.focus();
	if($t.is(':input') && !lcm_env.isNetReader){
		$t.select();
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
		$('#main_content').focus();
		document.body.scrollTop = 0; //描画が遅れるとカクカクしちゃうので、containerの描画位置自体を考えられたらよいのかも？
	}
	
	//見出しのh1に最初のフォーカスを与える
	$('h1').first().not(':has(>a)').attr('tabindex', '0');
})();

//非表示の要素の設定
$('.hidden_item').each(function(){
	var query, params, v, $trigger ; 
	//hidden_itemでデフォルトが.offの場合、中身に関係なく折りたたみ
	if($(this).is('.off')) {
		return;
	}
	//hidden_itemでも検索条件がデフォルトでない場合は展開しておく
	if($(this).find('form.search')[0]){
	//検索フォームの場合、get値を見る。
		query = window.location.search.substring(1);
		if(query!=''){
			params = query.split('&');
			for(var i=0, len = params.length ; i < len; i++){
				if( params[i].indexOf('orders') !== 0 && params[i].indexOf('page') !== 0 ){//ordersとpagesの場合は無視
					v = true;
					break;
				}
			}
		}
	}else if($(this).find(':input')[0]){
		$(this).find(':input').each(function(){
//		console.log($(this).attr('class')+' : '+$(this).val())
			v = $(this).val()!='' ? true : v;
		});
	}
	if(!v) {
		$(this).addClass('off');
		return;
	}
	$trigger = $('.toggle_item').eq($('.hidden_item').index(this));
	$(this).removeClass('off').addClass('on').show();
	$trigger.addClass('on');
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
	var $html, $el, rs, top;
	$html = $('html');
	top = $html.scrollTop();
	$el = $('<div>').height(10000).prependTo('body');
	$html.scrollTop(10000);
	rs = !!$html.scrollTop();
	$html.scrollTop(top);
	$el.remove();
	return rs;
})();

// ページ内リンク
$(document).on('click', 'a[href^=#]', function(e){
	e = e ? e : event;
	var href, $t, position;
	$(window).off('beforeunload');// ページ内リンクでは画面遷移の警告をキャンセル

	href= $(this).attr("href");
	if(href!='#'){
		// フォーカスを確実に移動させるために、ターゲットがtabbableでない場合はtabindex-1を付与する
		if(href != ''){
			$t = $(href);
			if(!$t.attr('tabindex')){
				$t.attr('tabindex', '-1');
			}
		}else{
			$t = $('html');
		}
		lcm_smooth_scroll($t);
		set_focus($t);
		return false;
	}else if(e.isDefaultPrevented()){ // #でイベントを設定されている場合に抑止？ 
		e.preventDefault();
	}
});

// フォーカスしたものが画面外にある場合に位置を調節する。
// クリックは除外したい
// もう少し条件を整理したら、上のページ内リンクともまとめられる？
$(document).on('keydown',function(e){
	e = e ? e : event;
	var k, $t, position;
	k = e.which;
	if( k == 9 ){
		setTimeout(function(){;
			$t = $(':focus');
			lcm_smooth_scroll($t);
		}, 0);
	};
})

function lcm_smooth_scroll($t) {
	var position, margin;
	if($t.closest($('#adminbar'))[0]) return;
	position = $t.offset();
	if(typeof position === 'undefined') return;
	lcm_env.headerheight = (!lcm_env.headerheight==0) ? lcm_env.headerheight : 0;// adminbarとの兼ね合いを考えないといけない
	margin = 10;// 上位置のマージン

	position = position.top-$(window).scrollTop()-lcm_env.headerheight;
	if(position > margin) return;
	$(is_html_scrollable ? 'html' : 'body').scrollTop($t.offset().top-lcm_env.headerheight-margin);
}

//要素の中央配置
$.fn.set_center = function(){
	var left, top;
	left = Math.floor(( $(window).width()-this.outerWidth() ) /2);
	top  = Math.floor(( $(window).height()-this.outerHeight() ) /2);
	this.css({'left': left, 'top': top});
	return this;
}
$(window).resize(function(){
	var $el = $('.set_center, .lcm_modal.on');
	if($el[0]){
		$el.set_center();
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

//=== form ===

//エラー時の入力エリアから一覧へのナビゲーション //複数の入力欄がある場合(開始・終了時刻等)はどうする？idを配列にしてしまって配列なら後者を参照できるようにして、その後ろに戻るリンクを作る、とか？
//とりあえずスケジューラについては一旦個別対応
//.validation_error が適切につくようならそれを見るとよいのかも
$('#alert_error .link').find('a').each(function(){
	var $link, $t;
	$link = $('<a href="#anchor_alert_error" class="skip show_if_focus link_alert_error">エラー一覧にもどる</a>');
	$t = $($(this).attr('href'));
	if($t.closest('.lcm_multiple_select')[0]){
		$t.closest('.lcm_multiple_select').eq(0).append($link);
		return;
	}else if($t.is('#form_start_date')){
		$t = $('#form_end_date');
	}else if($t.is('#form_start_time')){
		$t = $('#form_end_time');
	}
	$t.after($link);
});



/*=== グループ絞り込み Ajax ===*/
var base_uri = $('body').data('uri');
function lcm_select_narrow_down(group_id, uri, $select, $selected){
	var $label_item, $selected_items, now_items, name, id;
	$label_item = $select.find('option[value=""]')[0] ? $select.find('option[value=""]') : $();//valueが空のものをラベルとみなす。
	if(!!$selected){ // 選択済みのものがある場合(multiple_select_narrow_down)
		$selected_items = $selected.find('option');
		now_items = new Object();
		for(var i = 0, len = $selected_items.length; i < len; i++){
			now_items[$selected_items[i].value] = 1;
		};
	}

	//  Locomoでは、-10が「ログインしているユーザ」なので、空のgroup_idがきたら、明示的に-10をわたす
	// usr以外で-10が渡るのも不便かなあ
		group_id = ! group_id ? -10 : group_id;
	$.ajax({
		url: uri,
		type: 'post',
		data: 'gid=' + group_id,
		success: function(res) {
			var exists = JSON.parse(res||"null");
			select_items = '';
			for(var i in exists) {
				//scdl/reserveはuser:idとbuilding:item_idの２通りで管理している。あとで整理したい。
				if ($(now_items)[0] && $(now_items[exists[i]['id']])[0] ) continue;
				if ($(now_items)[0] && $(now_items[exists[i]['item_id']])[0] ) continue;
				
				id = (exists[i]['item_id']!=null) ? exists[i]['item_id'] : exists[i]['id'];
				name = (exists[i]['display_name']!=null) ? exists[i]['display_name'] : exists[i]['item_name'];
				select_items += '<option value="'+id+'">'+name+'</option>';
			}
			$select.html(select_items).prepend($label_item);
		}
	});
}

//通常の選択ボックス
$('.select_narrow_down').each(function(){
	var $select, uri;
	$select = $('#'+$(this).data('targetId'));
	uri = base_uri;
	uri += $(this).data('uri') ? $(this).data('uri') : 'usr/user_list.json';
	this.onchange = function(){
		lcm_select_narrow_down($(this).val(), uri, $select);
	};
});

//複数選択ボックスを持つ場合
$('.multiple_select_narrow_down').each(function(){
	var uri, $selects, $select, $selected; 
	uri = base_uri;
	uri += $(this).data('uri') ? $(this).data('uri') : 'usr/user_list.json';
	$selects  = $('#'+$(this).data('targetId')).find('select');
	$select   = $selects.eq(1);
	$selected = $selects.eq(0);
	this.onchange = function(){
		lcm_select_narrow_down($(this).val(), uri, $select, $selected);
	};
});


/* スケジューラ一日詳細グラフ用 あとで分ける */
if($('#schedule_graph')[0]){
	$('.lcm_tooltip_parent').each(function(){
		$(this).on('click', function(e){
			e = e ? e : event;
			var href = $('[data-jslcm-tooltip-id="'+$(this).data('jslcmTooltipId')+'"]').find('a').attr('href');
			if(href.length){
				location.href = href;
				return false;
			}
		});
	});
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
