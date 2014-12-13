<?php
//is_user?
if (\Auth::check()):
	$html = '';
	$html.= '<nav id="adminbar" class="clearfix">';
//		$html.= '<a href="#anchor_adminbar" class="skip show_if_focus" tabindex="1">ツールバーに移動</a>'; //inc_header,inc_admin_headerに移動。
		$html.= '<h2 class="skip"><a id="anchor_adminbar" tabindex="0">ここからツールバーです</a></h2>';

	//.adminbar_bottom
		$html.='<div class="adminbar_bottom">';
			//context menu
			$html.='<div class="adminbar_main">';
				$html.= '<div class="admin_controller">';
				$mod_home = '';
				$current_name = \Inflector::ctrl_to_safestr(\Arr::get($locomo, 'module.main_controller'));
				$current_nicename = \Arr::get($locomo, 'module.nicename') ?: \Arr::get($locomo, 'controller.nicename') ;

				$ctrl_index = '';
				if ($current_name == '-Admin-Controller_Admin'):
					$top_link = \Html::anchor(\Uri::create('admin/home/'), '管理トップ');
				else:
					$top_link = \Html::anchor(\Uri::create('admin/home/'.$current_name), $current_nicename).' ';
					$action_name = $title ? $title : '';
					if (isset($locomo['controller']['home'])):
						$home_name = \Arr::get($locomo, 'controller.home_name') ?: 'トップ';
						$ctrl_index = \Html::anchor($locomo['controller']['home'], $home_name);
					endif;
				endif;
				$html.= "<h3>{$top_link} : <span>{$title}</span></h3>\n";
				$html.= '</div><!-- /.admin_controller -->';

				$bases = \Arr::get($actionset, 'base', array());
				$ctrl_index and array_unshift($bases, array('urls' => array($ctrl_index)));

				if ($bases):
					$html.= '<div class="admin_context">';
					$html.= \Actionset::generate_menu_html($bases, array('class'=>'holizonal_list'));
					$html.= '</div><!-- .adminbar_context -->';
				endif;
			$html.= '</div><!-- .adminbar_main -->';

			//context menu2
			$html.= '<div class="adminbar_sub">';			
			if (@$actionset['ctrl']):
				$html.= '<div class="admin_ctrl hide_if_smalldisplay">';
				$html.= \Actionset::generate_menu_html($actionset['ctrl'], array('class'=>'holizonal_list'));
				$html.='</div><!-- /.admin_ctrl -->';
			endif;
			
				//option menu
				$optmenu = \Arr::get($actionset, 'option') ? \Actionset::generate_menu_html($actionset['option'], array('class'=>'modal hidden_item boxshadow')) : false ;
				if ($optmenu):
					$html.= '<div class="admin_module_option">';
					$html.= "<a href=\"javascript:void(0)\" class=\"has_dropdown toggle_item\" title=\"".\Config::get('nicename')."の設定を開く\"><span class=\"adminbar_icon icononly\"><img src=\"".\Uri::base()."content/fetch_view/img/system/adminbar_icon_module_option.png\" alt=\"".\Config::get('nicename')."の設定\"></span></a>";
					$html.= $optmenu;
					$html.= '</div><!-- .admin_module_option -->';
				endif;

			$html.= '</div><!-- /.adminbar_sub -->';
		$html.= '</div><!-- /.adminbar_bottom -->';

		//adminbar_top  -- logo(sitetop), mainmenu, renderinfo, option, user
		$html.= '<div class="adminbar_top">'; 
			$html.= "<img src=\"".\Uri::base()."content/fetch_view/img/system/logo.png\" id=\"adminbar_logo\" alt=\"".\Config::get('site_title')."\" title=\"".\Config::get('site_title')."トップへ\">" ;
			$html.= '<div class="adminbar_main">';
			$html.= \Config::get('no_home') ? '' : '<a href="'.\Uri::base().'" title="ホーム"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."content/fetch_view/img/system/adminbar_icon_home.png\" alt=\"\"></span>".'<span class="hide_if_smalldisplay">ホーム</span></a>';
			$html.= '<a href="'.\Uri::base().'admin/dashboard/" title="ダッシュボード"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."content/fetch_view/img/system/adminbar_icon_dashboard.png\" alt=\"\"></span>".'<span class="hide_if_smalldisplay">ダッシュボード</span></a>';
			$html.= '<h3 class="skip">ここからメインメニューです</h3>';
			//controller menu
			if ($locomo['controllers']):
				$html.= '<div class="admin_menu">';
				$html.= '<a href="javascript:void(0);" class="has_dropdown toggle_item" title="メニューを開く"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."content/fetch_view/img/system/adminbar_icon_menu.png\" alt=\"\">".'</span><span class="hide_if_smalldisplay">メニュー</span></a>';
				// IE8では画像のサイズをCSSで与えた場合、画像の本来のサイズで親要素が描画されてしまうので、明示的なサイズを持った要素で画像を囲む。
				$html.= '<ul class="modal hidden_item boxshadow">';
				foreach($locomo['controllers'] as $k => $v):
					if ( ! $v['is_for_admin'] && $v['show_at_menu'])
					{
						$html.= '<li><a href="'.\Uri::base().'admin/home/'.\Inflector::ctrl_to_safestr($k).'">'.$v['nicename'].'</a></li>';
					}
				endforeach;
				$html.= '</ul>';
				$html.= '</div><!-- /.admin_menu -->';
			endif;
			$html.= '</div><!-- /.adminbar_main -->';
		
			$html.='<div class="adminbar_sub">';
		
			//処理速度
			$html.= \Fuel::$env == 'development' ? '<div id="render_info">{exec_time}s  {mem_usage}mb</div>' : '';

			//help
			$html.= '<div class="admin_help">';
				$html.= '<a href="'.\Uri::base().'help/help/index_admin?searches[mod_or_ctrl]='.urlencode(\Inflector::ctrl_to_safestr($locomo['locomo_path'])).'" title="ヘルプ"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."content/fetch_view/img/system/adminbar_icon_help.png\" alt=\"ヘルプ\">".'</span></a>';
			$html.= '</div><!-- /.admin_help -->';

			//admin option menu
			if (\Auth::is_admin()):
				$html.= '<div class="admin_option">';
				$html.= "<a href=\"javascript:void(0)\" class=\"has_dropdown toggle_item\" title=\"管理者設定を開く\"><span class=\"adminbar_icon icononly\"><img src=\"".\Uri::base()."content/fetch_view/img/system/adminbar_icon_option.png\" alt=\"管理者設定\"></span></a>";
				$html.= '<ul class="modal hidden_item boxshadow">';
					foreach($locomo['controllers'] as $k => $v):
						if ($v['is_for_admin'] && $v['show_at_menu'])
							{
									$url = \Inflector::ctrl_to_dir($v['admin_home']);
								$html.= '<li><a href="'.\Uri::create($url).'">'.$v['nicename'].'</a></li>';
							}
					endforeach;
					$html.= '</ul>';
				$html.= '</div><!-- /.admin_option -->';
			endif;

		//thx debe
			$root_prefix = \Auth::is_admin() ? '_admin' : '' ;
			$root_prefix = \Auth::is_root() ? '_root' : $root_prefix ;
		
			//user menu
			$html.= '<div class="adminbar_user">';
			$html.= '<a href="javascript:void(0);" class="has_dropdown toggle_item" title="ユーザメニューを開く:'.\Auth::get('display_name').'でログインしています"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."content/fetch_view/img/system/adminbar_icon_user{$root_prefix}.png\" alt=\"\"></span><span class=\"hide_if_smalldisplay\">".\Auth::get('display_name').'</span></a>';
			$html.= '<ul class="modal hidden_item boxshadow">';
			$html.= '<li class="show_if_smalldisplay"><span class="label">'.\Auth::get('display_name').'</span></li>';
			if ( ! \Auth::is_admin()):
				$html.= "<li><a href=\"".\Uri::base()."user/user/view/".\Auth::get('id')."\">ユーザ情報</a></li>";
			endif;
			$html.= "<li><a href=\"".\Uri::base()."user/auth/logout\">ログアウト</a></li>";
			$html.= '</ul>';
			$html.= '</div><!-- /.adminbar_user -->';

		$html.= '</div><!-- /.adminbar_sub -->';
		$html.= '</div><!-- /.adminbar_top -->';
		$html.='<a class="skip show_if_focus" href="#page_title">本文へ移動</a>';
	$html.= '</nav><!-- /#adminbar -->';

	echo $html;
endif;
//is_user?
?>
