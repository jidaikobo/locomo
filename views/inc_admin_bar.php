<?php
// is_user?
if (\Auth::check()):
	$html = '';
	$html.= '<nav id="adminbar" class="clearfix noprint">';
		$html.= '<h2 class="skip"><a href="javascript:void(0);" id="anchor_adminbar" tabindex="0">ここからツールバーです</a></h2>';

	// .adminbar_bottom
		$html.='<div class="adminbar_bottom lcmbar_bottom">';
			// context menu
			$html.='<div class="adminbar_main">';
				$html.= '<div id="adminbar_controller">';
				$mod_home = '';
				if (\Arr::get($locomo, 'module.nicename'))
				{
					$current_name = \Inflector::ctrl_to_safestr(\Arr::get($locomo, 'module.main_controller'));
					$current_nicename = \Arr::get($locomo, 'module.nicename');
				}
				else
				{
					$controller_name = \Arr::get($locomo, 'controller.name');
					$controller_name = \Util::get_locomo($controller_name, 'main_controller') ?: $controller_name ;
					$current_name = \Inflector::ctrl_to_safestr($controller_name);
					$current_nicename = \Util::get_locomo($controller_name, 'nicename') ;
				}

				$ctrl_index = '';
				// Controller_Sys
				if ($current_name == '-Controller_Sys'):
					if(\Auth::has_access($locomo['controller']['name'].'/home')):
						$top_link = \Html::anchor(\Uri::create('sys/admin/'), '管理トップ');
					else:
						$top_link = '管理トップ';
					endif;
				else:
					// 権限があればコントローラトップへのリンクを表示
					if(\Auth::has_access($locomo['controller']['name'].DS.\Arr::get($locomo, 'controller.main_action'))):
						$top_link = \Html::anchor(\Uri::create('sys/admin/'.$current_name), $current_nicename).' ';
					else:
						$top_link = $current_nicename;
					endif;
				endif;
				$html.= "<h3>{$top_link} : <span>{$title}</span></h3>\n";
				$html.= '</div><!-- /#adminbar_controller -->';

				// realmがbase, option, ctrl以外のアクションセットを取得
				$indexes = \Actionset::get_actionset_by_realm($locomo['controller']['name'], array('base','option','ctrl'), $exclusive = true);
				foreach ($indexes as $realm => $index):
					$idxmenu = \Actionset::generate_menu_html($indexes[$realm], array('class'=>'semimodal menulist hidden_item boxshadow')) ;
					if(!$idxmenu) continue;
					$html.= '<div id="adminbar_index_list">';
					$html.= "<a href=\"javascript:void(0)\" class=\"has_dropdown toggle_item\" title=\"インデクス一覧を開く\">インデクス<span class=\"skip\">エンターでメニューを開きます</span></a>";
					
					$html.= $idxmenu;
					$html.= '</div><!-- /#adminbar_index_listadmin_index_list -->';
				endforeach;

				$bases = \Actionset::get_actionset_by_realm($locomo['controller']['name'], array('base'));
				// indexがない場合は、$ctrl_indexを表示
				if ($ctrl_index && ! $idxmenu):
					array_unshift($bases, array('urls' => array($ctrl_index)));
				endif;
				if ($bases):
					$html.= '<div id="adminbar_context">';
					$html.= \Actionset::generate_menu_html($bases['base'], array('class'=>'adminbar_list_horizontal'));
					$html.= '</div><!-- /#adminbar_context -->';
				endif;

			$html.= '</div><!-- /.adminbar_main -->';

			// context menu2
			$html.= '<div class="adminbar_sub">';
			$ctrl = \Actionset::get_actionset_by_realm($locomo['controller']['name'], array('ctrl'));
			if (@$ctrl):
				$html.= '<div id="admin_ctrl" class="hide_if_smalldisplay">';
				$html.= \Actionset::generate_menu_html($ctrl['ctrl'], array('class'=>'adminbar_list_horizontal'));
				$html.='</div><!-- /#admin_ctrl -->';
			endif;
			
				// option menu
				$optmenu = \Actionset::get_actionset_by_realm($locomo['controller']['name'], array('option'));
				$optmenu = $optmenu ? \Actionset::generate_menu_html($optmenu['option'], array('class'=>'semimodal menulist hidden_item boxshadow')) : false ;

				if ($optmenu):
					$html.= '<div id="module_option">';
					$html.= "<a href=\"javascript:void(0)\" class=\"has_dropdown toggle_item\" title=\"".$current_nicename."の設定を開く\"><span class=\"adminbar_icon icononly\"><img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_module_option.png\" alt=\"\"><span class=\"skip\">".$current_nicename."の設定 エンターでメニューを開きます</span></span></a>";
					$html.= $optmenu;
					$html.= '</div><!-- /#adminbar_module_option -->';
				endif;

			$html.= '</div><!-- /.adminbar_sub -->';
		$html.= '</div><!-- /.adminbar_bottom -->';

		// adminbar_top  -- logo(sitetop), mainmenu, renderinfo, option, user
		$html.= '<div class="adminbar_top lcmbar_top">'; 
			$html.=  \Asset::img('system/logo_s.png', array('id' => 'adminbar_logo', 'alt' => '')) ;
			$html.= '<div class="adminbar_main menu">';
			$html.= \Config::get('no_home') ? '' : '<div id="adminbar_home"><a href="'.\Uri::base().'" title="ホーム"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_home.png\" alt=\"\"></span>".'<span class="hide_if_smalldisplay">ホーム</span></a></div>';
			$html.= '<div id="adminbar_dashboard"><a href="'.\Uri::base().'sys/dashboard/" title="ダッシュボード"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_dashboard.png\" alt=\"\"></span>".'<span class="hide_if_smalldisplay">ダッシュボード</span></a></div>';
			$html.= '<h3 class="skip">ここからメインメニューです</h3>';
			// controller menu
			$controller_menu = '';
			$menu_separators = \Config::get('menu_separators');
			foreach($locomo['controllers'] as $k => $v):
				if ( ! $v['is_for_admin'] && $v['show_at_menu'])
				{
					$sep = array_key_exists($k, $menu_separators) ? ' class="'.$menu_separators[$k].'"' : '';
					$safe_str = \Inflector::ctrl_to_safestr($k);
					$controller_menu.= '<li'.$sep.'><a href="'.\Uri::base().'sys/admin/'.$safe_str.'" class="'.substr(strtolower($safe_str),1).'">'.$v['nicename'].'</a></li>';
				}
			endforeach;
			if ($controller_menu):
				$html.= '<div id="adminbar_menu" class="menu">';
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
			$html.= \Fuel::$env == 'development' ? '<div id="adminbar_render_info">{exec_time}s  {mem_usage}mb</div>' : '';


