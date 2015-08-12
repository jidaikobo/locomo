$(function () {

	function addNewElement(e)
	{
		if (e) e.preventDefault();
		$('#elements .element.focus').removeClass('focus');
		$('#controller_bar .add_element').removeClass('active');

		var new_element_class = 'new_element';
		var new_index = $('#elements .element.' + new_element_class + '').length;
		var replaced = $( $('#element_template').html().replace(/\$/g, new_index) );
		replaced.addClass(new_element_class);
		replaced.addClass('focus');

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

		$('#controller_name').val( elm.find('.name').val() );
		$('#controller_txt').val( elm.find('.txt').val() );
	}

	function valueInputController2Element()
	{
		if (! $('#elements .element.focus')[0]) return; // TODO throw error
		var elm = $('#elements .element.focus');

		elm.find('.name').val( $('#controller_name').val() );
		elm.find('.txt').val( $('#controller_txt').val() );
		applyStyle(elm);
	}


	function applyStyle(elm)
	{
		elm.find('.display_name').text(elm.find('.name').val());
		elm.find('.display_seq').text(elm.find('.seq').val());

		var txt = elm.find('.txt').val();
		elm.find('.txt').text(txt);
		rendered_text = txt.replace(/{(.*?)}/g, '<span class="field">$1</span>');
		rendered_text = rendered_text.replace(/\r\n/g, "<br>");
		rendered_text = rendered_text.replace(/(\n|\r)/g, "<br>");

		elm.find('.text_wrapper').html(rendered_text);
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
		if ($('#elements .element.focus')[0]) {
			$('#controller_name').closest('.input_group').show();
			$('#controller_txt').closest('.input_group').show();

			$('#model_properties .add_row').hide();
			$('#model_properties .add_txt').show();
		} else {
			$('#controller_name').closest('.input_group').hide();
			$('#controller_txt').closest('.input_group').hide();

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
			addNewElement();
			$('#elements .element.focus').find('.name').val(name);
			$('#elements .element.focus').find('.txt').val(txt);
			$('#controller_name').val(name);
			$('#controller_txt').val(txt);

			applyAllStyle();
		});

		$('#model_properties .add_txt').on('click', function(e) {
			var txt = '{' + $(e.target).data('field') + '}';
			$('#controller_txt').val( $('#controller_txt').val() + txt );
			valueInputController2Element()
			applyStyle($('#elements .element.focus'));
		});

	}

	init();
	displayController();
	applyAllStyle();

});
