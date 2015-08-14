(function TabularControl(jquery) {

	// jQuery の prototype
	jQuery.fn.tabularControl = function(prop) {

		if (this.length == 1) {
			this.tabularControl = new Fn($(this), prop);
			this.tabularControl.eventBindAll(this.tabularControl);
			this.tabularControl.calculateTabular(this.tabularControl);
		} else if (this.length > 1) {
			$(this).each(function() {
				this.tabularControl = new Fn($(this), prop);
				this.tabularControl.eventBindAll(this.tabularControl);
				this.tabularControl.calculateTabular(this.tabularControl);
			});
		} else {
			throw new Error( 'TabularControl need jquery object' );
		}

		//return fn;
	}


	// コンストラクタ
	function Fn(wrap, prop) {

		// configで書いたデフォルトを設定する
		this.wrap = wrap;
		this.model              = prop['model']            || '';
		this.row_class          = prop['row_class']        || '';
		this.add_row            = prop['add_row']          || '';
		this.remove_row_class   = prop['remove_row_class'] || '';
		this.up_row_class       = prop['up_row_class']     || '';
		this.down_row_class     = prop['down_row_class']   || '';
		this.renumber_classes   = prop['renumber_classes'] || [];
		this.row_template       = prop['row_template']     || "";
		this.calculate          = prop['calculate']        || 'price * quantity';
		this.row_total          = prop['row_total']        || '';
		this.calculate_total    = prop['calculate_total']  || '';
		this.calculate_callback = prop['calculate_callback'] || false;
		this.add_row_callback   = prop['add_row_callback'] || false;


		if (!this.model) { throw new Error( 'undefined "model"' ); }

		this.eventBindAll = function(tb) {

			var wrap = $(tb.wrap);
			// rows
			var rows = /^\./.test(tb.row_class) ? wrap.find(tb.row_class) : wrap.find('.' + tb.row_class);
			// calculate
			var matches = tb.calculate.match(/([\.\w]+)/g);
			rows.each(function() {
				$(this).closest(tb.row_class).find(/^\./.test(tb.up_row_class) ? tb.up_row_class : '.' + tb.up_row_class)
					.on('click', {tb:tb}, tb.upRow);
				$(this).closest(tb.row_class).find(/^\./.test(tb.down_row_class) ? tb.down_row_class : '.' + tb.down_row_class)
					.on('click', {tb:tb}, tb.downRow);
				$(this).closest(tb.row_class).find(/^\./.test(tb.remove_row_class) ? tb.remove_row_class : '.' + tb.remove_row_class)
					.on('click', {tb:tb}, tb.removeRow);
				// bind calculate
				for (key in matches) {
					var cls = matches[key];
					cls = /^\./.test(cls) ? cls : '.' + cls;
					$(this).closest(tb.row_class).find(cls).on('change', {tb:tb}, tb.calculateTabular);
				}
			});
			// addRow
			wrap.find(tb.add_row).on('click', {tb:tb}, tb.addRow);

		}

	}

	// jQuery.fn.tabularControl.Proto // 拡張可能?

	Fn.prototype = {
		upRow: function(e) {
			var tb = e.data.tb;
			var row_class = /^\./.test(tb.row_class) ? tb.row_class : '.' + tb.row_class;
			$(e.target).parents(row_class)
			.after($(e.target).parents(row_class).prev(row_class));
			tb.calculateTabular(tb);
		},
		downRow: function(e) {
			tb = e.data.tb;
			var row_class = /^\./.test(tb.row_class) ? tb.row_class : '.' + tb.row_class;
			$(e.target).parents(row_class)
			.before($(e.target).parents(row_class).next(row_class));
			tb.calculateTabular(tb);
		},


		removeRow: function(e) {
			if (window.confirm('消去します、よろしいですか')) {
				var tb = e.data.tb;
				if (tb.wrap.find(tb.row_class).length <= 1) tb.addRow(tb);
				$(e.target).parents(tb.row_class).remove();
				tb.calculateTabular(tb);
			}
		},

		addRow: function(e) {
			var tb = e.target ? e.data.tb : e;

			var new_row = (tb.wrap.find(tb.row_class)).length + 1;
			// var clone = tb.wrap.find(tb.row_class + ':last-child').clone(true);
			var clone = $(tb.wrap.find(tb.row_class)[tb.wrap.find(tb.row_class).length - 1]).clone(true);
			// selected 解除
			clone.find('option:selected').attr('selected', false);

			// 値をリセット
			clone.find('input').each(function() {
				var ng_arr = ['hidden', 'button', 'submit'];
				if (-1 === $.inArray($(this).attr('type'), ng_arr)) {
					$(this).val('');
					$(this).prop('checked', false);
				}

			clone.find('textarea').val('');

				// todo 30分刻みとかあるので、case に
				if ($(this).hasClass('date')) {
					$(this)
						.attr('id', '')
						.removeClass('hasDatepicker')
						.datepicker({
							dateFormat: 'yy-mm-dd',
							changeMonth: true,
							changeYear: true,
							showButtonPanel: true,
						});
				} else if ($(this).hasClass('time')) {
					$(this)
						.attr('id', '')
						.removeClass('hasDatepicker')
						.timepicker({
							timeFormat: 'HH:mm',
							beforeShow: function(){
								if( $(this).attr('readonly') ) return;
							}
						});
				} else if ($(this).hasClass('datetime')) {
					$(this)
						.attr('id', '')
						.removeClass('hasDatepicker')
						.datetimepicker({
							firstDay       : 1,
						});
				}


			});
			// tb.wrap.find(tb.row_class + ':last-child').after(clone);
			$(tb.wrap.find(tb.row_class)[tb.wrap.find(tb.row_class).length - 1]).after(clone);

			tb.calculateTabular(tb);

			if (typeof tb.add_row_callback === 'function') {
				tb.add_row_callback(clone, tb);
				tb.calculateTabular(tb);
			}

		},

		calculateTabular: function(e) {
			var tb = e.target ? e.data.tb : e;
			var wrap = tb.wrap;
			var cal_str = tb.calculate;
			var matches = cal_str.match(/([\.\w]+)/g);

			for (key in matches) {
				var cls = matches[key];
				matches[key] = /^\./.test(cls) ? cls : '.' + cls;
				cal_str = cal_str.replace(new RegExp(cls) , '$(this).find("' + matches[key] + '").val()')
			}


			var row_total = 0;
			wrap.find(tb.row_class).each(function() {
				for (key in matches) {
					if ($(this).find(matches[key]).val() == '') $(this).find(matches[key]).val(0);
				}
				var evaled = parseInt(eval(cal_str));
				row_total += evaled;
				if ($(this).find(tb.row_total)[0]) $(this).find(tb.row_total).val( evaled );
			});

			// 合計
			if (wrap.find(tb.calculate_total)[0]) {
				var cal_total = 0;
				if (wrap.find(tb.row_class).find(tb.row_total)[0]) {
					wrap.find(tb.row_class).each(function() {
						cal_total += parseInt($(this).find(tb.row_total).val());
					});
				} else {
					cal_total = row_total;
				}
				wrap.find(tb.calculate_total).val(cal_total);
			} else {
				cal_total = row_total;
			}


			if (typeof tb.calculate_callback === 'function') {
				tb.calculate_callback(cal_total, tb);
			}

			tb.renumberRow(tb);

		},
		// 列番号のふり直し
		renumberRow: function () {
			var tb = this;
			var i = 0;
			var classes = tb.renumber_classes;
			var wrap = tb.wrap;
			var display_class = 'inline';
			var row_class = /^\./.test(tb.row_class) ? tb.row_class : '.' + tb.row_class;
			var up_row_class = /^\./.test(tb.up_row_class) ? tb.row_class : '.' + tb.up_row_class;
			var down_row_class = /^\./.test(tb.down_row_class) ? tb.down_row_class : '.' + tb.down_row_class;

			$(wrap.find(row_class)).each(function() {
				// up down button display
				if (i == 0) {
					$(this).find(up_row_class).css('display', 'none');
					$(this).find(down_row_class).css('display', display_class);
					display_class = $(this).find(down_row_class).css('display');
				} else if (i == wrap.find(row_class).length-1) {
					$(this).find(up_row_class).css('display', display_class);
					$(this).find(down_row_class).css('display', 'none');// $(this).find(tb.down_row_class).css('display') );
				} else {
					$(this).find(up_row_class).css('display', display_class);
					$(this).find(down_row_class).css('display', display_class);
				}

				// renumber row
				for (var j = 0; j < classes.length; j++) {
					$(this).find('.' + classes[j]).attr('name', tb.model + '_new[' + i + '][' + classes[j] + ']');
				}
				i++;

			});

		}


 
	}


})(jQuery);

