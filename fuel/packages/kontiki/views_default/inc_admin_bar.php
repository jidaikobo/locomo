<?php
//$is_user_logged_in
if($is_user_logged_in):
	//item
	$item = isset($item) ? $item : array();
	$item = isset($items) ? $items : $item;
	
	$html = '';

	//controller menu
	$controller4menu = $get_controllers();
	if($controller4menu):
		$html.= '<div style="float:left;width:33%;font-size:90%;">';
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
		$html.= '<div style="float:left;width:33%;font-size:90%;">';
		$html.= '<h2>コンテクストメニュー</h2>';
	endif;
	if($actions['index']):
		$html.= '<h3>各種インデクス</h3>';
		$html.= '<ul>';
		foreach($actions['index'] as $url => $menu_str):
			if( ! $url) continue;
			$html.= "<li><a href=\"{$home_uri}{$url}\">{$menu_str}</a></li>";
		endforeach;
		$html.= '</ul>';
	endif;
	if($actions['control']):
		$html.= '<h3>各種コントロール</h3>';
		$html.= '<ul>';
		foreach($actions['control'] as $url => $menu_str):
			if( ! $url) continue;
			$html.= "<li><a href=\"{$home_uri}{$url}\">{$menu_str}</a></li>";
		endforeach;
		$html.= '</ul>';
	endif;
	if($actions['index'] || $actions['control']):
		$html.= '</div>';
	endif;

	//user menu
	$html.= '<div style="float:left;width:33%;font-size:90%;">';
	$html.= '<h2>ユーザメニュー</h2>';
	$html.= '<ul>';
	$html.= "<li><a href=\"{$home_uri}user/logout\">ログアウト</a></li>";
	$html.= '</ul>';
	$html.= '</div>';

	echo $html;
endif;
//$is_user_logged_in
?>
