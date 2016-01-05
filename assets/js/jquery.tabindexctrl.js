if(typeof(lcm_env)=='undefined') var lcm_env = new Object();
(function($){
	/*=== lcm_envにフォーカス制御の是否 ===*/
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
	
	
	var methods = {
		set : function() {
			//tabindexを一旦dataに格納し、現在の要素のみtabindex制御をリセットする。
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
			$(this).tabindex_ctrl('reset');
			return this;
		},
		reset :  function(){
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
	};

	$.fn.tabindex_ctrl = function(method){//呼び出したいものを渡す
		if(methods[method]){
			return methods[method].call(this);
		}else if(!method){ //引数のない場合 => set扱い
			return methods['set'].call(this);
		} else {
		
		}
	}
	
})(jQuery);