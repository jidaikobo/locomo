jQuery(function ($){
	tinymce.init({
		mode : "none",
		resize: "both",
		theme : "modern",
		language: 'ja',
		theme_advanced_buttons3_add : "tablecontrols",
		plugins:"table code",
	});
	
	$(':input.tinymce').each(function(){
		var id, state, label, btn;
		id = this.id;
		if($(this).hasClass('on')){
			state = 'on';
			label = 'テキストエディタに切替え';
			tinymce.EditorManager.execCommand('mceAddEditor', true, id);
		}else{
			state = 'off';
			label = 'ビジュアルエディタに切替え';	
		}
		if($(this).hasClass('nolabel')) {
			state += ' nolabel';
		}
		$(this).before('<p class="cf" style="font-size:.8em;"><a id="switch_'+id+'" class="switch_mce '+state+'" href="javascript: void(0);">')
		btn = $(this).prev().find('.switch_mce');
		if($(this).hasClass('nolabel')){
			btn.html('<span class="skip">'+label+'</span>');
		}else{
			btn.text(label);
		}
	});
	$(document).on('click', '.switch_mce', function(){
		var id, label;
		id = this.id.replace('switch_','');
		if( $(this).hasClass('on') ){
			$(this).removeClass('on').addClass('off');
			label = 'ビジュアルエディタに切替え';
			tinymce.EditorManager.execCommand('mceRemoveEditor', false, id);
		} else {
			$(this).removeClass('off').addClass('on');
			label = 'テキストエディタに切替え';
			tinymce.EditorManager.execCommand('mceAddEditor', true, id);
		}
		if($(this).hasClass('nolabel')){
			$(this).html('<span class="skip">'+label+'</span>');
		}else{
			$(this).text(label);
		}
	});

});