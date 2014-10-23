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
	$actions = $get_actionset($controller, $item);
	$html.='<div id="adminbar_context" class="clearfix">';
	$action_name = $title ? '：'.$title : '';
	$context_label = $controller_name.$action_name;
//	$context_label = $title;
	if(!$actions['index'] || count($actions['index']) == 1 ):
		$html.='<h3>'.$context_label.'</h3>'; //ツールバーのアンカーにも足す？
	else:
		$html.= '<div id="adminbar_index">';
		$html.= '<h3 class="listopen"><a href="javascript:void(0);" title="インデクスメニューを開く">'.$context_label.'</a></h3>';
		$html.= '<ul class="boxshadow modal">';
		foreach($actions['index'] as $url => $v):
			if( ! $url) continue;
			$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
			$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
			$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div>';
	endif;

		$html.= '<ul class="holizonal_list">';
		foreach($actions['control'] as $url => $v):
			if( ! $url) continue;
			$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
			$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
			$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div>';

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
		// IE8では画像のサイズをCSSで与えた場合、画像の本来のサイズで親要素が描画されてしまうので、明示的なサイズを持った要素で画像を囲む。
		$html.= '<ul class="boxshadow modal">';
		foreach($controller4menu as $v):
			if( ! $v['url']) continue;
			$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div><!-- /.admin_menulist -->';
	endif;
	$html.= '</div><!-- /.adminbar_main_left -->';
	
	$html.='<div class="adminbar_main_right">';

	//thx debe
	$root_prefix = $is_root ? '_root' : '' ;

	//user menu
	$html.= '<div id="adminbar_user">';
	$html.= '<a href="javascript:void(0);" class="listopen modal" title="ユーザメニューを開く:'.\User\Controller_User::$userinfo["display_name"].'でログインしています"><span class="adminbar-icon">'."<img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_user{$root_prefix}.png\" alt=\"\"></span>".\User\Controller_User::$userinfo["display_name"].'</a>';
	$html.= '<ul class="boxshadow modal">';
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
		$html.= '<ul class="boxshadow modal">';
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