// ブックマーク テスト中
			// bkmk ブックマーク
			if(\Auth::has_access('\\Controller_Bkmk/index_admin')):
				$bookmark_uri = \Uri::base().'bkmk/index_admin';
				$html.= '<div class="admin_bookmark menu lcm_floatwindow_parent">';
				$html.= '<a href="'.$bookmark_uri.'" title="ブックマーク" id="lcm_bookmark"  class="toggle_floatwindow" data-uri="'.$bookmark_uri.'" data-target-window="bookmark_window" accesskey="D"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_bookmark.png\" alt=\"\">".'<span class="skip">ブックマーク エンターでブックマークを開きます</span></span></a>';
				$html.= '</div><!-- /.admin_bookmark -->';
			endif;
// ブックマーク テスト中

			// help
			if(\Auth::has_access('\\Controller_Hlp/index_admin')):
				$help_uri = \Uri::base().'hlp/view?action='.urlencode(\Inflector::ctrl_to_safestr($locomo['locomo_path']));
				$html.= '<div id="adminbar_help" class="menu lcm_floatwindow_parent">';
				$html.= '<a href="'.$help_uri.'" title="ヘルプ" id="lcm_help" class="toggle_floatwindow" data-uri="'.$help_uri.'"  accesskey="H"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_help.png\" alt=\"\">".'<span class="skip">ヘルプ エンターでヘルプを開きます</span></span></a>';
				$html.= '</div><!-- /#admin_help -->';
			endif;
			
			// admin option menu
			$admin_menu = '';
			foreach($locomo['controllers'] as $k => $v):
				if ($v['is_for_admin'] && $v['show_at_menu']):
					$url = \Inflector::ctrl_to_dir($v['main_action']);
					$admin_menu.= '<li><a href="'.\Uri::create($url).'">'.$v['nicename'].'</a></li>';
				endif;
			endforeach;
			if ($admin_menu):
				$html.= '<div id="adminbar_option" class="menu">';
				$html.= "<a href=\"javascript:void(0)\" class=\"has_dropdown toggle_item\" title=\"管理者設定を開く\"><span class=\"adminbar_icon icononly\"><img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_option.png\" alt=\"\"><span class=\"skip\">管理者設定 エンターでメニューを開きます</span></span></a>";
				$html.= '<ul class="semimodal menulist hidden_item">'.$admin_menu.'</ul>';
				$html.= '</div><!-- /#adminbar_option -->';
			endif;

		// thx debe
			$root_prefix = \Auth::is_admin() ? '_admin' : '' ;
			$root_prefix = \Auth::is_root() ? '_root' : $root_prefix ;
		
			// user menu
			$html.= '<div id="adminbar_user" class="menu">';
			$html.= '<a href="javascript:void(0);" class="has_dropdown toggle_item" title="ユーザメニューを開く:'.\Auth::get('display_name').'でログイン中"><span class="adminbar_icon">'."<img src=\"".\Uri::base()."lcm_assets/img/system/adminbar_icon_user{$root_prefix}.png\" alt=\"\"></span><span class=\"hide_if_smalldisplay\">".\Auth::get('display_name').'<span class="skip"> エンターでメニューを開きます</span></span></a>';
			$html.= '<ul class="semimodal menulist hidden_item">';
			$html.= '<li class="show_if_smalldisplay"><span class="label">'.\Auth::get('display_name').'</span></li>';
			$html.= "<li><a href=\"".\Uri::base()."auth/logout\">ログアウト</a></li>";
			if ( ! \Auth::is_admin()):
				$html.= "<li class=\"has_bottm_separator\"><a href=\"".\Uri::base()."usr/view/".\Auth::get('id')."\">ユーザ情報</a></li>";
				if (\Config::get('is_use_customusergroup')):
					$html.= "<li class=\"has_bottm_separator\"><a href=\"".\Uri::base()."usrgrp/custom/index_admin/\">カスタムユーザグループ</a></li>";
				endif;
			endif;
			// usergroup
			$usergroups = \Auth::get('usergroup');

			if($usergroups):
				$html.= '<li id="adminbar_usergroup">所属ユーザグループ<ul>';
				foreach ($usergroups as $k => $usergroup):
					if (in_array($k, [0, -10])) continue; // usergroup -10: logged in users, 0:guest
					$html.= "<li>{$usergroup->name}</li>";
				endforeach;
				$html.= '</ul></li>';
			endif;
			$html.= '</ul>';
			$html.= '</div><!-- /#adminbar_user -->';

		$html.= '</div><!-- /.adminbar_sub -->';
		$html.= '</div><!-- /.adminbar_top -->';
		$html.='<a class="skip show_if_focus" href="#main_content">本文へ移動</a>';
	$html.= '</nav><!-- /#adminbar -->';

	echo $html;
