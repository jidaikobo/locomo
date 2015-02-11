<?php
// is_user?
if (\Auth::check()):
	$html = '';
	$html.= '<nav id="adminbar" class="clearfix">';
		$html.= '<h2 class="skip"><a id="anchor_adminbar" tabindex="0">ここからツールバーです</a></h2>';

	// .adminbar_bottom
		$html.='<div class="adminbar_bottom lcmbar_bottom">';
			// context menu
			$html.='<div class="adminbar_main">';
				$html.= '<div class="admin_controller">';
				$mod_home = '';
				if (\Arr::get($locomo, 'module.nicename'))
				{
					$current_name = \Inflector::ctrl_to_safestr(\Arr::get($locomo, 'module.main_controller'));
					$current_nicename = \Arr::get($locomo, 'module.nicename');
				}
				else
				{
					$current_name = \Inflector::ctrl_to_safestr(\Arr::get($locomo, 'controller.name'));
					$current_nicename = \Arr::get($locomo, 'controller.nicename') ;
				}

				$ctrl_index = '';
				// Controller_Sys
				if ($current_name == '-Controller_Sys'):
					if(\Auth::has_access($locomo['controller']['name'].'::home')):
						$top_link = \Html::anchor(\Uri::create('sys/admin/'), '管理トップ');
					else:
						$top_link = '管理トップ';
					endif;
				else:
