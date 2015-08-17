$(function () {

	var mm2px = 1.3; // 拡大縮小用に variable
	var px2mm = 1/mm2px;
	const pt2mm = 2.8346;
	const mm2pt = 1/pt2mm;

	$('form').on('change', applyAllStyle);

	
	$('.print_rotate').each(function() {

	});
	

	$('.print_size').each(function() {

	});

	function applyAllStyle()
	{
		$('#preview').css('width',  $('#form_w').val()*mm2px);
		$('#preview').css('height', $('#form_h').val()*mm2px);

		$('#print').css('top', $('#form_margin_top').val()*mm2px);
		$('#print').css('left', $('#form_margin_left').val()*mm2px);

		$('#form_h').val()*mm2px;
		var print_width = ($('#form_w').val() - $('#form_margin_left').val() - $('#form_margin_right').val() );
		var print_height = ($('#form_h').val() - $('#form_margin_top').val() - $('#form_margin_bottom').val() );
		$('#print').css('width', print_width*mm2px);
		$('#print').css('height', print_height*mm2px);

		if ( $('input[name="is_multiple"]:checked').val() == 1)
		{
			$('form .for_multiple').closest('.input_group').show();
			$('#print').empty();
			var start_top  = $('#form_margin_top').val();
			var start_left = $('#form_margin_left').val();

			var cols = $('#form_cols').val();
			var rows = $('#form_rows').val();
			console.log(print_width);
			console.log($('#form_space_horizontal').val()*(cols-1));
			var cell_width  = (print_width - $('#form_space_horizontal').val()*(cols-1)) / cols;
			var cell_height = (print_height - $('#form_space_vertical').val()*(rows-1)) / rows;
			for (var i = 0; i < rows; i++)
			{
				for (var j = 0; j < cols; j++) {
					var elm = $('<div class="element">印刷範囲</div>');
					elm.css('width',  cell_width*mm2px);
					elm.css('height', cell_height*mm2px);
					console.log((cell_width + parseInt($('#form_space_horizontal').val())));
					elm.css('left',   ( cell_width + parseInt($('#form_space_horizontal').val()) ) *j*mm2px);
					elm.css('top',    ( cell_height + parseInt($('#form_space_vertical').val()) ) *i*mm2px);
					elm.appendTo('#print');
				}
			}
			$('#form_cell_w').val(cell_width);
			$('#form_cell_h').val(cell_height);

			$('#form_rotation').closest('.input_group').hide();
			var rotation = $('#form_rotation').val(0);
			$('#preview').css('transform', 'rotate(0deg)');
		}
		else
		{
			$('form .for_multiple').closest('.input_group').hide();
			$('#print').empty();
			var elm = $('<div class="element">印刷範囲</div>');
			elm.css('width',  '100%');
			elm.css('height', '100%');
			elm.appendTo('#print');
			$('#form_rotation').closest('.input_group').show();
			var rotation = $('#form_rotation').val();
			$('#preview').css('transform', 'rotate(' + rotation + 'deg)');
		}
	}

	applyAllStyle();

});
