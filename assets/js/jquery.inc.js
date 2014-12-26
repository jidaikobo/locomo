// ヘルプ呼び出し
var help_preparation = false;
function show_help(e){
	if(!e){
		e = event;
	}
	if(e){
		e.preventDefault(); //クリックイベント以外(アクセスキー等)の場合を除外
	}
	$(function(){
		if(!help_preparation){
			var uri = $('#lcm_help').data('uri');
			$.ajax({
				url: uri,
				dataType: 'html',
			})
			.success(function(data) {
				$("#help_txt").html(data);
				help_preparation = true;
			})
		}
		$("#help_window").show();
		$("#help_title_anchor").focus();
	});
}
$(function(){
	$('#lcm_help').click(show_help);
});

//モーダル
function modal(id){
	var el = document.getElementById(id);
	if(el){
		$(function(){
			var el = $('#'+id);
			var wrapper = document.createElement('div');
			var closelink = document.createElement('a');
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
			$(el).set_center();
			$(el).set_tabindex();
		});
	}
	event.stopPropagation();
	return false;
}

$(function(){
//UA //php側は？
var userAgent = window.navigator.userAgent;
isNetReader = userAgent.indexOf('NetReader') > 0 ? true : false;

//JavaScript有効時に表示、無効時にはCSSで非表示
$('.hide_if_no_js').removeClass("hide_if_no_js");

//.show_if_no_js noscript的な扱い
$(".show_if_no_js").remove();

//ページ読み込み直後のフォーカス制御
if($('.flash_alert')[0]){
	var firstFocus = $('.flash_alert a.skip').first();
}else if($('body').hasClass('lcm_action_login')){
	var firstFocus = $('input:visible').first();
}
if(firstFocus){
	set_focus(firstFocus);
}
function set_focus(t){
	$(t).focus();
	if($(t).is(':input') && !isNetReader){
		$(t).select();
	}
}

//スクロールバーのサイズ取得
var scrollbar_s = (function(){
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

//モーダルの外制御 //キーボードのことを考えてdisabled制御をするならclick処理は重複？
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

//要素の中央配置
$.fn.set_center = function(){
	var left  = Math.floor(( $(window).width()-this.outerWidth() ) /2);
	var top   = Math.floor(( $(window).height()-this.outerHeight() ) /2);
	this.css({'left': left, 'top': top});
}
$(window).resize(function(){
	var el = $('.set_center, .modal.on');
	if(el){
		el.set_center();
	}
});

//アクセスキーをもつ要素へのタイトル付与 //読み上げ要確認
//accessKeyLabelが取得できないブラウザでは、accessKeyを表示する。できないブラウザのほうが多い？
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


//tabindex制御
$.fn.set_tabindex = function(){
	$(document).find(':focusable').each(function(){
	if($(this).attr('tabindex')){
		$(this).data('tabindex',$(this).attr('tabindex'));
	}
	$(this).attr('tabindex','-1');
	});
	$(this).find(':focusable').removeAttr('tabindex');
}
$.fn.reset_tabindex = function(){
	$(document).find(':focusable').each(function(){
		if($(this).data('tabindex')){
			$(this).attr('tabindex', $(this).data('tabindex'))
		}else{
			$(this).removeAttr('tabindex');
		}
	});
}





//表内スクロール - 各ブラウザでの挙動が怪しいのでもうちょっと
if( !isNetReader && $('.tbl_scrollable')[0]){
/*
//スクロールバーの幅ぶん調整したい
//現状だと右端は最終列にかぶり、下端はスクロールバー分はみ出る（margin-bottm: -{スクロールバー};）
//おなじく、ボーダーの幅
//wrapperに枠を表示できる？
*/
	$(document).find('.tbl_scrollable').each(tbl_scrollable);
	
	function tbl_scrollable(){
		var thead, tfoot, h, tbl_wrapper, thead_wrapper, tbody_wrapper, tfoot_wrapper, fixed_thead, fixed_tfoot;
		thead = $(this).find('thead').clone();
		tfoot = $(this).find('tfoot').clone();
		if(thead.length || tfoot.length){
			tbl_wrapper = $('<div>').addClass('jslcm_tbl_wrapper');
			tbody_wrapper = $('<div>').addClass('jslcm_tbody_wrapper');
			if(thead.length){
				thead_wrapper = $('<div>').addClass('jslcm_thead_wrapper');
				fixed_thead = $('<table>').addClass($(this).attr('class')+' jslcm_fixed_thead').removeClass('tbl_scrollable').attr('aria-hidden','true').append(thead);
				$(fixed_thead).find(':tabbable').attr('tabindex', '-1');
			}
			if(tfoot.length){
				tfoot_wrapper = $('<div>').addClass('jslcm_tfoot_wrapper');
				fixed_tfoot = $('<table>').addClass($(this).attr('class')+' jslcm_fixed_tfoot').removeClass('tbl_scrollable').attr('aria-hidden','true').append(tfoot);
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
	
	function adjust_columns(tbl, ws){
		//exresizeで変更を取得しているときには、そちらのサイズを使う……のでなければならなかったのかは、要確認。
		//フォントサイズの変更はどうにか取れなかったかなあ……も要確認
		//読み込み時に動いていないのも要確認
		var thead, tfoot, fixed_thead, fixed_tfoot, thead_cols, tfoot_cols, fixed_thead_cols, fixed_tfoot_cols, thead_len, tfoot_len, w;
		thead = $(tbl).find('thead');
		tfoot = $(tbl).find('tfoot');
		//重複を整理したい、というより一回でできる？
		if(thead.length){
			thead_cols = $(tbl).children('thead').find('th, td');
			thead_len = thead_cols.length;
			fixed_thead = $(tbl).closest('.jslcm_tbl_wrapper').find('.jslcm_fixed_thead');
			fixed_thead_cols = $(fixed_thead).find('th, td');
			set_colswidth(thead_cols, thead_len, fixed_thead_cols, ws);
		}
		if(tfoot.length){
			tfoot_cols = $(tbl).children('tfoot').find('th, td');
			tfoot_len = tfoot_cols.length;
			fixed_tfoot = $(tbl).closest('.jslcm_tbl_wrapper').find('.jslcm_fixed_tfoot');
			fixed_tfoot_cols = $(fixed_tfoot).find('th, td');
			set_colswidth(tfoot_cols, tfoot_len, fixed_tfoot_cols, ws);
		}
	}
	function set_colswidth(cols, len, fixed_cols, ws){
		for(i=0; i<len-1; i++){
			if(ws){
				w = ws[i];
			}else{
				w = $(cols[i]).width();
			}
			$(fixed_cols[i]).width(w+1);//borderの太さを足す。とりあえず1pxで
		}
	}
	
	$.fn.el_overflow_y = function(){
		var parent, parent_h, parent_t, tbl, h, t, overflow, min_h;
		//ウィジェットや指定の枠がある場合は親にする。自分より小さな祖先ブロック要素を見つけてあわせる、ほうがいいのかなあ
		parent = $(this).closest('.widget, .parent_tbl_scrollable').length ? $(this).closest('.widget, .parent_tbl_scrollable')[0] : $('.container');
		tbl = $(this).find('.jslcm_tbl_scrollable');
		h = parseInt($(tbl)[0].scrollHeight, 10)+2;
		t = parseInt($(tbl).offset().top, 10);
		parent_h = parseInt($(parent).height(), 10);
		parent_t = parseInt($(parent).offset().top, 10);
		overflow = t - parent_t + h - parent_h;
		console.log();
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
	
	if($('.jslcm_tbody_wrapper').length){
		$('.jslcm_tbody_wrapper').el_overflow_y();	
		//ウィンドウリサイズ時やフォントサイズ変更時に追随したい（exResizeの挙動を再確認）
		//ブラウザによって、リサイズを捕捉できなかったりする？ ひとまず、Safariの拡大縮小要確認
		$(window).resize(function(){
			$('.jslcm_tbody_wrapper').el_overflow_y();
		});
		$('.jslcm_tbl_scrollable').exResize(function(){
			is_resize = true;
			$('.jslcm_tbody_wrapper').el_overflow_y(
				$(this).closest('.widget , .parent_tbl_scrollable').length ? $(this).closest('.widget , .parent_tbl_scrollable') : null
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
				set_colswidth(cols, cols.length, fixed_cols);
			}
		});
	}
}

//Focusまわりのテスト（NetReaderでFocus移動を検知したい）
//setActiveとか、activeElementとか、なにかIE7で使えるものでないと行けない
//が、最新版のNetReaderはIEが7でなくなったので、古い環境の動作確認はできない(再インストール？)

//管理バーの高さ+αのヘッダーの高さを確保
var headerheight = 0;
function add_body_padding(headerheight){
	$('body').css('padding-top', headerheight+'px' );
}
if($('#adminbar')[0]){
	var bar = '#adminbar';
	headerheight = $(bar).outerHeight();
	add_body_padding(headerheight);
	
	$(bar).exResize(function(){
	headerheight = $(this).outerHeight();
		add_body_padding(headerheight);
	});
}

//ページ内リンク ヘッダー分位置調整とスムーズスクロール
//html要素がスクロール対象であるか判定。
//http://www.webdesignleaves.com/wp/jquery/573/
var is_html_scrollable = (function(){
	var html, el, rs;
	html = $('html'), top = html.scrollTop();
	el = $('<div/>').height(10000).prependTo('body');
	html.scrollTop(10000);
	rs = !!html.scrollTop();
	html.scrollTop(top);
	el.remove();
	return rs;
})();
//スクロール
$(document).on('click', 'a[href^=#]', function(){
	$(window).off('beforeunload'); //ページ内リンクでは画面遷移の警告をキャンセル
	var href= $(this).attr("href");
	var t = $(href == '#' || href == '' ? 'html' : href);
	var position = t.offset().top-headerheight-10;
	$(is_html_scrollable ? 'html' : 'body').animate({scrollTop:position}, 250, 'swing');
	set_focus(t);
	return false;
});


//非表示
$('.hidden_item').each(function(){
	if( $(this).is(':input') && $(this).val() ){ //hidden_itemでも中に値がある場合は表示
		var index  = $('.hidden_item').index(this);
		var trigger = $('.toggle_item').eq(index);
		$(this).show().addClass('on');
		trigger.addClass('on');
	}
});


//クリックイベント
$(document).click(function(e){
	if(!e){
		e = event;
	}
	var t = e.target;
//リストの開け閉め
	close_semimodal(t);
} );


//モーダル
function close_modal(focus,t){
	//modalを閉じる機能、で、semimodalと併用できるようにパラメータを考える
	//現在のtabbableを取るなど。（ということはjqueryUIを使わずにtabbableを取得できる？focusable中のtabindexが-でないもの、で行ける？）
	focus.focus();
	t.removeClass('on');
	$(document).reset_tabindex();
}

function close_semimodal(el){
	var t = $(document).find('.semimodal.on');
	if($(t)[0]){
		var index = $(document).find('.hidden_item').index(t);
		var trigger = $('.toggle_item').eq(index);
		var focus = ($(el).is(':input')) ? el : trigger;
		trigger.removeClass('on');
		close_modal(focus,t);
	}
	return false;
}
$(document).on('click', '#close_modal' ,function(){
	close_modal($('.modal_parent'),$('.modal_on'));
});
$(document).on('click', '.semimodal.on, modal.on', function(e){
	if(!e){
		e = event;
	}
	e.stopPropagation();
});

//表示・非表示切り替え
$(document).on('click', '.toggle_item', function(e){
	if(!e){
		e = event;
	}
	var index = $('.toggle_item').index(this);
	var t = $('.hidden_item').eq(index);		//切り替える相手
	if($('.semimodal.on')[0] ){	//モーダルが開いている場合モーダルを消す
		var itself = t.is('.semimodal.on');		//開いているのはそのモーダルか
		close_semimodal();
		if(itself){	//モーダルが自分ならそこでおわり
			return false;
		}
	}
	$(t).toggleClass('on');
	$(this).toggleClass('on').focus();

	if(t.is('.semimodal.on')){ //ここまででsemimodalが開いている場合、tabindexの制御を行う
		t.set_tabindex();
		//targetの中とtoggleの要素だけtabindexを元に。//data('tabindex')を見る？ tabindex=0にする？
		$(this).removeAttr('tabindex');
	}
	e.stopPropagation();
	return false;
});

//キーボード操作の制御 prevent
//NetReaderでうまく取得できないので、なにか考える

$(document).on('keydown',function(e){
	if(!e){
		e = event;
	}
	var t = e.target;
	var k = e.which;
	var modal = $(document).find('.modal.on, .semimodal.on')[0];
	// k = 9:tab, 13:enter,16:shift 27:esc, 37:←, 38:↑, 40:↓, 39:→
	// TAB,ENTER,SHIFT,ESCAPE,RIGHT,UP,DOWN,RIGHT,(矢印系は、ALLOWをつけるようになる、らしい。バージョン？)
	//モーダル周り モーダルの外に出た時のことを考えるとdocument全体のキーイベントを見るのがいいのか、それとも.modal.onや.semimodal.onだけを相手にするのがいいのか
	if(modal){
		var tabbable = $(document).find(':tabbable');
		var first    = tabbable.first()[0];
		var last     = tabbable.last()[0];
		var index    = null;
		
		switch( e.keyCode ){
		case $.ui.keyCode.LEFT:
			return false;
			break;
		case $.ui.keyCode.RIGHT:
			return false;
			break;
		case $.ui.keyCode.DOWN:
			var index = tabbable.index($(':focus'))+1;
			if( t === last){
				var index = 0;
			}
			break;
		case $.ui.keyCode.UP:
			var index = tabbable.index($(':focus'))-1;
			break;
		case $.ui.keyCode.TAB:
			if( t === last && ! e.shiftKey){
				var index = 0;
			}else if( t === first && e.shiftKey){
				var index = -1;
			}
			break;
		case $.ui.keyCode.ESCAPE:
			close_semimodal();
			break;
		}
			if(index!==null){
			tabbable.eq(index).focus();
			return false;
		}
	}
});

//確認ウィンドウ
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
//エラー時には必ず。//フォームと無関係のエラーはどうする？
//login画面とsubmitがない場合(編集履歴など)では出さない。編集履歴はむしろdisableにするほうがよい？
//イベントを渡して.targetの値を見ることも可

function confirm_beforeunload(){
	$(window).on('beforeunload', function(){
		return '変更内容は保存されていません。';
	});
}

if($('a:submit, input:submit').length && !$('body').hasClass('lcm_action_login')){
	var datetime = $('.datetime');
	datetime.each(function(){
		var val = $(this).val();
		$(this).data('val',val);
	});
	$('form').change( function(e){
	if(!e){
		e = event;
	}
		var t = e.target;
		if($(t).hasClass('datetime') && $(t).val() == $(t).data('val') ){
			return false;
		}else{
			confirm_beforeunload();
		}
	});
	if($('#alert_error').children('ul.list').length ){
		confirm_beforeunload();
	}
}

//ページ遷移警告抑止
$('a:submit, input:submit').click(function(){
	$(window).off('beforeunload');
});


//エラー時の、入力エリアから一覧へのナビゲーション
$('.validation_error :input').after('<a href="#anchor_alert_error" class="skip show_if_focus">エラー一覧にもどる</a>');


//=== rollover ===
$('.bt a:has(img)').hover(function(){
	var imgsrc = $(this).find('img').attr('src');
	if( ! $(this).hasClass("on") && imgsrc.indexOf('_ro.') == -1){
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

$('input.date , input[type=date]').datepicker({
	dateFormat: 'yy-mm-dd',
	changeMonth: true,
	changeYear: true,
});

$('input.datetime.min15, input[type=datetime].min15').datetimepicker({
	timeFormat: 'HH:mm',
	stepMinute: 15
});
$('input.datetime.min30, input[type=datetime].min30').datetimepicker({
	timeFormat: 'HH:mm',
	stepMinute: 30
});
$('input.datetime,  input[type=datetime]').datetimepicker();

$('input.time.min15').timepicker({
	timeFormat: 'HH:mm',
	stepMinute: 15
});
$('input.time.min30').timepicker({
	timeFormat: 'HH:mm',
	stepMinute: 30
});
$('input.time').timepicker({
	timeFormat: 'HH:mm'
});

//tooltip
//title属性はブラウザの対応がまちまちなので、data-を対象にする
//※要確認
$('.validation_error :input').tooltip({
	tooltipClass : 'form_tooltip',
	show         : 200,
	hide         : 'fade',
	position     : {
		             my : 'left bottom-8',
		             at : 'left top'
		            },
	items        : '[data-jslcm-tooltip]',
	content      : function(){
	                 return $(this).data('jslcmTooltip')
		           }
});


//resizable
$('#help_window, .resizable').resizable({
//	'handles' : 'all',
	'containment' : 'document',
	/* 
	//positionのrightとresizeしたときのleftの位置の兼ね合いで動作不良、なんとかしたい。rightがないとwindowをリサイズしたときにはみでる。どこであわせたものか。
	stop : function(e, ui) {
    var el = $(e.target);
    var left = el.offset().left;
    var top = el.attr("startTop");
    console.log(left);
    }
    */
});

//draggable
$('#help_window').draggable({
	'handle' : '#help_title',
	'containment' : 'document',

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