//					if(\Auth::has_access($locomo['controller']['name'].DS.'sys/admin')):
					// 権限があればコントローラトップへのリンクを表示
					if(\Auth::has_access($locomo['controller']['name'].'::'.\Arr::get($locomo, 'controller.main_action'))):
						$top_link = \Html::anchor(\Uri::create('sys/admin/'.$current_name), $current_nicename).' ';
					else:
						$top_link = $current_nicename;
					endif;
					$action_name = $title ? $title : '';
					if (isset($locomo['controller']['main'])):
						$action_name = \Arr::get($locomo, 'controller.main_action_name') ?: 'トップ';
						if(\Auth::has_access($locomo['controller']['name'].'::'.$locomo['controller']['main_action'])):
							$ctrl_index = \Html::anchor($locomo['controller']['home'], $action_name);
						endif;
					endif;
				endif;
				$html.= "<h3>{$top_link} : <span>{$title}</span></h3>\n";
				$html.= '</div><!-- /.admin_controller -->';

				$idxmenu = \Arr::get($actionset, 'index') ? \Actionset::generate_menu_html($actionset['index'], array('class'=>'semimodal menulist hidden_item boxshadow')) : false ;
				if ($idxmenu):
					$html.= '<div class="admin_index_list">';
					$html.= "<a href=\"javascript:void(0)\" class=\"has_dropdown toggle_item\" title=\"インデクス一覧を開く\">インデクス<span class=\"skip\">エンターでメニューを開きます</span></a>";
					$html.= $idxmenu;
					$html.= '</div><!-- .admin_index_list -->';
				endif;

				$bases = \Arr::get($actionset, 'base', array());
				// indexがない場合は、$ctrl_indexを表示
				if ($ctrl_index && ! $idxmenu):
					array_unshift($bases, array('urls' => array($ctrl_index)));
				endif;
				if ($bases):
					$html.= '<div class="admin_context">';
					$html.= \Actionset::generate_menu_html($bases, array('class'=>'horizontal_list'));
					$html.= '</div><!-- .adminbar_context -->';
				endif;

			$html.= '</div><!-- .adminbar_main -->';

			// context menu2
			$html.= '<div class="adminbar_sub">';			
			if (@$actionset['ctrl']):
				$html.= '<div class="admin_ctrl hide_if_smalldisplay">';
				$html.= \Actionset::generate_menu_html($actionset['ctrl'], array('class'=>'horizontal_list'));
				$html.='</div><!-- /.admin_ctrl -->';
			endif;
			
				// option menu
				$optmenu = \Arr::get($actionset, 'option') ? \Actionset::generate_menu_html($actionset['option'], array('class'=>'semimodal menulist hidden_item boxshadow')) : false ;

				if ($optmenu):
					$html.= '<div class="admin_module_option">';
					$html.= "<a href=\"javascript:void(0)\" class=\"has_dropdown toggle_item\" title=\"".$current_nicename."の設定を開く\"><span class=\"adminbar_icon icononly\"><img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_module_option.png\" alt=\"\"><span class=\"skip\">".$current_nicename."の設定 エンターでメニューを開きます</span></span></a>";
					$html.= $optmenu;
					$html.= '</div><!-- .admin_module_option -->';
				endif;

			$html.= '</div><!-- /.adminbar_sub -->';
		$html.= '</div><!-- /.adminbar_bottom -->';

		// adminbar_top  -- logo(sitetop), mainmenu, renderinfo, option, user
		$html.= '<div class="adminbar_top lcmbar_top">'; 
			$html.=  \Asset::img('system/logo_s.png', array('id' => 'adminbar_logo', 'alt' => '')) ;
			$html.= '<div class="adminbar_main">';
			$html.= \Config::get('no_home') ? '' : '<a href="'.\Uri::base().'" title="ホーム"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_home.png\" alt=\"\"></span>".'<span class="hide_if_smalldisplay">ホーム</span></a>';
			$html.= '<a href="'.\Uri::base().'sys/dashboard/" title="ダッシュボード"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_dashboard.png\" alt=\"\"></span>".'<span class="hide_if_smalldisplay">ダッシュボード</span></a>';
			$html.= '<h3 class="skip">ここからメインメニューです</h3>';
			// controller menu
			$controller_menu = '';
			$menu_separators = \Config::get('menu_separators');
			foreach($locomo['controllers'] as $k => $v):
				if ( ! $v['is_for_admin'] && $v['show_at_menu'])
				{
					$sep = array_key_exists($k, $menu_separators) ? ' class="'.$menu_separators[$k].'"' : '';
					$controller_menu.= '<li'.$sep.'><a href="'.\Uri::base().'sys/admin/'.\Inflector::ctrl_to_safestr($k).'">'.$v['nicename'].'</a></li>';
				}
			endforeach;
			if ($controller_menu):
				$html.= '<div class="admin_menu">';
				$html.= '<a href="javascript:void(0);" class="has_dropdown toggle_item" title="メニューを開く"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_menu.png\" alt=\"\">".'</span><span class="hide_if_smalldisplay">メニュー<span class="skip"> エンターでメニューを開きます</span></span></a>';
				// IE8では画像のサイズをCSSで与えた場合、画像の本来のサイズで親要素が描画されてしまうので、明示的なサイズを持った要素で画像を囲む。
				$html.= '<ul class="semimodal hidden_item menulist">';
				$html.= $controller_menu;
				$html.= '</ul>';
				$html.= '</div><!-- /.admin_menu -->';
			endif;
			$html.= '</div><!-- /.adminbar_main -->';
			$html.='<div class="adminbar_sub">';
		
			// 処理速度
			$html.= \Fuel::$env == 'development' ? '<div id="render_info">{exec_time}s  {mem_usage}mb</div>' : '';

			// help
			$help_uri = \Uri::base().'hlp/view?action='.urlencode(\Inflector::ctrl_to_safestr($locomo['locomo_path']));
			$html.= '<div class="admin_help">';
			$html.= '<a href="'.$help_uri.'" title="ヘルプ" id="lcm_help" data-uri="'.$help_uri.'"  accesskey="H"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_help.png\" alt=\"\">".'<span class="skip">ヘルプ エンターでヘルプを開きます</span></span></a>';
			$html.= '</div><!-- /.admin_help -->';

			// admin option menu
			$admin_menu = '';
			foreach($locomo['controllers'] as $k => $v):
				if ($v['is_for_admin'] && $v['show_at_menu']):
					$url = \Inflector::ctrl_to_dir($v['main_action']);
					$admin_menu.= '<li><a href="'.\Uri::create($url).'">'.$v['nicename'].'</a></li>';
				endif;
			endforeach;
			if ($admin_menu):
				$html.= '<div class="admin_option">';
				$html.= "<a href=\"javascript:void(0)\" class=\"has_dropdown toggle_item\" title=\"管理者設定を開く\"><span class=\"adminbar_icon icononly\"><img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_option.png\" alt=\"\"><span class=\"skip\">管理者設定 エンターでメニューを開きます</span></span></a>";
				$html.= '<ul class="semimodal menulist hidden_item">'.$admin_menu.'</ul>';
				$html.= '</div><!-- /.admin_option -->';
			endif;

		// thx debe
			$root_prefix = \Auth::is_admin() ? '_admin' : '' ;
			$root_prefix = \Auth::is_root() ? '_root' : $root_prefix ;
		
			// user menu
			$html.= '<div class="adminbar_user">';
			$html.= '<a href="javascript:void(0);" class="has_dropdown toggle_item" title="ユーザメニューを開く:'.\Auth::get('display_name').'でログイン中"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_user{$root_prefix}.png\" alt=\"\"></span><span class=\"hide_if_smalldisplay\">".\Auth::get('display_name').'<span class="skip"> エンターでメニューを開きます</span></span></a>';
			$html.= '<ul class="semimodal menulist hidden_item">';
			$html.= '<li class="show_if_smalldisplay"><span class="label">'.\Auth::get('display_name').'</span></li>';
			$html.= "<li><a href=\"".\Uri::base()."auth/logout\">ログアウト</a></li>";
			if ( ! \Auth::is_admin()):
				$html.= "<li class=\"has_bottm_separator\"><a href=\"".\Uri::base()."usr/view/".\Auth::get('id')."\">ユーザ情報</a></li>";
			endif;
			// usergroup
			$usergroups = \Auth::get('usergroup');

			if($usergroups):
				$html.= '<li class="usergroup">所属ユーザグループ<ul>';
				foreach ($usergroups as $k => $usergroup):
					if ($k == -10) continue; // usergroup -10: logged in users
					$html.= "<li>{$usergroup->name}</li>";
				endforeach;
				$html.= '</ul></li>';
			endif;
			$html.= '</ul>';
			$html.= '</div><!-- /.adminbar_user -->';

		$html.= '</div><!-- /.adminbar_sub -->';
		$html.= '</div><!-- /.adminbar_top -->';
		$html.='<a class="skip show_if_focus" href="#main_content">本文へ移動</a>';
	$html.= '</nav><!-- /#adminbar -->';

	echo $html;
endif;
// is_user?
?>

<div id="help_window">
	<h1 id="help_title" class="lcmbar_top lcmbar_top_title"><a href="javascript:$(function(){show_help()});void(0);" tabindex="0"  id="help_title_anchor" style="color: #fff; text-decoration: none; cursor: move;">ヘルプ<span class="accesskey">(H)</span></a></h1>
	<div id="help_txt">
		<img src="<?php echo \Uri::base() ;?>lcm_assets/img/system/mark_loading_m.gif" class="mark_loading" alt="" role="presentation">
	</div>
	<a href="javascript: void(0);" role="button" class="lcm_close_parent lcm_reset_style menubar_icon"><img src="<?php echo \Uri::base() ;?>lcm_assets/img/system/adminbar_icon_close.png" alt="ヘルプウィンドウを閉じる"></a>
</div>
