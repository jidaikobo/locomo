// Copyright (c) 2008 ichi (http://ichi.fool.jp/memo/)
// 
// Permission is hereby granted, free of charge, to any person obtaining
// a copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to
// permit persons to whom the Software is furnished to do so, subject to
// the following conditions:
// 
// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
// LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
(function($){
	
	$.fn.autoKana = function(target, config){
		config = $.extend({
			katakana: true
		}, config);
		
		var $this = this,
		    $target = $(target),
		    timer;
		
		var kana_extraction_pattern = new RegExp('[^ 　ぁあ-んー]', 'g'),
		    kana_compacting_pattern = new RegExp('[ぁぃぅぇぉっゃゅょ]', 'g');
		
		var baseKana     = '',
		    flagConvert  = false,
		    ignoreString = '',
		    input        = '',
		    values       = [];
		
		
		var _stateClear = function(){
			baseKana     = '';
			flagConvert  = false;
			ignoreString = '';
			input        = '';
			values       = [];
		};
		var _stateInput = function() {
			baseKana     = $target.val();
			flagConvert  = false;
			ignoreString = $this.val();
		};
		var _stateConvert = function() {
			baseKana     = baseKana + values.join('');
			flagConvert  = true;
			values       = [];
		};
		
		var _setInterval = function() {
			timer = setInterval(_checkValue, 30);
		};
		var _clearInterval = function() {
			clearInterval(timer);
		};
		
		var _checkConvert = function(new_values) {
			if(!flagConvert) {
				if(Math.abs(values.length - new_values.length) > 1) {
					var tmp_values = new_values.join('').replace(kana_compacting_pattern, '').split('');
					if(Math.abs(values.length - tmp_values.length) > 1) {
						_stateConvert();
					}
				} else {
					if(values.length == input.length && values.join('') != input) {
						_stateConvert();
					}
				}
			}
		};
		var _checkValue = function() {
			var new_input = $this.val(),
			    new_values;
			
			if(new_input == '') {
				_stateClear();
				_setKana();
			}else{
				new_input = _removeString(new_input);
				if(input == new_input) {
					return;
				}else{
					input = new_input;
					if(!flagConvert) {
						new_values = new_input.replace(kana_extraction_pattern, '').split('');
						_checkConvert(new_values);
						_setKana(new_values);
					}
				}
			}
		};
		
		var _setKana = function(new_values) {
			if(!flagConvert) {
				if(new_values) {
					values = new_values;
				}
				$target.val(_toKatakana(baseKana + values.join('')));
			}
		};
		
		var _removeString = function(new_input) {
			if(new_input.match(ignoreString)) {
				return new_input.replace(ignoreString, '');
			}else{
				var ignoreArray = ignoreString.split(''),
				    inputArray = new_input.split('');
				for(var i = 0, iL = ignoreArray.length; i <iL; i++){
					if(ignoreArray[i] == inputArray[i]) {
						inputArray[i] = '';
					}
				}
				return inputArray.join('');
			}
		};
		
		var _isHiragana = function(c) {
			return ((c >= 12353 && c <= 12435) || c == 12445 || c == 12446);
		};
		
		var _toKatakana = function(src) {
			if(config.katakana){
				var c,
				    str = new String;
				for(var i = 0, iL = src.length; i < iL; i++) {
					c = src.charCodeAt(i);
					if(_isHiragana(c)) {
						str += String.fromCharCode(c + 96);
					}else{
						str += src.charAt(i);
					}
				}
				return str;
			}else{
				return src;
			}
		};
		
		
		return $this
			.blur(function(e){
				_clearInterval();
			})
			.focus(function(e){
				_stateInput();
				_setInterval();
			})
			.keyup(function(e){
				if(flagConvert) {
					_stateInput();
				}
			});
	};
	
})(jQuery);


(function ($) {
	$.fn.magicpreview = function (str, options) {
		
		if (typeof str === "object" && typeof options === "undefined") // If function(options)
		{
			options = str;
			str = '';
		}
		
		str = str || '';
		options = $.extend({}, $.fn.magicpreview.options, options);
		
		var ev = $.fn.magicpreview.events, len = ev.length;
		
		function change(e, n, o, i, onload)
		{
			if (options.onBefore() && (options.onLoad || !onload))
			{
				var st = ev[i].f(e), orig_st = st;
				
				if (st !== false) 
				{
					st = st.replace(/\r|\n/mg, '<br />');
					st = options.formatValue(st);
				}
				else
				{
					st = o;
				}
				
				if (options.change === 'html') 
				{
					n.html(st);
				}
				else if (options.change === 'text') 
				{
					n.text(st);
				}
				else
				{
					n.attr(options.change, st);
				}
				
				options.onAfter(orig_st);
			}
			return;
		}
		
		return this.filter(':text, :radio, :checkbox, select, textarea').each(function () {
			var e = this, n, o, i;
			
			if (options.child !== false)
			{
				n = $('#' + str + $(e).attr('name')).find(options.child);
			}
			else
			{
				n = $('#' + str + $(e).attr('name'));
			}
			
			if (options.change === 'html') 
			{
				o = n.html();
			}
			else if (options.change === 'text') 
			{
				o = n.text();
			}
			else
			{
				o = n.attr(options.change);
			}
			
			for (i = 0; i < len; i++)
			{
				if ($(e).is(ev[i].on)) 
				{
					e.i = i; // Hack!
					
					if (typeof ev[i].e !== 'undefined')
					{
						for (j in ev[i].e)
						{
							if (ev[i].e[j] === 'load')
							{
								change(e, n, o, e.i, true);
							}
							else if (typeof ev[i].e[j] === 'string')
							{
								$(e).bind(ev[i].e[j], function () {
									change(this, n, o, e.i, false);
								});
							}
						}
					}
				}
			}
		});
	};
	
	$.fn.magicpreview.events = [{
			'on': ':text, textarea',
			'e': ['keyup', 'load'],
			'f': function (e) {
				return ($(e).val().replace(/\n|\r/mg, '') !== '') ? $(e).val() : false;
			}
		}, {
			'on': ':checkbox, :radio',
			'e': ['click', 'load'],
			'f': function (e) {
				return ($(e).is(':checked')) ? $(e).val() : false;
			}
		}, {
			'on': 'select',
			'e': ['change', 'load'],
			'f': function (e) {
				return ($(e).attr('value') !== '' || $(e).attr('value') !== 'undefined') ? $(e).attr('value') : false;
			}
		}];
		
	$.fn.magicpreview.options = {
		'child': false,
		'change': 'html',
		'onLoad': true,
		'onBefore': function () { 
			return true;
		},
		'onAfter': function (val) { 
			return true; 
		},
		'formatValue': function (val) { 
			return val; 
		}
	};

})(jQuery);