endif;
// is_user?
?>

<div id="lcm_help_window" class="lcm_floatwindow resizable draggable" style=" display: none;">
	<h1 id="lcm_help_title" class="lcmbar_top lcmbar_top_title lcm_floatwindow_title">
		<a href="javascript:lcm_floatwindow({trigger: 'lcm_help'});void(0);" tabindex="0" id="help_title_anchor" class="has_accesskey" >ヘルプ<span class="accesskey">(H)</span></a>
	</h1>
	<div id="lcm_help_txt" class="lcm_load_txt lcm_modal_content">
		<img src="<?php echo \Uri::base() ;?>lcm_assets/img/system/mark_loading_m.gif" class="mark_loading" alt="" role="presentation">
	</div>
	<a href="javascript: void(0);" role="button" class="lcm_close_window lcm_reset_style menubar_icon"><img src="<?php echo \Uri::base() ;?>lcm_assets/img/system/adminbar_icon_close.png" alt="ヘルプウィンドウを閉じる"></a>
</div>

<!-- ブックマークテスト中 -->
<div id="bookmark_window" class="lcm_floatwindow resizable draggable" style=" display: none;">
	<h1 id="bookmark_title" class="lcmbar_top lcmbar_top_title lcm_floatwindow_title">
<a href="javascript:lcm_floatwindow({trigger: 'lcm_bookmark'});void(0);" tabindex="0" id="bookmark_title_anchor" class="has_accesskey" >ブックマーク<span class="accesskey">(D)</span></a>
	</h1>
	<div id="bookmark_txt" class="lcm_modal_content">
		<div id="bookmark_form">
			<h2>現在のページ</h2>
			<input type="text" id="admin_bookmark_name_input" value="<?php echo $title; ?><?php if (\Arr::get($locomo, 'module.nicename')) echo ' - '.\Arr::get($locomo, 'module.nicename'); ?>">
			<input type="text" id="admin_bookmark_url_input" value="<?php echo \Uri::create(\Uri::current(), array(), \Input::get()); ?>">
			<button type="" id="admin_bookmark_add_button" class="ar button primary small">ブックマークに追加</button>
		</div>
		<h2>ブックマークリスト</h2>
		<ul id="bookmarks">
		</ul>
		<div id="bookmark_bottom">
			<?php echo \Html::anchor('bkmk/bulk', 'ブックマーク管理へ', array('class' => 'button small')) ?>
		</div>
	</div>
	<a href="javascript: void(0);" role="button" class="lcm_close_window lcm_reset_style menubar_icon"><img src="<?php echo \Uri::base() ;?>lcm_assets/img/system/adminbar_icon_close.png" alt="ブックマークウィンドウを閉じる"></a>
