$(function(){
// datepicker
(function(){
//複数のdateFormatを切り替えて使用している場合にyy-mm-dd形式以外の値が消えるのを抑止（エラーでの再表示時など）
if($('#form_repeat_kb')[0]){
	var $input = $('input.date , input[type=date]');
	var input_val = new Object;
	$input.each(function(index){
		input_val[index] = $(this).val();
	});
	setTimeout(function($input, input_val){
		if(typeof($input) == "undefined") return;
		$input.each(function(index){
		if($(this).is(':hidden') || input_val[index] == "0000-00-00") return;
			$(this).val(input_val[index]);
		});
	}, 0, $input, input_val);
}


$('input.date , input[type=date]').datepicker({dateFormat: "yy-mm-dd"});
//開始日と終了日
var jslcm_dates = $( '#form_start_date, #form_end_date' );
//日付選択時、繰り返しなしの区分での終了日補完
$( '#form_start_date, #form_end_date' ).datepicker('option', 'onSelect', function( selectedDate ) {
//		var option = this.id == 'form_start_date' ? 'minDate' : 'maxDate',
		inst = $(this).data('datepicker'),
		date = $.datepicker.parseDate(inst.settings.dateFormat || $.datepicker._defaults.dateFormat,
			selectedDate,inst.settings );
//		jslcm_dates.not(this).datepicker('option', option, date);
		if($("#form_repeat_kb")[0] && $("#form_repeat_kb").val() == 0){
			set_startdate_to_enddate(this);
			$(this).trigger('change');
		}
//		val_compare($('#form_start_date'), $('#form_end_date'));
	});



// デフォルトの設定
$.datepicker.setDefaults({
	firstDay         : 1,
	autoSize         : false,
	changeMonth      : true,
	changeYear       : true,
	hideIfNoPrevNext : true,
	showButtonPanel  : true,
	beforeShow: function(input, inst) {
		var dateFormat = 'yy-mm-dd';
		var stepMonths = 1;
		var currentDate = $(this).val().replace(/-/g, "/");
		var currentDateLen = (currentDate.replace(/\u002f/g, "")+"").length;

		if(currentDateLen==6){
			currentDate = currentDate+"/01";
		}else if(currentDateLen==4){
			currentDate = currentDate+"/01/01";
		}

		if(!$(input).hasClass('month') && !$(input).hasClass('year')){
			$(inst.dpDiv).removeClass('monthpicker yearpicker');
		}else if($(input).hasClass('month')){ // 年月選択
			$(inst.dpDiv).removeClass('yearpicker').addClass('monthpicker');
			dateFormat = 'yy-mm';
		}else{ // 年選択
			$(inst.dpDiv).removeClass('monthpicker').addClass('yearpicker');
			dateFormat = 'yy';
			var stepMonths = 12;
		}
		
		$(this).datepicker('option', 'dateFormat', dateFormat);
		$(this).datepicker('option', 'stepMonths', stepMonths);
		$(this).datepicker('option', 'defaultDate', new Date(currentDate));
		if(!currentDate) return;
		$(this).datepicker('setDate', new Date(currentDate));
	},
	onSelect : function(){
		$(this).trigger('change');
	},
	onChangeMonthYear: function(year, month){
		if(!$(this).hasClass('month') && !$(this).hasClass('year')) return;
		if($(this).hasClass('month')){
			month = ("0"+month).slice(-2); 
			$(this).val(year+'-'+month);
		}else{
			$(this).val(year);
		}
	},
	onClose: function(dateText, inst) {
		if($(this).val || !$(this).hasClass('month') && !$(this).hasClass('year')) return;
		
		var year = inst.selectedYear;
		if($(this).hasClass('month')){
			var month = ("0"+(inst.selectedMonth+1)).slice(-2);//1ずれるので？補正
			$(this).val(year+'-'+month);
		}else{
			$(this).val(year);
		}
		$(this).trigger('change');
//		$(this).datepicker('setDate', new Date(year, month, 1));
	},
});

// 開始日選択時に終了日補完
if($('#form_start_date')[0] && $('#form_end_date')[0]){
//	val_compare($('#form_start_date'), $('#form_end_date'));
	$('#form_start_date, #form_end_date').change(function(){
		set_startdate_to_enddate(this);
//		val_compare($('#form_start_date'), $('#form_end_date'));
	});
}
function set_startdate_to_enddate(elm){
	if(elm.id == 'form_start_date'){//はんていそとでやるほうがよい？
		$('#form_end_date').val($(elm).val());
	}
}

//日付＋時間は、入力欄がひとつなのでdatetimepickerを使用
//15分区切り
$('input.datetime.min15, input[type=datetime].min15').datetimepicker({
	timeFormat: 'HH:mm',
	stepMinute: 15
});

//30分区切り
$('input.datetime.min30, input[type=datetime].min30').datetimepicker({
	timeFormat: 'HH:mm',
	stepMinute: 30
});

//通常の日付＋時間選択
$('input.datetime,  input[type=datetime]').datetimepicker({
		firstDay: 1,
});

// jquery.timepicker 
// beforeRender, beforeShowを追加
	$('input.time').timepicker({
		timeFormat: 'H:i',
		beforeRender: function(self){
			if(isTouchDevice){ this.useSelect = true;}
		},
		beforeShow: function(){
		},
	});
})();//datepickerここまで

//入力された日付の整形

$('input.date , input[type=date]').each(function(){
	this.onchange = function(){
		$(this).val(format_datestr($(this).val()));
	}
});
function format_datestr(data){
	data += '';
	var table = {
	"０":0,
	"１":1,
	"２":2,
	"３":3,
	"４":4,
	"５":5,
	"６":6,
	"７":7,
	"８":8,
	"９":9,
	"ー":"-",
	"－":"-",
	"−":"-",
	"/":"-",
	"／":"-",
	"年":"-",
	"月":"-",
	"日":"",
	};
	while(data.match(/[０-９]/)){
		for(n in table){
			data = data.replace(n, table[n]);
		}
	}
	if(data.substr((data.length-1)) == "-") data = data.substr(0, (data.length-1));

	data = data.split('-');
	for( var i = 0, len = data.length;  i < len; i++ ){
		if(data[i].length == 1 ) data[i] = ("0"+ data[i]).slice(-2);
	}
	data = data.join('-');
	return data;
}

});
