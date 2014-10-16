<?php
//$is_user_logged_in
if($is_user_logged_in):
	//item
	$item = isset($item) ? $item : array();
	$item = isset($items) ? $items : $item;
	
	$html = '';
	$html.= '<div id="adminbar">';
	$html.= '<a href="#anchor_adminbar" class="skip show_if_focus" tabindex="1">ツールバーに移動</a>';
	$html.= '<a id="anchor_adminbar" class="skip" tabindex="0">ツールバー</a>';

//ツールバー下段
	//context menu
	$actions = $get_actionset($controller, $item);
	if($actions['control']):
		$html.='<div id="adminbar_context" class="clearfix">';
		$html.='<h2>コントローラ名</h2>'; //ツールバーのアンカーにも足す？
		$html.= '<ul>';
		foreach($actions['control'] as $url => $v):
			if( ! $url) continue;
			$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
			$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
			$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '<p class="context_info">事業所名？</p>';
		$html.= '</div>';
	endif;

//ツールバー上段
	$html.= '<div class="adminbar_main clearfix borderbox">' ; 
	$html.= "<img src=\"{$home_uri}content/fetch_view/images/parts/logo.png\" class=\"adminbar_logo\" >" ;
	$html.= '<div class="adminbar_main_left">';
	//controller menu
	$controller4menu = $get_controllers();
	if($controller4menu):
		$html.= '<div id="admin_menu">';
		$html.= '<h2><a href="javascript:void(0);" class="listopen">■ メニュー</a></h2>';
		$html.= '<ul class="boxshadow">';
		foreach($controller4menu as $v):
			if( ! $v['url']) continue;
			$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div><!-- /.admin_menulist -->';
	endif;

	//index menu
	if($actions['index']):
		$html.= '<div id="adminbar_index">';
		$html.= '<h2><a href="javascript:void(0);" class="listopen">インデクス</a></h2>';
		$html.= '<ul class="boxshadow">';
		foreach($actions['index'] as $url => $v):
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
	//user menu
	$html.= '<div id="adminbar_user">';
	$html.= '<h2><a href="javascript:void(0);" class="listopen">▲ '.\User\Controller_User::$userinfo["display_name"].'</a></h2>';
	$html.= '<ul class="boxshadow">';
	$html.= '<li><a href="">ユーザ設定</a></li>';
	$html.= "<li><a href=\"{$home_uri}user/logout\">ログアウト</a></li>";
	$html.= '</ul>';
	$html.= '</div>';

	//admin controller menu
	$controller4menu = $get_controllers($is_admin = true);
	if($controller4menu):
		$html.= '<div id="admin_controller">';
		$html.= '<h2><a href="javascript:void(0);" class="listopen" title="管理者メニュー">●(管理者メニュー)</a></h2>';
		$html.= '<ul class="boxshadow">';
		foreach($controller4menu as $v):
			if( ! $v['url']) continue;
			$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div><!-- /.admin_menulist -->';
	endif;

	//help
	$html.= '<a id ="admin_help" href="">？</a>';
	
	//処理速度
	$html.= $is_admin ? '<span id="render_info">{exec_time}s  {mem_usage}mb</span>' : '';
	$html.= '</div><!-- /.adminbar_main_right -->';
	$html.= '</div><!-- /.adminbar_main -->';

	
	$html.= '</div><!-- /#adminbar -->';

	echo $html;
	
endif;
//$is_user_logged_in
?>



