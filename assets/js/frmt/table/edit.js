$(function () {

	var mm2px = 4; // 拡大縮小用に variable
	var px2mm = 1/mm2px;
	const pt2mm = 2.8346;
	const mm2pt = 1/pt2mm;


	// value が number になるべきクラス
	var classes_for_num = {
		w: '',
		font_size: '',
		min_h: '',
		padding_right: '',
		padding_left: '',
		padding_top: '',
		padding_bottom: '',
		is_merge: '',


		/*
		x: '',
		y: '',
		h: '',
		margin_top: '',
		*/
		// border_width: '',
	};
	// checkbox bool クラス
	var classes_for_checkbox = {
		/*
		ln_y: '',
		h_adjustable: '',
		border_left: '',
		border_top: '',
		border_right: '',
		border_bottom: '' ,
		*/
	};
	var classes_for_text = {
		name: '',
		'label': '',
		align: '',
		valign: '',
		font_family: '',
		txt: '',
		fitcell_type: '',
	};

	var display_toggle_controller_ids = [
		'#form_txt',
		'#form_name',
		'#form_label',
		'#form_w',
		'#form_font_size',
		'#form_font_family',
		'#form_align',
		'#form_valign',
		'#form_fitcell_type',

		'#form_min_h',
		'#form_padding_top',
		'#form_padding_left',
		'#form_padding_right',
		'#form_padding_bottom',
		'#form_is_merge',
	];


	function addNewElement(e)
	{
		if (e)
		{
			e.preventDefault();
		}

		$('#elements .element.focus').removeClass('focus');
		$('#controller_bar .add_element').removeClass('active');

		var new_element_class = 'new_element';
		var new_index = $('#elements .element.' + new_element_class + '').length;
		var replaced = $( $('#element_template').html().replace(/\$/g, new_index) );
		replaced.addClass(new_element_class);
		replaced.addClass('focus');

		if (e && $(e.target))
		{
			var label_name = $(e.target).closest('li').find('.label_name').text();
			if (replaced.find('.name').val()  == '') replaced.find('.name').val( label_name );
			if (replaced.find('.label').val() == '') replaced.find('.label').val( label_name );
		}

		replaced.appendTo($('#elements'));
		replaced.on('click', focusElementListener);

		valueInputElement2Controller();

		applyAllStyle(); 
	}

	// エレメントを選択した
	function focusElementListener(e)
	{
		$('#elements .element.focus').removeClass('focus');

		if ($(e.target).hasClass('element')) {
			var elm = $(e.target);
		} else {
			var elm = $(e.target).closest('#elements .element');
		}
		e.stopPropagation();

		// もしすでにフォーカスされていてコントローラーが非表示なら表示する
		if (elm.hasClass('focus')) $('#controllers_wrapper').show();
		$('#elements .element.focus').removeClass('focus');
		elm.addClass('focus');

		displayController();
		valueInputElement2Controller();
	}

	function valueInputElement2Controller()
	{
		if (! $('#elements .element.focus')[0]) return; // TODO throw error
		var elm = $('#elements .element.focus');

		for (var class_name in classes_for_text)
		{
			$('#controller .' + class_name).val( elm.find('.' + class_name).val() );
		}
		for (var class_name in classes_for_num)
		{
			var value = parseFloat( elm.find('.' + class_name).val() );
			if (isNaN(value) || value < 0) value = 0;
			$('#controller .' + class_name).val( value );
		}
		for (var class_name in classes_for_checkbox)
		{
			var value = parseFloat( elm.find('.' + class_name).val() );
			if (isNaN(value) || value < 0) value = 0;
			if (value) {
				$('#controller .' + class_name).prop('checked', true);
			} else {
				$('#controller .' + class_name).prop('checked', false);
			}
		}

		// TODO value の処理
		/*
		$('#controller_name').val( elm.find('.name').val() );
		$('#controller_txt').val( elm.find('.txt').val() );
		*/
	}

	function valueInputController2Element()
	{
		if (! $('#elements .element.focus')[0]) return; // TODO throw error
		var elm = $('#elements .element.focus');

		// タイトルを自動で付ける
		/*
		if ($('#controller .name').val() == '')
		{
			$('#controller .name').val( $('#controller .txt').val().substr(0, 10) );
		}
		*/

		for (var class_name in classes_for_text)
		{
			elm.find('.' + class_name).val( $('#controller .' + class_name).val() );
		}
		for (var class_name in classes_for_num)
		{
			var value = parseFloat( $('#controller .' + class_name).val() );
			if (isNaN(value) || value < 0) value = 0;
			elm.find('.' + class_name).val( value );
		}
		for (var class_name in classes_for_checkbox)
		{
			var value = $('#controller .' + class_name).is(':checked');
			if (value) {
				elm.find('.' + class_name).val( 1 );
			} else {
				elm.find('.' + class_name).val( 0 );
			}
		}


		/*
		elm.find('.name').val( $('#controller_name').val() );
		elm.find('.txt').val( $('#controller_txt').val() );
		*/
		applyStyle(elm);
	}


	function applyStyle(elm)
	{
		elm.find('.display_name').text(elm.find('.name').val());
		elm.find('.display_label').text(elm.find('.label').val());
		elm.find('.display_seq').text(elm.find('.seq').val());

		var txt = elm.find('.txt').val();
		elm.find('.txt').text(txt);
		rendered_text = txt.replace(/{(.*?)}/g, '<span class="field">$1</span>');
		rendered_text = rendered_text.replace(/\r\n/g, "<br>");
		rendered_text = rendered_text.replace(/(\n|\r)/g, "<br>");

		elm.find('.text').html(rendered_text);

		elm.css('width',         parseFloat(elm.find('.w').val())*mm2px);
		elm.find('.text').css('font-size', parseFloat(elm.find('.font_size').val())*mm2px*mm2pt);

		elm.find('.text_wrapper').css('paddingRight',  parseFloat(elm.find('.padding_right').val())*mm2px);
		elm.find('.text_wrapper').css('paddingLeft',   parseFloat(elm.find('.padding_left').val())*mm2px);
		elm.find('.text_wrapper').css('paddingTop',    parseFloat(elm.find('.padding_top').val())*mm2px);
		elm.find('.text_wrapper').css('paddingBottom', parseFloat(elm.find('.padding_bottom').val())*mm2px);

		switch (elm.find('.align').val())
		{
			case 'R':
				elm.find('.text').css('text-align', 'right');
				break;
			case 'C':
				elm.find('.text').css('text-align', 'center');
				break;
			case 'L':
			default :
				elm.find('.text').css('text-align', 'left');
				break;
		}
		switch (elm.find('.valign').val())
		{
			case 'B':
				elm.find('.text_wrapper').css('vertical-align', 'bottom');
				break;
			case 'M':
				elm.find('.text_wrapper').css('vertical-align', 'middle');
				break;
			case 'T':
			default :
				elm.find('.text_wrapper').css('vertical-align', 'top');
				break;
		}
		switch (elm.find('.font_family').val())
		{
			case 'G':
				elm.find('.text').removeClass('mincho');
				elm.find('.text').addClass('gothic');
				break;
			case 'M':
			default :
				elm.find('.text').removeClass('gothic');
				elm.find('.text').addClass('mincho');
				break;
		}

		if (parseInt(elm.find('.is_merge').val()))
		{
			elm.find('.text_wrapper').addClass('merge');
		}
		else
		{
			elm.find('.text_wrapper').removeClass('merge');
		}

	}

	function applyAllStyle()
	{
		var seq = 1;
		$('#elements .element').each(function() {
			$(this).find('.seq').val(seq);
			seq++;

			applyStyle($(this));
		});
	}

	function displayController()
	{
		if ($('#elements .element.focus')[0])
		{
			for (var i in display_toggle_controller_ids)
			{
				$(display_toggle_controller_ids[i]).closest('.input_group').show();
			}

			$('#model_properties .add_row').hide();
			$('#model_properties .add_txt').show();
		}
		else
		{
			for (var i in display_toggle_controller_ids)
			{
				$(display_toggle_controller_ids[i]).closest('.input_group').hide();
			}

			$('#model_properties .add_txt').hide();
			$('#model_properties .add_row').show();
		}
	}

	function init()
	{
		$('#elements').on('click', function() {
			$('#elements .element.focus').removeClass('focus');
		});

		$('#elements').sortable({update: applyAllStyle});

		$('#controllers_wrapper').on('change', valueInputController2Element);
		$('#elements .element').on('click', focusElementListener);

		$('#controllers_wrapper').draggable();
		$('#controllers_wrapper').resizable({handles: 'ne, se, sw, nw'});

		// 使用可能データの振る舞いを変える
		$('body').on('click', displayController);

		$('#controller_bar .add_element').on('click', addNewElement);

		$('#controller_bar .show_controller').on('click', function(e) {
			e.preventDefault()
			if ($('#controller_bar .show_controller').hasClass('active')) {
				$('#controller_bar .show_controller').removeClass('active');
				$('#controllers_wrapper').hide();
			} else {
				$('#controller_bar .show_controller').addClass('active');
				$('#controllers_wrapper').show();
			}
		});
		$('#controller_bar .show_controller').addClass('active');
		$('#controllers_wrapper').show();

		$('#model_properties .add_row').on('click', function(e) {
			var name = $(e.target).val();
			var txt = '{' + $(e.target).data('field') + '}';
			addNewElement(e);
			$('#elements .element.focus').find('.name').val(name);
			$('#elements .element.focus').find('.txt').val(txt);
			$('#controller_name').val(name);
			$('#form_txt').val(txt);

			applyAllStyle();
		});

		$('#model_properties .add_txt').on('click', function(e) {
			var txt = '{' + $(e.target).data('field') + '}';
			$('#form_txt').val( $('#form_txt').val() + txt );
			valueInputController2Element()
			applyStyle($('#elements .element.focus'));
		});


		$('#controller_bar .delete_element').on('click', function(e) {
			e.preventDefault()
			if ($('#elements .element.focus')[0]) {
				var name = $('#elements .element.focus').find('.name').val() || '選択した要素';
				if(window.confirm('[' + name + ']を削除します')) {
					$('#elements .element.focus').remove();
					applyAllStyle();
				}
			} else {
				alert('削除する要素を選択して下さい。');
			}
		});


		$('#controller_bar .show_display_name').on('click', function(e) {
			e.preventDefault();

			if ($('#controller_bar .show_display_name').hasClass('active')) {
				$('.element .display_name').hide();
				$('#controller_bar .show_display_name').removeClass('active')
			} else {
				$('.element .display_name').show();
				$('#controller_bar .show_display_name').addClass('active')
			}
		});
		$('#controller_bar .show_display_name').addClass('active')


	}

	init();
	displayController();
	applyAllStyle();

});

