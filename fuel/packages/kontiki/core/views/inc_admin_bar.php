<?php
//$is_user_logged_in
if($is_user_logged_in):
	//item
	$item = isset($item) ? $item : array();
	$item = isset($items) ? $items : $item;
	
	$html = '';
	$html.= '<div id="admin_bar">';

	//controller menu
	$controller4menu = $get_controllers();
	if($controller4menu):
		$html.= '<div id="admin_bar_controller">';
		$html.= '<h2>コントローラメニュー</h2>';
		$html.= '<ul>';
		foreach($controller4menu as $v):
			if( ! $v['url']) continue;
			$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div>';
	endif;

	//context menu
	$actions = $get_actionset($controller, $item);
	if($actions['index'] || $actions['control']):
		$html.= '<div id="admin_bar_context">';
		$html.= '<h2>コンテクストメニュー</h2>';
	endif;
	if($actions['index']):
		$html.= '<h3>各種インデクス</h3>';
		$html.= '<ul>';
		foreach($actions['index'] as $url => $v):
			if( ! $url) continue;
			$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
			$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
			$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
		endforeach;
		$html.= '</ul>';
	endif;
	if($actions['control']):
		$html.= '<h3>各種コントロール</h3>';
		$html.= '<ul>';
		foreach($actions['control'] as $url => $v):
			if( ! $url) continue;
			$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
			$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
			$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
		endforeach;
		$html.= '</ul>';
	endif;
	if($actions['index'] || $actions['control']):
		$html.= '</div>';
	endif;

	//user menu
	$html.= '<div id="admin_bar_user">';
	$html.= '<h2>'.\User\Controller_User::$userinfo['user_name'].'</h2>';
	$html.= '<ul>';
	$html.= "<li><a href=\"{$home_uri}user/logout\">ログアウト</a></li>";
	$html.= '</ul>';
	$html.= '</div>';
	$html.= '</div>';

	echo $html;
endif;
//$is_user_logged_in
?>
