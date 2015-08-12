$(function() {
	$('.select_format').on('change', setStartCell);

	function setStartCell(e) {
		// もう一方に入力
		$('.select_format').val($(this).val());
		
		if ( $(this).find('option:selected').hasClass('multiple') )
		{
			// value はイベントターゲットから取る
			var cells = parseInt($(this).find('option:selected').data('cells'));

			// 両方に適用
			$('select.start_cell').each(function() {
				$(this).empty();
				for (var i=1; i<=cells; i++)
				{
					$(this).append('<option value="' + i + '">' + i + '</option>');
				}
			});

			$('.pdf_start_cell').show();
		}
		else
		{
			// 両方に適用
			$('.pdf_start_cell').hide();
		}
	};


	$('select.start_cell').on('change', function(e) {
		// もう一方に入力
		$('select.start_cell').val($(this).val());
	});


	// init
	$('.select_format').change();

});

