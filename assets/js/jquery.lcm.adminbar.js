/* === adminbar用にjsを追加 === */
/* 他に、jquery.lcm.modal.semimodal.js, jquery.lcm.floatwindow.js, jquery.lcm.msgconfirm.js が必要。
/* 
/* =========================== */

if(typeof(lcm_env)=='undefined') var lcm_env = new Object();
jQuery(function ($){

/* 非表示要素の設定 */
//for NetReader
//NetReaderで付与されたスタイルに負けることがあるようなので、.hidden_itemをインラインスタイルでdisplay: none;
$('.hidden_item').hide();

var $adminbar = $('#adminbar')

// 管理バーの高さを取得
if(!lcm_env.headerheight){
	lcm_env.headerheight = 0;
	if($adminbar[0]) lcm_env.headerheight = $adminbar.outerHeight();
}
// ヘッダーの高さぶん(+α)bodyにpaddingを追加
if($adminbar[0]){
	add_body_padding(lcm_env.headerheight);
	$adminbar.exResize(function(){
		lcm_env.headerheight = $(this).outerHeight();
		add_body_padding(lcm_env.headerheight);
	});
}
function add_body_padding(){
	
	$('body').css('cssText', $('body').attr('style') + '; padding-top: '+lcm_env.headerheight+'px !important;' );
}

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


//アクセスキーをもつ要素へのタイトル付与
//accessKeyLabelが取得できないブラウザではaccessKeyを表示する。
function add_accesskey_title(){
	if($(this).hasClass('has_accesskey')) return; //すでに付与済みの場合終了。jquery.inc.jsと競合するため
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
