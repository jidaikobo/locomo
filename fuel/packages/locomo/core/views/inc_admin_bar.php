<?php
//$is_user_logged_in
if($is_user_logged_in):
	//item
	$item = isset($item) ? $item : null;
	
	$html = '';
	$html.= '<div id="adminbar" class="clearfix">';
		$html.= '<a href="#anchor_adminbar" class="skip show_if_focus" tabindex="1">ツールバーに移動</a>';
		$html.= '<h2 class="skip"><a id="anchor_adminbar" tabindex="-1">ツールバー</a></h2>';

	//.adminbar_bottom
		$html.='<div class="adminbar_bottom">';
			//context menu
			$html.='<div class="adminbar_main">';
				$html.= '<div class="admin_controller">';
					$action_name = $title ? '：'.$title : '';
					$html.="<h3>{$controller_name}{$action_name}</h3>"; //ツールバーのアンカーにも足す？
				$html.= '</div><!-- /.admin_controller -->';
				$actions = \Actionset::get_menu(
					$controller,
					$realm = 'base',
					$item,
					$get_authed_url = true
				);
				if($actions):
					$html.= '<div class="admin_context">';
						$html.= '<ul class="holizonal_list">';
						foreach($actions as $url => $v):
							if( ! $url) continue;
							$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
							$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
							$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
						endforeach;
						$html.= '</ul>';
					$html.= '</div><!-- .adminbar_context -->';
				endif;
			$html.= '</div><!-- .adminbar_main -->';
	
			//context menu2
			$html.= '<div class="adminbar_sub">';			
			$actions = \Actionset::get_menu(
				$controller,
				$realm = 'ctrl',
				$item,
				$get_authed_url = true
			);
			if($actions):
				$html.= '<div class="admin_ctrl hide_if_smalldisplay">';
					$html.= '<ul class="holizonal_list">';
					foreach($actions as $url => $v):
						if( ! $url) continue;
						$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
						$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
						$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
					endforeach;
					$html.= '</ul>';
				$html.='</div><!-- /.admin_ctrl -->';
			endif;
			
						//context menu2	
			$actions = \Actionset::get_menu(
				$controller,
				$realm = 'option',
				$item,
				$get_authed_url = true
			);
				if($actions):
					$html.= '<div class="admin_module_option">';
					$html.= "<a href=\"javascript:void(0)\" class=\"modal dropdown_list trigger\" title=\"{$controller_name}の設定を開く\"><span class=\"adminbar_icon icononly\"><img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_module_option.png\" alt=\"{$controller_name}の設定\"></span></a>";
						$html.= '<ul class="modal dropdown_list boxshadow">';
						foreach($actions as $url => $v):
							if( ! $url) continue;
							$confirm_str = "'{$v['menu_str']}をしてよろしいですか？'";
							$script = @$v['confirm'] ? ' onclick="return confirm('.$confirm_str.')" onkeypress="return confirm('.$confirm_str.')"' : '';
							$html.= "<li><a href=\"{$home_uri}{$url}\"{$script}>{$v['menu_str']}</a></li>";
						endforeach;
						$html.= '</ul>';
					$html.= '</div><!-- .admin_module_option -->';
				endif;

			$html.= '</div><!-- /.adminbar_sub -->';
		$html.= '</div><!-- /.adminbar_bottom -->';

		//adminbar_top  -- logo(sitetop), mainmenu, user, option, renderinfo
		$html.= '<div class="adminbar_top">'; 
			$html.= "<img src=\"{$home_uri}content/fetch_view/images/parts/logo.png\" id=\"adminbar_logo\" alt=\"{$site_title}\" title=\"{$site_title}トップへ\">" ;
			$html.= '<div class="adminbar_main">';
			$html.= '<h3 class="skip">メインメニュー</h3>';
			//controller menu
			$controller4menu = $get_controllers();
			if($controller4menu):
				$html.= '<div class="admin_menu">';
				$html.= '<a href="javascript:void(0);" class="modal dropdown_list trigger" title="メニューを開く"><span class="adminbar_icon">'."<img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_menu.png\" alt=\"\">".'</span><span class="hide_if_smalldisplay">メニュー</span></a>';
				// IE8では画像のサイズをCSSで与えた場合、画像の本来のサイズで親要素が描画されてしまうので、明示的なサイズを持った要素で画像を囲む。
				$html.= '<ul class="modal dropdown_list boxshadow">';
				foreach($controller4menu as $v):
					if( ! $v['url']) continue;
					$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
				endforeach;
				$html.= '</ul>';
				$html.= '</div><!-- /.admin_menu -->';
			endif;
		/*
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
		*/
			$html.= '</div><!-- /.adminbar_main -->';
		
			$html.='<div class="adminbar_sub">';
		
			//thx debe
			$root_prefix = $is_root ? '_root' : '' ;
		
			//user menu
			$html.= '<div class="adminbar_user">';
				$html.= '<a href="javascript:void(0);" class="modal dropdown_list trigger" title="ユーザメニューを開く:'.\User\Controller_User::$userinfo["display_name"].'でログインしています"><span class="adminbar_icon">'."<img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_user{$root_prefix}.png\" alt=\"\"></span><span class=\"hide_if_smalldisplay\">".\User\Controller_User::$userinfo["display_name"].'</span></a>';
				$html.= '<ul class="modal dropdown_list boxshadow">';
				$html.= '<li class="show_if_smalldisplay"><span class="label">'.\User\Controller_User::$userinfo["display_name"].'</span></li>';
				if( ! $is_admin):
					$html.= "<li><a href=\"{$home_uri}user/view/{$userinfo["user_id"]}\">ユーザ情報</a></li>";
				endif;
				$html.= "<li><a href=\"{$home_uri}user/logout\">ログアウト</a></li>";
				$html.= '</ul>';
			$html.= '</div><!-- /.adminbar_user -->';
		
			//admin option menu
			$controller4menu = $get_controllers($is_admin = true);
			if($controller4menu):
				$html.= '<div class="admin_option">';
					$html.= "<a href=\"javascript:void(0)\" class=\"modal dropdown_list trigger\" title=\"管理者設定を開く\"><span class=\"adminbar_icon icononly\"><img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_option.png\" alt=\"管理者設定\"></span></a>";
					$html.= '<ul class="modal dropdown_list boxshadow">';
					foreach($controller4menu as $v):
						if( ! $v['url']) continue;
						$html.= "<li><a href=\"{$home_uri}{$v['url']}\">{$v['nicename']}</a></li>";
					endforeach;
					$html.= '</ul>';
				$html.= '</div><!-- /.admin_option -->';
			endif;
		
			//help
			$html.= '<div class="admin_help">';
				$html.= '<a href="" title="ヘルプ"><span class="adminbar_icon">'."<img src=\"{$home_uri}content/fetch_view/images/parts/adminbar_icon_help.png\" alt=\"ヘルプ\">".'</span></a>';
			$html.= '</div><!-- /.admin_help -->';
			
			//処理速度
			$html.= $is_admin ? '<div id="render_info" class="hide_if_smalldisplay">{exec_time}s  {mem_usage}mb</div>' : '';
		$html.= '</div><!-- /.adminbar_sub -->';
		$html.= '</div><!-- /.adminbar_top -->';

	
	$html.= '</div><!-- /#adminbar -->';

	echo $html;
endif;
//$is_user_logged_in
?>



