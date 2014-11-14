console.log('customer/edit.js');

$(function() {
	var personal_group_tab = $('#personal_group_tab');
	var common_group_tab = $('#common_group_tab');

	$('input[name="user_type"]').on('change', function() {
		tab_change();
	});


	function tab_change() {
		var value = $('input[name="user_type"]:checked').val()
		if (value == '個人') {
			personal_group_tab.show();
			common_group_tab.hide();
		} else if (value == '団体等') {
			personal_group_tab.hide();
			common_group_tab.show();
		} else {
			alert('不正な値になっています');
		}
	}
	
	tab_change();
});
