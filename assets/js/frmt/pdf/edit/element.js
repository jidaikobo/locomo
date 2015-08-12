$(function () {

	var mm2px = 4; // 拡大縮小用に variable
	var px2mm = 1/mm2px;
	const pt2mm = 2.8346;
	const mm2pt = 1/pt2mm;

	// TODO JSON で渡す
	// value が text になるべきクラス
	var classes_for_text = {
		name: '',
		align: '',
		valign: '',
		font_family: '',
		txt: '',
	};
	// value が number になるべきクラス
	var classes_for_num = {
		x: '',
		y: '',
		w: '',
		h: '',
		padding_right: '',
		padding_left: '',
		padding_top: '',
		padding_bottom: '',
		margin_top: '',
		font_size: '',
		// border_width: '',
	};
	// checkbox bool クラス
	var classes_for_checkbox = {
		ln_y: '',
		h_adjustable: '',
		border_left: '',
		border_top: '',
		border_right: '',
		border_bottom: '' ,
	};

	var apply_callbacks = [
		function () {
			// console.log('callback1');
		},
		function () {
			// console.log('callback2');
		},
	];



	// 用紙をクリックした
	// 選択の解除
	function printClickListener(e)
	{
		applyAllStyle();
		$('.print .element.focus').removeClass('focus');

		$('#controller').find('input[type="text"]').val('');
		$('#controller').find('textarea').val('');
		$('#controller').find('input[type="checkbox"]').prop('checked', false);
		$('#controller').find('select').val('');

		// 新しい要素の追加
		if ($('#controller_bar .add_element').hasClass('active')) {
			addNewElement(e);
		}
	}

	function addNewElement(e)
	{

		$('#controller_bar .add_element').removeClass('active');

		var new_element_class = 'new_element';
		var new_index = $('.print .element.' + new_element_class + '').length;
		var replaced = $( $('#element_template').html().replace(/\$/g, new_index) );
		replaced.addClass(new_element_class);
		replaced.find('.x').val(e.offsetX*px2mm);
		replaced.find('.y').val(e.offsetY*px2mm);

		replaced.find('.display_name').show();
		if ($('#controller_bar .show_shade').hasClass('active')) replaced.addClass('shade');

		replaced.appendTo($('#print_div'));
		replaced.on('click', focusElementListener);

		refleshElementSeq();
		sortedCallback(); // ->applyAllStyle() 走る 
	}

	// エレメントを選択した
	function focusElementListener(e)
	{
		if ($(e.target).hasClass('element')) {
			var elm = $(e.target);
		} else {
			var elm = $(e.target).closest('.print .element');
		}
		e.stopPropagation();

		// もしすでにフォーカスされていてコントローラーが非表示なら表示する
		if (elm.hasClass('focus')) $('#controllers_wrapper').show();
		$('.print .element.focus').removeClass('focus');
		elm.addClass('focus');

		valueInputElement2Controller();
	}

	function valueInputElement2Controller()
	{
		if (! $('.print .element.focus')[0]) return; // TODO throw error
		var elm = $('.print .element.focus');

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

		applyStyle(elm);
	}

	function valueInputController2Element()
	{
		if (! $('.print .element.focus')[0]) return; // TODO throw error
		var elm = $('.print .element.focus');

		// タイトルを自動で付ける
		if ($('#controller .name').val() == '')
		{
			$('#controller .name').val( $('#controller .txt').val().substr(0, 10) );
		}

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

		applyStyle(elm);
	}


	/*
	 * スタイルの適用
	 */
	function applyStyle(elm)
	{
		elm.find('.display_name').text(elm.find('.name').val());


		if (elm.find('.ln_y').val() == 1) {
			before_id = $('#element_seq').find('.' + $(elm).attr('id')).prev().find('.id').val();
			if (before_id) {
				var before_elm = $('#' + before_id);
				if (before_elm) {
					elm.css('top',   before_elm.position().top + before_elm.outerHeight() + parseInt(before_elm.css('marginTop')));
				}
			} else { // 直前の要素がない
				// alert ('直前の要素がないため y=0 で適用します。');
				elm.css('top',  0);
			}
		} else {
			elm.css('top',   parseFloat(elm.find('.y').val())*mm2px);
		}

		if (elm.find('.h_adjustable').val() == 1) {
			elm.addClass('h_adjustable');
		} else {
			elm.removeClass('h_adjustable');
		}
		if (elm.find('.ln_y').val() == 1) {
			elm.addClass('ln_y');
		} else {
			elm.removeClass('ln_y');
		}

		elm.css('left',   parseFloat(elm.find('.x').val())*mm2px);
		elm.css('width',  parseFloat(elm.find('.w').val())*mm2px);
		elm.css('maxWidth',  parseFloat(elm.find('.w').val())*mm2px);
		elm.css('height', parseFloat(elm.find('.h').val())*mm2px);
		elm.css('maxHeight', parseFloat(elm.find('.h').val())*mm2px);
		elm.css('paddingRight',  parseFloat(elm.find('.padding_right').val())*mm2px);
		elm.css('paddingLeft',   parseFloat(elm.find('.padding_left').val())*mm2px);
		elm.css('paddingTop',    parseFloat(elm.find('.padding_top').val())*mm2px);
		elm.css('paddingBottom', parseFloat(elm.find('.padding_bottom').val())*mm2px);
		elm.css('marginTop',    parseFloat(elm.find('.margin_top').val())*mm2px);
		elm.find('.text').css('font-size', parseFloat(elm.find('.font_size').val())*mm2px*mm2pt);

		elm.find('.text').css('maxHeight', parseFloat(elm.find('.h').val() - elm.find('.padding_top').val() - elm.find('.padding_bottom').val())*mm2px);

		// var border_width = parseFloat(elm.find('.border_width').val())*mm2px*mm2pt;
		border_width = 1;
		if (border_width > 0) {
			elm.find('.border_right').val()>0  ? elm.css('borderRight', 'solid '+border_width+'px #000')  : elm.css('borderRight', 'none');
			elm.find('.border_left').val()>0   ? elm.css('borderLeft', 'solid '+border_width+'px #000')   : elm.css('borderLeft', 'none');
			elm.find('.border_top').val()>0    ? elm.css('borderTop', 'solid '+border_width+'px #000')    : elm.css('borderTop', 'none');
			elm.find('.border_bottom').val()>0 ? elm.css('borderBottom', 'solid '+border_width+'px #000') : elm.css('borderBottom', 'none');
		}

		var txt = elm.find('.txt').val();
		elm.find('.txt').text(txt);
		rendered_text = txt.replace(/{(.*?)}/g, '<span class="field">$1</span>');
		rendered_text = rendered_text.replace(/\r\n/g, "<br>");
		rendered_text = rendered_text.replace(/(\n|\r)/g, "<br>");

		elm.find('.text').html(rendered_text);

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

		// element_seq に名前を適用
		$('#element_seq .' + elm.attr('id')).find('.display_name').text( elm.find('.name').val() );

		if (apply_callbacks[0])
		{
			for (key in apply_callbacks)
			{
				apply_callbacks[key](elm);
			}
		}
	}

	/*
	 * 並べ替えや上位要素の考慮
	 */
	function applySortLn()
	{
	}

	/*
	 * sort の作成
	 */
	function refleshElementSeq()
	{
		var elements = {};
		$('#element_seq').empty();
		var seq = 1;
		$('.print .element').each(function() {
			var id = this.id;
			$('#element_seq').append('' + 
					'<li class=' + id + '>' +
					'<span class="seq">' + seq + '</span>' +
					'<span class="display_name">element</span>' +
					'<input type="hidden" class="id            btn small" value="' + id + '">' +
					'<input type="button" class="focus_delete  btn small" value="削除">' +
					'<input type="button" class="focus_element btn small" value="フォーカス">' +
					'</li>');
			seq++;
		});

		$( "#element_seq" ).sortable({ update: sortedCallback });

		addElementSeqListener();
	}

	function addElementSeqListener()
	{
		$('#element_seq li').each(function () {
			var id = $(this).find('.id').val();

			$(this).find('.focus_element').on('click', function(e) {
				e.preventDefault()
				var elm = $('#' + id);

				// もしすでにフォーカスされていてコントローラーが非表示なら表示する
				if (elm.hasClass('focus')) $('#controllers_wrapper').show();
				$('.print .element.focus').removeClass('focus');
				elm.addClass('focus');

				valueInputElement2Controller();
			});

			$(this).find('.focus_delete').on('click', function(e) {
				e.preventDefault()
				var elm = $('#' + id);

				if (elm) {
					var name = elm.find('.name').val() || '選択した要素';
					if(window.confirm('[' + name + ']を削除します')) {
						elm.remove();
						refleshElementSeq();
						applyAllStyle();
					}
				} else {
					alert('削除する要素を選択して下さい。');
				}
			});
		});
	}

	function sortedCallback()
	{
		var seq = 0;
		$('#element_seq li').each(function () {
			var id = $(this).find('.id').val();
			$('#' + id).find('.seq').val(seq);
			seq++;
			$(this).find('.seq').text(seq);
			$('#' + id).appendTo('#print_div');
		});
		applyAllStyle();
	}

	function inputModelProperty(e)
	{
		var field = $(e.target).data('field');
		$('#controller .txt').val( $('#controller .txt').val() + '{' + field + '}');
		if ($('.print .element.focus')[0]) valueInputController2Element();
	}


	function applyAllStyle()
	{
		$('.print').css('width' , $('#print_width').val()*mm2px );
		$('.print').css('height', $('#print_height').val()*mm2px );
		$('.print .element').each(function () {
			applyStyle($(this));
		});
		$('#print_wrapper').css('height', $('.print').height() + 100);
	}

	function init()
	{
		// jQuertyUI
		$('#controllers_wrapper').draggable();
		$('#controllers_wrapper').resizable({handles: 'ne, se, sw, nw'});
		$('#element_seq_wrapper').draggable();
		$('#element_seq_wrapper').resizable({handles: 'ne, se, sw, nw'});

		// Handlers
		$('.print').on('click', printClickListener);
		$('#model_properties button').on('click', inputModelProperty);
		$('#controller').on('change', valueInputController2Element);
		$('.print .element').on('click', focusElementListener);
		// Handlers inline
		$('#controller_bar .zoom_in').on('click', function(e) {
			e.preventDefault()
			mm2px = Math.min(mm2px + 1, 10);
			px2mm = 1/mm2px;
			applyAllStyle();
		});
		$('#controller_bar .zoom_out').on('click', function(e) {
			e.preventDefault();
			mm2px = Math.max(mm2px - 1, 0.5);
			px2mm = 1/mm2px;
			applyAllStyle();
		});
		$('#controller_bar .zoom_reset').on('click', function(e) {
			e.preventDefault();
			mm2px = 4;
			px2mm = 1/mm2px;
			applyAllStyle();
		});

		$('#controller_bar .show_shade').on('click', function(e) {
			e.preventDefault();
			if ($('#controller_bar .show_shade').hasClass('active')) {
				$('#controller_bar .show_shade').removeClass('active')
				$('.print .element').removeClass('shade');
			} else {
				$('.print .element').addClass('shade');
				$('#controller_bar .show_shade').addClass('active')
			}
		});
		$('.print .element').addClass('shade');
		$('#controller_bar .show_shade').addClass('active');

		$('#controller_bar .show_display_name').on('click', function(e) {
			e.preventDefault();

			if ($('#controller_bar .show_display_name').hasClass('active')) {
				$('.print .display_name').hide();
				$('#controller_bar .show_display_name').removeClass('active')
			} else {
				$('.print .display_name').show();
				$('#controller_bar .show_display_name').addClass('active')
			}
		});
		$('#controller_bar .show_display_name').addClass('active')

		$('#controller_bar .show_text').on('click', function(e) {
			e.preventDefault();

			if ($('#controller_bar .show_text').hasClass('active')) {
				$('.print .text').hide();
				$('#controller_bar .show_text').removeClass('active')
			} else {
				$('.print .text').show();
				$('#controller_bar .show_text').addClass('active')
			}
		});
		$('#controller_bar .show_text').addClass('active')


		$('#controller_bar .add_element').on('click', function(e) {
			e.preventDefault()
			if ($('#controller_bar .add_element').hasClass('active')) {
				$('#controller_bar .add_element').removeClass('active');
			} else {
				$('#controller_bar .add_element').addClass('active');
			}
		});

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
		$('#controller_bar .show_element_seq').on('click', function(e) {
			e.preventDefault()
			if ($('#controller_bar .show_element_seq').hasClass('active')) {
				$('#controller_bar .show_element_seq').removeClass('active');
				$('#element_seq_wrapper').hide();
			} else {
				$('#controller_bar .show_element_seq').addClass('active');
				$('#element_seq_wrapper').show();
			}
		});
		$('#controller_bar .show_element_seq').addClass('active');
		$('#element_seq_wrapper').show();

		$('#controller_bar .centering_element').on('click', function(e) {
			e.preventDefault()
			if ($('.print .element.focus')[0]) {
				var name = $('.print .element.focus').find('.name').val() || '選択した要素';
				// if(window.confirm('[' + name + ']を中央配置します')) {
					var left = ( $('#print_width').val() - $('.print .element.focus').find('.w').val() ) / 2;
					$('.print .element.focus').find('.x').val(left);
					$('#controller .x').val(left);
					applyStyle($('.print .element.focus'));
				// }
			} else {
				alert('中央配置する要素を選択して下さい。');
			}
		});
		$('#controller_bar .left_align_element').on('click', function(e) {
			e.preventDefault()
			if ($('.print .element.focus')[0]) {
				var name = $('.print .element.focus').find('.name').val() || '選択した要素';
				// if(window.confirm('[' + name + ']を中央配置します')) {
					$('.print .element.focus').find('.x').val(0);
					$('#controller .x').val(0);
					applyStyle($('.print .element.focus'));
				// }
			} else {
				alert('左寄せする要素を選択して下さい。');
			}
		});
		$('#controller_bar .right_align_element').on('click', function(e) {
			e.preventDefault()
			if ($('.print .element.focus')[0]) {
				var name = $('.print .element.focus').find('.name').val() || '選択した要素';
				// if(window.confirm('[' + name + ']を中央配置します')) {
					var left = $('#print_width').val() - $('.print .element.focus').find('.w').val();
					$('.print .element.focus').find('.x').val(left);
					$('#controller .x').val(left);
					applyStyle($('.print .element.focus'));
				// }
			} else {
				alert('右寄せする要素を選択して下さい。');
			}
		});

		$('#controller_bar .delete_element').on('click', function(e) {
			e.preventDefault()
			if ($('.print .element.focus')[0]) {
				var name = $('.print .element.focus').find('.name').val() || '選択した要素';
				if(window.confirm('[' + name + ']を削除します')) {
					$('.print .element.focus').remove();
					refleshElementSeq();
					applyAllStyle();
				}
			} else {
				alert('削除する要素を選択して下さい。');
			}
		});

		$('#controller_bar .save').on('click', function(e) {
			// 保存する前にスタイル適用
			applyAllStyle();
		});

		$('#controllers_wrapper').on('click', function() {
			// 重なりの制御
			$('#controllers_wrapper').css('zIndex', 11);
			$('#element_seq_wrapper').css('zIndex', 10);
		});
		$('#element_seq_wrapper').on('click', function() {
			// 重なりの制御
			$('#controllers_wrapper').css('zIndex', 10);
			$('#element_seq_wrapper').css('zIndex', 11);
		});

		// $('#controllers_wrapper').hide();
		// css
		refleshElementSeq();
		applyAllStyle();
	}



	init();

});
