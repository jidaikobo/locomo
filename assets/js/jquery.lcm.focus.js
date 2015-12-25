if(typeof(lcm_env)=='undefined') var lcm_env = new Object();
$(function(){
	/*=== 環境の取得 ===*/
	lcm_env.UA = window.navigator.userAgent;
	lcm_env.isNetReader = lcm_env.UA.indexOf('NetReader') > 0 ? true : false;
	$('body').addClass(lcm_env.isNetReader ? 'netreader' : '');
	lcm_env.isTouchDevice = lcm_env.UA.indexOf('iPhone') > 0 || lcm_env.UA.indexOf('iPod') > 0 || lcm_env.UA.indexOf('iPad') > 0 || lcm_env.UA.indexOf('Android') > 0 ? true : false;
	lcm_env.isie          = !$('body').hasClass('lcm_ieversion_0') ? true : false;
	lcm_env.isLtie9       = $('body').hasClass('lcm_ieversion_8') || $('body').hasClass('lcm_ieversion_7') || $('body').hasClass('lcm_ieversion_6') ? true : false;
	
	/*=== フォーカス制御の是否 ===*/
	lcm_env.tabindexCtrl  = true;
	query = window.location.search.substring(1);
	if(query!=''){
		var params = query.split('&');
		for(var len = params.length, n = len-1  ; n > 0; n--){
			var param = params[n];
			if( param.indexOf('limit') == 0 ){
				param_val = param.split('=')[1]
				if(param_val >= 50) lcm_env.tabindexCtrl = false;
			}
		}
	}
	lcm_env.tabindexCtrl  = lcm_env.isNetReader || lcm_env.isLtie9 || lcm_env.isTouchDevice || $('body').hasClass('nofocusctrl') ? false : lcm_env.tabindexCtrl;

	

// ================== lcm_focus ==================

//.lcm_focus フォーカス枠の設定 //フォーカス制御がむずかしい環境は除外
if(lcm_env.tabindexCtrl && $('.lcm_focus')[0]){
	// 閲覧状態のフォーム内のlcm_focusを外す
	if($('.lcm_form.view')[0]){
		$('.lcm_form.view .lcm_focus').removeClass('lcm_focus').removeAttr('tabindex');
		//ブロック説明用に、lcm_focusとは別にフォーカスを与えることを想定してlcm_form内のinput_groupはtabindexを持っている。そもそも妥当？
	}
	lcm_focus();
}
function lcm_focus(){
	var elm, current_tabbable, esc;
	elm = $(document).find('.lcm_focus');
	// === set_focus === 
	//フォーカス対象を指定して実行されている場合はそれを、なければlcm_focusを相手にする。
	//?ない場合?：初回と、lcm_focus最上部で抜ける時。
	lcm_focus_set = function(target){
		var parent, t; 
		$('.currentfocus').removeClass('currentfocus');

		if(!esc){
			$(document).tabindex_ctrl();
		}else if(target && target.hasClass('lcm_focus')){
			target.parents('.lcm_focus');
			parents = target.parents('.lcm_focus').addClass('focusparent');
			target.addClass('currentfocus').css('position', 'relative').tabindex_ctrl();
		}else{
			$(document).tabindex_ctrl();//重いかなあ。
		}
		// ================== esc ==================
		
		if(!esc){//抜けるリンクなどの準備
			esc = $('<div id="esc_focus_wrapper" class="skip show_if_focus" style="display: none;" tabindex="0"><a id="esc_focus"  class="boxshadow" href="javascript: void(0);" tabindex="-1">抜ける</a></div>').appendTo($('body'));
			var len = elm.length;
			for( var n = len-1 ; n >= 0 ; n-- ){
				el = elm.eq(n);
				var title_str = el.attr('title') ? el.attr('title') : '';
				el.attr('title', title_str+' エンターで入ります')
			}
		}

		//targetの中にlcm_focusがあれば中身のtabindexを-1にする
		t = target ? target.find('.lcm_focus') : elm;
		t.attr('tabindex', '0');
		t.find(':tabbable').attr('tabindex', '-1');

		//現在のフォーカス対象を取得し、抜けるリンクの枠の表示領域をcurrentfocusを元に設定
		var current = $('.currentfocus');
		if(current[0]){
			current_tabbable = current.find(':tabbable');
			esc.show().attr('tabindex','0');
			esc.css({
				'top'   : current.offset().top,
				'left'  : current.offset().left,
				'width' : current[0].scrollWidth,
				'height': current[0].scrollHeight - current.height() < 0 ? current[0].scrollHeight : current.height(),
			});
		}else{
			current_tabbable = $(document).find(':tabbable');
			esc.hide();
		}
	}
	// === lcm_focus_esc ===
	//フォーカス有効時にESCや「抜ける」リンクでフォーカスを1階層抜ける。
	var lcm_focus_esc = function(e){
		e = e ? e : event;//抜けるリンクはeがclickイベントになり、tが#esc_focusになる
		e.preventDefault();
		e.stopPropagation();
		var t, current, parent;
		t = $(e.target);

		current = $('.currentfocus').eq(0).removeClass('currentfocus').tabindex_ctrl().focus();
		esc.hide();
		parent = $('.focusparent')[0] ? $('.focusparent').last() : null;
		$(document).tabindex_ctrl('reset');
		if(parent){
			parent.removeClass('focusparent').addClass('currentfocus');
		}
		lcm_focus_set(parent);
	}
	//ひとまず実行 //lcm_focusが入れ子になっていてもここで一旦-1
	setTimeout(lcm_focus_set, 0);

	// ================== lcm_focus上でのキーボードイベント ==================
	// 要素が多いページのことを考えるとtabindexの操作をやめてしまいたいので、Tabでの移動制御をもう少しまとめる。
	// いずれにせよブラウザ間の挙動の差異を埋めるためなどの理由でtabindexの制御はやめたほうがよさそう。
	// Tabキーの動きをのっとっちゃうことのデメリットはよくよく調べること
	elm.on('keydown', function(e){
		e = e ? e : event;
		var t, k, parent, is_esc;
		t = $(e.target);
		k = e.which;
		if(k == 9){ //Tab
			if( current_tabbable.length == 0){
			// 中にフォーカス対象がないとき（読み上げ用の枠にtabindexをあてているなど）。この場合Enterで入れる状態にすべきかどうかわからないのだけど、入れたらタブ移動対象は抜けるリンクだけにする。
						esc.focus()
						is_esc = true;
			}else{
				var index = $(current_tabbable).index(t);
				if(e.shiftKey){ //現在のフォーカス枠上でshift+tabの場合、escに移動
					if(t.hasClass('currentfocus') || index==0){//とりあえず
						esc.focus()
						is_esc = true;
					}else{
						$(current_tabbable).eq(index-1).focus();
					}
				}else{
					if(t.is($(current_tabbable).last())){
						esc.focus()
						is_esc = true;
					}else{
						$(current_tabbable).eq(index+1).focus();
					}
				}
			}
			e.preventDefault();
			if(is_esc) return false;
		}
		
	
		if( k == 13 ){//Enter
			if(lcm_env.isie && !t.is('a') && !t.is(':input')){
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


	//datepickerが、フォーカス時に表示されてしまうのをふせぐ。
	$(document).on('focus', '#esc_focus_wrapper', function(){
		$('#ui-datepicker-div').hide();
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
		//キーボードでのESC。
		e = e ? e : event;
		var t, k;
		t = $(e.target);
		k = e.which;
		
//		console.log($('.modal.on, .semimodal.on'));
		if($('.currentfocus')[0]){
			if(k == 13 && (t.is('#esc_focus_wrapper')) || 
				(k == 27 && !t.is(':input') && !$('.modal.on, .semimodal.on')[0])){
				lcm_focus_esc(e);
				e.stopPropagation();
			}
		}
	});
	
	$(document).on('focus', '#esc_focus', function(e){
		e = e ? e : event;
		lcm_focus_esc(e);
	});
}

});
