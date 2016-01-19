$(function() {

	var docroot = $('body').data('uri');

	function getBookmarks()
	{
		$.ajax({
			type: 'POST',
			url: docroot + 'bkmk/bookmarks',
			timeout: 10000,
			dataType: 'json',
			data: {
			},
		}).done(function(datas, st, xhr) {
			$('#bookmarks').empty();
			for (key in datas)
			{
				var name = datas[key].name;
				var url = datas[key].url;
				var elm = $('<li><a class="bookmark" href="' + url + '">' + name + '</a></li>');
				elm.appendTo('#bookmarks');
			}
		}).fail(function(xhr, st, err) {
		});
		if(!$('#admin_bookmark_name_input').val()) $('#admin_bookmark_name_input').val(document.title);
	}

	function addBookmark(e)
	{
//		e.preventDefault();
//		e.stopPropagation();

		var name = $('#admin_bookmark_name_input').val();
		var url = $('#admin_bookmark_url_input').val();

		if (!name) alert('ブックマーク名を入力して下さい');
		if (!url) alert('ブックマークURLが空です');

		$('#admin_bookmark_add_button').off();

		$.ajax({
			type: 'POST',
			url: docroot + 'bkmk/add',
			timeout: 10000,
			dataType: 'json',
			data: {
				name: name,
				url: url,
			},
		}).done(function(data, st, xhr) {
			getBookmarks();
			$('#admin_bookmark_add_button').on('click', addBookmark);
		}).fail(function(xhr, st, err) {
			alert('ブックマークの追加に失敗しました。');
			$('#admin_bookmark_add_button').on('click', addBookmark);
		});
	}

	$('#lcm_bookmark').on('click', function(e) {
//		e.preventDefault();
//		e.stopPropagation();

		if ($('#bookmark_window').css('display') == 'none')
		{
//			$('#bookmark_window').show();
			getBookmarks();
		}
		else
		{
//			$('#bookmark_window').hide();
		}
	});

	$('#admin_bookmark_add_button').on('click', addBookmark);

	$('#bookmark_window').hide(); // なくてもいい
});

