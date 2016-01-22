if(typeof(lcm_env)=='undefined') var lcm_env = new Object();
lcm_env.load_lcm_modal_semimodal = true;
(function($){
	/*=== 環境の取得 ===*/
	//lcm_env.UAがなければ設定
	if(!lcm_env.UA){
		lcm_env.UA = window.navigator.userAgent;
		lcm_env.isNetReader = lcm_env.UA.indexOf('NetReader') > 0 ? true : false;
		$('body').addClass(lcm_env.isNetReader ? 'netreader' : '');
		lcm_env.isTouchDevice = lcm_env.UA.indexOf('iPhone') > 0 || lcm_env.UA.indexOf('iPod') > 0 || lcm_env.UA.indexOf('iPad') > 0 || lcm_env.UA.indexOf('Android') > 0 ? true : false;
		lcm_env.isie          = !$('body').hasClass('lcm_ieversion_0') ? true : false;
		lcm_env.isLtie9       = $('body').hasClass('lcm_ieversion_8') || $('body').hasClass('lcm_ieversion_7') || $('body').hasClass('lcm_ieversion_6') ? true : false;
	}
	
	//全体に対するクリックイベント。
	$(document).click(function(e){
		e = e ? e : event;
		var t = e.target;

	//modalの枠外伝播防止	
		if($(t).is($('#modal_wrapper'))){
			e.stopPropagation();
			return false;
		}
	
	//リストの開け閉め
		$(document).lcm_modal('close_semimodal',t);
		replace_info();//開く・閉じる説明文切り替え
	} );

	//モーダルの外制御//キーボードのことを考えてdisabled制御をするならclick処理は重複？
	$('#modal_wrapper').on('click', function(){
		return false;
	});

	//親を閉じる
	$('.lcm_close_window').on('click', function(e){
		e = e ? e : event;
		var w = $(this).parent();
		$(this).lcm_modal('close_window', w);
		e.preventDefault();//抑止しておかないとIEでページ遷移前の警告が出る
	});
	
var methods = {
	close_window : function(w){
		w.hide();
		if($(w.find('.lcm_close_window')[0]).hasClass('lcm_reset_style')){
			w.removeAttr('style').hide();
		}
	},
	close_modal : function(focus, t){
		//modalを閉じる機能、で、semimodalと併用できるように考える
		//現在のtabbableを取る？
		focus.focus();
		t.removeClass('on');
		$(document).tabindex_ctrl('reset');
	},
	close_semimodal: function(el){
		var t, trigger, focus;
		t = $(document).find('.semimodal.on');
		if(t[0]){
			trigger = $('.toggle_item').eq($(document).find('.hidden_item').index(t));
			focus = ($(el).is(':input')) ? el : trigger;
			trigger.removeClass('on');
			$(document).lcm_modal('close_modal', focus,t);
		}
		return false;
	},
	
};


$.fn.lcm_modal = function(method){
	if(methods[method]){
		methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
	}
}


function replace_info(){ //説明文の切り替え
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


/* === 実行部分 === */
$(document).on('click', '#close_modal' ,function(){
	$document.lcm_modal('close_modal', $('.modal_parent'), $('.lcm_modal_on'));
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
	
	if($('.semimodal.on')[0]){//モーダルが開いている場合は閉じる
		var itself = t.is('.semimodal.on');
		$(document).lcm_modal('close_semimodal');
		replace_info();//開く・閉じる説明文切り替え
		if(itself) return;//モーダルが自分ならそこでおわり
	}
	if(t.is('.on')){
		t.removeClass('on').addClass('off');
	}else{
		t.removeClass('off').addClass('on');
	}
	$(this).toggleClass('on').focus();

	if(t.is('.semimodal.on')){//tabindex制御
//		t.set_tabindex();
		t.tabindex_ctrl('set');
		//targetの中とtoggleの要素だけtabindexを元に。//data('tabindex')を見る？
		$(this).removeAttr('tabindex');
	}
	replace_info();//開く・閉じる説明文切り替え
	
	e.stopPropagation();
	return false;
});


//キーボード操作の制御

//NetReaderでうまく取得できないので、なにか考える
//.lcm_focusのようにまず枠にフォーカスを当てる場合のShift+Tabの動作のことも
//フォーカス枠のある時の表示位置の調整もかんがえる(ページ内リンクのスクロールと同じ)

$(document).on('keydown',function(e){
	e = e ? e : event;
	var t, k, index, modal, tabbable, first, last;
	t = e.target;
	k = e.which;
	// k = 9:tab, 13:enter,16:shift 27:esc, 37:←, 38:↑, 40:↓, 39:→
	index = null;
	
	modal = $(document).find('.lcm_modal.on, .semimodal.on, .currentfocus');//これらが混在することがある？
	triger = $('.toggle_item.on');
	if($(modal)[0]){
		tabbable = triger.add($(modal).find(':tabbable'));
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
				$(document).lcm_modal('close_semimodal');
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

})(jQuery);