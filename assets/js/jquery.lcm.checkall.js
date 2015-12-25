// checkbox 全選択・全選択解除
// input.checkbox_binded, .check_all, .uncheck_all, tr.has_checkbox
$(function() {
	var $checkboxes = $('.checkbox_binded');
	$('.check_all').on('click', function(e) {
		e.preventDefault();
		$checkboxes.prop('checked', true).trigger('change');
	});
	$('.uncheck_all').on('click', function(e) {
		e.preventDefault();
		$checkboxes.prop('checked', false).trigger('change');
	});

	$(document).on('click', '.has_checkbox tr' ,function(e){
		var $t, $tr, checkbox, prop;
		e = e ? e : event;
		$t = $(e.target);
		$tr = $($t.closest('tr'));

		if(!$t || ($t.is('label'))){
			e.preventDefault();
		}
		if($t.is('input') || $t.is('a')){
			return;
		}

		checkbox = $tr.find('.checkbox_binded');
		if(!$(checkbox).prop('checked')){
			prop = true; 
		}else{
			prop = false;
		}
		$(checkbox).prop('checked', prop).trigger('change');
	});

	function set_class(){
		var t = $.isWindow(this) ? $('.checkbox_binded:checked') : this;
		if(!$.isWindow(this)){
			if($(this).prop('checked')){
				$(this).closest('tr').addClass('checked');
			}else{
				$(this).closest('tr').removeClass('checked');
			}
		}else{
			$(document).find('tr').has($('input[type="checkbox"]:checked')).addClass('checked');
		}
	}
	set_class();
	$checkboxes.change(set_class);
});
