$(function(){
//UA
var userAgent = window.navigator.userAgent;

isNetReader = userAgent.indexOf('NetReader') > 0 ? true : false;

//JavaScript有効時に表示、無効時にはCSSで非表示
$('.hide_if_no_js').removeClass("hide_if_no_js");

//.show_if_no_js的な処理があるとすれば？？
//$(".show_if_no_js").();

//ページ読み込み直後のフォーカス制御
if($('.flash_alert')[0]){
	var firstFocus = $('.flash_alert').first();
}else if($('body').hasClass('login')){
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
var isHtmlScrollable = (function(){
	var html = $('html'), top = html.scrollTop();
	var elm = $('<div/>').height(10000).prependTo('body');
	html.scrollTop(10000);
	var rs = !!html.scrollTop();
	html.scrollTop(top);
	elm.remove();
	return rs;
})();
//スクロール
$(document).on('click', 'a[href^=#]', function(){
	var href= $(this).attr("href");
	var t = $(href == '#' || href == '' ? 'html' : href);
	var position = t.offset().top-headerheight-10;
	$(isHtmlScrollable ? 'html' : 'body').animate({scrollTop:position}, 250, 'swing');
	set_focus(t);
	return false;
});

//クリックイベント
$(document).click(function(event){
	var t = event.target;
//リストの開け閉め
	if(isModal){
		closeModal(t);
	}
} );

//モーダル
var isModal = false; 
var closeModal = function(t){
	$('.modal.on').removeClass('on');
	isModal = false;
}
$('a.modal').click(function(event){
	var index = $('.toggle_item').index(this); //modalは.toggle_item-.hidden_itemとセットで使う
	var t = $('.hidden_item').eq(index);
	if($(this).hasClass('on')){
		$(document).find('.modal.on').removeClass('on');
		isModal = false;
	}else{
		t.addClass('on');
		$(this).addClass('on');
		isModal = true;
	}
	return false;
});
$(document).on('click', '.modal.on', function(){
	event.stopPropagation();
});

//表示・非表示切り替え
$('.hidden_item').each(function(){
	if((isModal == true)&& ( $(this).not(':input') || $(this).val()=='')){
		$(this).hide();
	}
});
$(document).on('click', '.toggle_item', function(){
	var index = $('.toggle_item').index(this);
	var t = $('.hidden_item').eq(index);
	t.toggle().toggleClass('on');
	$(this).toggleClass('on');
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
$('.datetime.schedule').datetimepicker({
	timeFormat: 'HH:mm',
	stepMinute: 15
});
$('.datetime').datetimepicker();

$('.date').datepicker();
$('.date').datepicker("option", "dateFormat", 'yy-mm-dd');
$('.date').datepicker("option", "changeMonth", true);
$('.date').datepicker("option", "changeYear", true);

//tooltip
//title属性はブラウザの対応がまちまちなので、data-を対象にする
//※要調
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

//login failed
//中央表示の方法要考？ jquery uiのeffectは制御しづらそうなのでここではつかわない
//そもそもjavascriptだけヘッダに直書きのほうがよいかも。
/*
if($('.login .alert_error')[0]){
	$('.container').delay(0)
                   .animate({ left: -8 }, 75)
                   .animate({ left: 8 }, 75)
                   .animate({ left: -8 }, 75)
                   .animate({ left: 8 }, 75)
                   .animate({ left: 0 }, 75);
}
*/





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

});


//lightbox
//$(document).ready(function(){}); いらない？
$(document).ready(function(){
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
