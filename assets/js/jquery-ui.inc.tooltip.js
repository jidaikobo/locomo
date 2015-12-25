jQuery(function ($){
	//tooltip
	//表示枠外（overflow:hidden)の要素にページ内リンクでスクロールして表示するとスクロール前の位置を基準に表示されてしまう。
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
		relative     : true,
		items: '[data-jslcm-tooltip-id]',
		content: function() {
			var el = document.getElementById($(this).data('jslcmTooltipId'));
			el = $(el).html();
			return el
		},
		tooltipClass : 'lcm_tooltip',
		show         : 200,
		hide         : 'fade',
		position     : {
			             my : 'left bottom-8',
			             at : 'left top'
			            },
	});
});
