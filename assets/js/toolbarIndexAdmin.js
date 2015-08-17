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

	$('select.print_repeat').on('change', function(e) {
		// もう一方に入力
		$('select.print_repeat').val($(this).val());
	});



	$('.index_toolbar input.print_all').on('click', function(e) {
		var print_count = parseInt($('.index_toolbar .count_total').val());
		if ($('select.print_repeat')[0]) print_count = print_count * parseInt($('select.print_repeat').val());
		if ( print_count > 1000)
		{
			if (!confirm("出力: " + print_count + "件\n" + 
					"出力件数が 1000 件を超えているため\n出力に時間がかかります\n(目安として 1000件あたり 30秒弱かかります)\n多すぎる場合は出力を停止する可能性があります"))
			{
				e.preventDefault();
			}
		}
	});

	// init
	$('.select_format').change();

});

