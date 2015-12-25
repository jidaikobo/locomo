$(function(){

/*=== lcm_multiple_select ===*/

$('.lcm_multiple_select').each(function(){
	var $select, $selected, $selects, $to, $from, hidden_items;
	$select = $($(this).find('.select_from'));
	$selected = $($(this).find('.selected'));
	$selects = $select.add($selected);
	hidden_items = $(this).data('hiddenItemId') ?
		$(this).data('hiddenItemId') :
		$(this).closest('.show_if_js').prevAll('.show_if_no_js').last();
		//スケジューラはnoscriptのチェックボックス未対応。別途hiddenの値をしようしている
	
	if(typeof hidden_items !== 'object'){
	//スケジューラの場合data-hidden-item-idを取っている。noscriptチェックボックス併用の場合はここは不要
		make_hidden_form_items(hidden_items, $selected);
	}
	
	$(this).find(':button').click(function(e){
		e = e ? e : event;
		$from = $(this).hasClass('add_item') ? $select : $selected;
		$to = $selects.not($from);
		lcm_multiple_select($from, $to, hidden_items, $selected);
	});
	$selects.dblclick(function(){
		$from = $(this);
		$to = $selects.not($from);
		lcm_multiple_select($from, $to, hidden_items, $selected);
	});
});

function lcm_multiple_select($from, $to, hidden_items, $selected){
	//引数$selectedはhidden_itemがなくなれば不要
	var vals, v, item;
	vals = $from.val();
	if ( vals == "" || !vals) return;
	//相手のセレクトボックスに移動
	for(var i=0, len = vals.length; i < len; i++){
		v = vals[i];
		item = $from.find('option[value='+v+']');
		item.appendTo($to).attr('selected',false);
		if(typeof hidden_items == 'object'){//この判定は、スケジューラ用の措置がなくなれば不要
			change_hidden_inputs($from, hidden_items, v);
		}
	}
	
	//スケジューラ用。hidden_itemがnoscript用のチェックボックスでない場合に。
	if(typeof hidden_items !== 'object'){
		make_hidden_form_items(hidden_items, $selected);
	}	
}

//selectedの中身をチェックボックスに反映
function change_hidden_inputs($from, hidden_items, v){
	var prop, $item;
	prop = $from.hasClass('selected') ? false : true;
	$item = $(hidden_items.find('input[value='+v+']'));
	$item.prop('checked', prop);
	}

//スケジューラ用hidden
function make_hidden_form_items(hidden_items, $selected){
	var $hidden_item = $('#'+hidden_items);
	if (!$hidden_item[0]) {
		$hidden_item = $('<input>').attr({
		    type : 'hidden',
		    id   : hidden_items,
		    name : hidden_items,
		    value: '',
		}).appendTo('form');
	}
	var hidden_str = "";
	var els = $selected.find('option');
	// 配列に入れる
	for( var len = els.length, n = 0; n < len ; n++){
		hidden_str += "/" + els.eq(n).val();
	}
	$hidden_item.val(hidden_str);
}

});
