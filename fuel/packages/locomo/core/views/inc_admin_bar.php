<?php
//$is_user_logged_in
if($is_user_logged_in):
	//item
	$item = isset($item) ? $item : null;
	
	$html = '';
	$html.= '<div id="adminbar">';
	$html.= '<a href="#anchor_adminbar" class="skip show_if_focus" tabindex="1">ツールバーに移動</a>';
	$html.= '<h2 class="skip"><a id="anchor_adminbar" tabindex="-1">ツールバー</a></h2>';

//ツールバー下段
	//context menu
	$actions = \Actionset::get_menu(
		$controller,
		$realm = 'base',
		$item,
		$get_authed_url = true
	);
	if($actions):
		$html.='<div id="adminbar_context" class="clearfix">';
		$html.='<h3>コントローラ名<span class="skip">の操作</span></h3>'; //ツールバーのアンカーにも足す？
		$html.= '<ul>';
		foreach($actions as $url => $v):
			if( ! $url) continue;
			$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
			$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
			$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
		endforeach;
		$html.= '</ul>';

		//context menu2
		$actions = \Actionset::get_menu(
			$controller,
			$realm = 'option',
			$item,
			$get_authed_url = true
		);
		if($actions):
			$html.= '<ul>';
			foreach($actions as $url => $v):
				if( ! $url) continue;
				$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
				$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
				$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
			endforeach;
			$html.= '</ul>';
		endif;

		//context menu2
		$actions = \Actionset::get_menu(
			$controller,
			$realm = 'ctrl',
			$item,
			$get_authed_url = true
		);
		if($actions):
			$html.= '<ul>';
			foreach($actions as $url => $v):
				if( ! $url) continue;
				$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
				$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
				$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
			endforeach;
			$html.= '</ul>';
		endif;

		$html.= '</div>';
	endif;

//ツールバー上段
	$html.= '<div class="adminbar_main clearfix borderbox">' ; 
	$html.= "<img src=\"{$home_uri}content/fetch_view/images/parts/logo.png\" class=\"adminbar_logo\" >" ;
	$html.= '<div class="adminbar_main_left">';
	$html.= '<h3 class="skip">メインメニュー</h3>';
	//controller menu
	$controller4menu = $get_controllers();
	if($controller4menu):
		$html.= '<div id="admin_menu">';
		$html.= '<a href="javascript:void(0);" class="listopen" title="メニューを開く"><span class="adminbar-icon">'."<img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_menu.png\" alt=\"\">".'</span>メニュー</a>';
		// IE8では画像のサイズをCSSで与えた場合、画像の本来のサイズで包括要素が描画されてしまうので、明示的なサイズを持った要素で画像を囲む。
		$html.= '<ul class="boxshadow">';
		foreach($controller4menu as $v):
			if( ! $v['url']) continue;
			$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div><!-- /.admin_menulist -->';
	endif;

	//index menu
	$actions = \Actionset::get_menu(
		$controller,
		$realm = 'index',
		$item,
		$get_authed_url = true
	);
	if($actions):
		$html.= '<div id="adminbar_index">';
		$html.= '<a href="javascript:void(0);" class="listopen" title="インデクスメニューを開く">インデクス</a>';
		$html.= '<ul class="boxshadow">';
		foreach($actions as $url => $v):
			if( ! $url) continue;
			$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
			$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
			$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div>';
	endif;
	$html.= '</div><!-- /.adminbar_main_left -->';
	
	$html.='<div class="adminbar_main_right">';

	//thx debe
	$root_prefix = $is_root ? '_root' : '' ;

	//user menu
	$html.= '<div id="adminbar_user">';
	$html.= '<a href="javascript:void(0);" class="listopen modal" title="ユーザメニューを開く:'.\User\Controller_User::$userinfo["display_name"].'でログインしています"><span class="adminbar-icon">'."<img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_user{$root_prefix}.png\" alt=\"\"></span>".\User\Controller_User::$userinfo["display_name"].'</a>';
	$html.= '<ul class="boxshadow">';
	if( ! $is_admin):
		$html.= "<li><a href=\"{$home_uri}user/view/{$userinfo["user_id"]}\">ユーザ情報</a></li>";
	endif;
	$html.= "<li><a href=\"{$home_uri}user/logout\">ログアウト</a></li>";
	$html.= '</ul>';
	$html.= '</div>';

	//admin controller menu
	$controller4menu = $get_controllers($is_admin = true);
	if($controller4menu):
		$html.= '<div id="admin_controller">';
		$html.= "<a href=\"javascript:void(0);\" class=\"listopen icononly\" title=\"管理者メニューを開く\"><span class=\"adminbar-icon\"><img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_setting.png\" alt=\"管理者メニュー\"></span></a>";
		$html.= '<ul class="boxshadow">';
		foreach($controller4menu as $v):
			if( ! $v['url']) continue;
			$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div><!-- /.admin_menulist -->';
	endif;

	//help
	$html.= '<a id ="admin_help" href="" title="ヘルプ"><span class="adminbar-icon">'."<img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_help.png\" alt=\"ヘルプ\">".'</span></a>';
	
	//処理速度
	$html.= $is_admin ? '<span id="render_info">{exec_time}s  {mem_usage}mb</span>' : '';
	$html.= '</div><!-- /.adminbar_main_right -->';
	$html.= '</div><!-- /.adminbar_main -->';

	
	$html.= '</div><!-- /#adminbar -->';

	echo $html;
	
endif;
//$is_user_logged_in
?>