</div>


<?php echo \Asset::js('bookmark.js') ?>

<?php /* adminbar用のjs、css。重複をチェックして読み込む */
//	echo \Asset::js('jquery.lcm.adminbar.js');
?>
<script>
<!--
$(function(){
	if(typeof(lcm_env)=='undefined') lcm_env = new Object();
	$.getScript("<?php echo \Asset::get_file('jquery.lcm.adminbar.js', 'js'); ?>");
//	$('<link/>', {rel: 'stylesheet',type: 'text/css', href: '<?php echo \Asset::get_file('adminbar.css', 'css') ?>'}).appendTo('head'); //読み込むタイミングでずれる？
	if(! lcm_env.load_lcm_modal_semimodal)      $.getScript("<?php echo \Asset::get_file('jquery.lcm.modal.semimodal.js', 'js') ?>");
	if(! lcm_env.load_dragresize)               $.getScript("<?php echo \Asset::get_file('jquery-ui.dragresize.js', 'js') ?>");
	if(! $.fn.tabindex_ctrl)                    $.getScript("<?php echo \Asset::get_file('jquery.tabindexctrl.js', 'js') ?>");
	if(typeof(check_formchange) == 'undefined') $.getScript("<?php echo \Asset::get_file('jquery.lcm.msgconfirm.js', 'js') ?>");
	if(typeof(lcm_floatwindow)  == 'undefined') $.getScript("<?php echo \Asset::get_file('jquery.lcm.floatwindow.js', 'js') ?>");
	if(typeof(jQuery.ui)        == 'undefined'){
		$.getScript("<?php echo \Asset::get_file('jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.min.js', 'js') ?>");
		$.getScript("<?php echo \Asset::get_file('jquery-ui-1.10.4/development-bundle/ui/i18n/jquery.ui.datepicker-ja.js', 'js') ?>") ;
	}
	if(typeof($.ex)             == 'undefined') $.getScript("<?php echo \Asset::get_file('jquery.exresize/jquery.exresize.0.1.0.js', 'js') ?>");
});
// -->
</script>
