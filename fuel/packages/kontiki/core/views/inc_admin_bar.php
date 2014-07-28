<?php
//$is_user_logged_in
if($is_user_logged_in):
	//item
	$item = isset($item) ? $item : array();
	$item = isset($items) ? $items : $item;
	
	$html = '';
	$html.= '<div id="admin_bar">';
	$html.= '<a href="#anchor_admin_bar" class="skip show-if-focus" tabindex="1">管理バーに移動</a>';//移動用のアンカーだが、どのような形がよいのか要検討。
	$html.= '<a id="anchor_admin_bar" tabindex="0"></a>';
	//controller menu
	$controller4menu = $get_controllers();
	if($controller4menu):
		$html.= '<div id="admin_bar_controller">';
		$html.= '<h2><a href="javascript:void(0);" class="listOpen">コントローラメニュー</a></h2>';
		$html.= '<div class="admin_bar_sub">';
		$html.= '<ul>';
		foreach($controller4menu as $v):
			if( ! $v['url']) continue;
			$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
		endforeach;
		$html.= '</ul>';
		$html.= '</div><!-- /.admin_bar_sub -->';
		$html.= '</div>';
	endif;

	//context menu
	$actions = $get_actionset($controller, $item);
	if($actions['index'] || $actions['control']):
		$html.= '<div id="admin_bar_context">';
		$html.= '<h2><a href="javascript:void(0);" class="listOpen">コンテクストメニュー</a></h2>';
		$html.= '<div class="admin_bar_sub">';
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
		$html.= '</div><!-- /.admin_bar_sub -->';
		$html.= '</div>';
	endif;

	//user menu
	$html.= '<div id="admin_bar_user">';
	$html.= '<h2><a href="javascript:void(0);" class="listOpen">'.\User\Controller_User::$userinfo['user_name'].'</a></h2>';
	$html.= '<ul>';
	$html.= "<li><a href=\"{$home_uri}user/logout\">ログアウト</a></li>";
	$html.= '</ul>';
	$html.= '</div>';
	$html.= '</div>';

	echo $html;
endif;
//$is_user_logged_in
?>
