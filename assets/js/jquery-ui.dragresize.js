if(typeof(lcm_env)=='undefined') var lcm_env = new Object();
lcm_env.load_dragresize =  true;

$(function(){
	//resizable, draggable //画面の上下はみ出してドラッグしたときのふるまい?
	$('.resizable').resizable({
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
	});
	$('.draggable').draggable({
		'handle'      : '.lcm_floatwindow_title',//このハンドルをどう決めるか、ちょっとかんがえる。
		'containment' : 'document',
		'scroll' : true,
		stop:function(e, ui) {
			e = e ? e : event;
			var el = $(e.target);
		}
	});
	
	//テキストエリアのリサイズ非対応ブラウザへの処置。とりあえずIEのみ
	var ta_unresizable = lcm_env.isie ;
	//もともとの最大幅・最大高とのかねあいを解消できるようにしたい
	//現在幅・高さをいったん明示的に与えて、max-widthとmax-heightをauto?にすればよい？？？
	if(!lcm_env.isLtie9 && ta_unresizable){
		$('textarea').resizable({
			'maxWidth' : 800,
			'minWidth' : 60,
			'minHeight': 30,
			'contain'   : '#main_container'
	}).parent().addClass('resizable_textarea');
	}
});