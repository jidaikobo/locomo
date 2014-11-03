<?php
namespace Scaffold;
class Controller_Scaffold extends \Locomo\Controller_Base
{
	/**
	 * action_main()
	 */
	public function action_main()
	{
		//only at development
//		if(\Fuel::$env != 'development') die();

		//template path
		if(! defined('LOCOMO_SCFLD_TPL_PATH')){
			define('LOCOMO_SCFLD_TPL_PATH', dirname(dirname(__DIR__)).'/module_templates/');
		}

		//view
		$view = \View::forge('main');

		//scaffold
		if(\Input::method() == 'POST'):

			if( ! \Security::check_token()):
				\Session::set_flash('error', 'please check token');
//				return \Response::redirect(\Uri::create('/scaffold/main/'));
			endif;

			//vals
			$cmd_orig = \Input::post('cmd');
			$cmd  = Helper_Scaffold::remove_nicename($cmd_orig);
			$cmds = explode(' ', $cmd);

			//migration
			$name       = array_shift($cmds);
			$table_name = \Inflector::pluralize($name);
			$subjects   = array($table_name, $table_name);

			//molding - logic
			$migration        = Helper_Scaffold::migration($name, $subjects, $cmds);
			$controller       = Helper_Scaffold::generate_controller($name);
			$actionset_index  = Helper_Scaffold::generate_actionset_index($name);
			$actionset_base   = Helper_Scaffold::generate_actionset_base($name);
			$actionset_owner  = Helper_Scaffold::generate_actionset_owner($name);
			$actionset_option = Helper_Scaffold::generate_actionset_option($name);
			$model            = Helper_Scaffold::generate_model($name, $cmd_orig);
			$config           = Helper_Scaffold::generate_config($cmd_orig);

			//molding - view
			$tpl_index        = Helper_Scaffold::generate_views_index($name, $cmd_orig);
			$tpl_index_admin  = Helper_Scaffold::generate_views_index($name, $cmd_orig, true);
			$tpl_view         = Helper_Scaffold::generate_views_view($name, $cmd_orig);
			$tpl_edit         = Helper_Scaffold::generate_views_edit($name, $cmds);

			//mkdir
			$scfldpath = PKGPROJPATH.'modules/'.$name;
			if( ! file_exists($scfldpath)) mkdir($scfldpath);

			//put files
			$name = strtolower($name);

			//migrations
			if( ! file_exists($scfldpath.'/migrations')) mkdir($scfldpath.'/migrations');
			Helper_Scaffold::putfiles($scfldpath.'/migrations/001_create_'.$name.'.php', $migration) ;

			//controller
			if( ! file_exists($scfldpath.'/classes')) mkdir($scfldpath.'/classes');
			if( ! file_exists($scfldpath.'/classes/controller')) mkdir($scfldpath.'/classes/controller');
			Helper_Scaffold::putfiles($scfldpath.'/classes/controller/'.$name.'.php', $controller) ;

			//actionset
			if( ! file_exists($scfldpath.'/classes/actionset')) mkdir($scfldpath.'/classes/actionset');
			if( ! file_exists($scfldpath.'/classes/actionset/index')) mkdir($scfldpath.'/classes/actionset/index');
			if( ! file_exists($scfldpath.'/classes/actionset/base')) mkdir($scfldpath.'/classes/actionset/base');
			if( ! file_exists($scfldpath.'/classes/actionset/owner')) mkdir($scfldpath.'/classes/actionset/owner');
			if( ! file_exists($scfldpath.'/classes/actionset/option')) mkdir($scfldpath.'/classes/actionset/option');
			Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/index/'.$name.'.php', $actionset_index) ;
			Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/base/'.$name.'.php', $actionset_base) ;
			Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/owner/'.$name.'.php', $actionset_owner) ;
			Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/option/'.$name.'.php', $actionset_option) ;

			//model
			if( ! file_exists($scfldpath.'/classes/model')) mkdir($scfldpath.'/classes/model');
			Helper_Scaffold::putfiles($scfldpath.'/classes/model/'.$name.'.php', $model) ;

			//config
			if( ! file_exists($scfldpath.'/config')) mkdir($scfldpath.'/config');
			Helper_Scaffold::putfiles($scfldpath.'/config/'.$name.'.php', $config) ;

			//views
			if( ! file_exists($scfldpath.'/views')) mkdir($scfldpath.'/views');
			Helper_Scaffold::putfiles($scfldpath.'/views/index.php', $tpl_index) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/index_admin.php', $tpl_index_admin) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/view.php', $tpl_view) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/edit.php', $tpl_edit) ;

			//messages
			$messages   = array();
			$messages[] = "モジュールを生成しました。編集するためにコマンドラインからパーミッションを調整してください。";
			$messages[] = "sudo chmod -R 777 {$scfldpath}";
			$messages[] = "migrationとconfigを調整したら、コマンドラインで";
			$messages[] = "cd ".DOCROOT;
			$messages[] = "php oil refine migrate:up --modules={$name}";
			$messages[] = "を実行してください。";

			\Session::set_flash('success', $messages);
			\Response::redirect(\Uri::create('/scaffold/main/'));
		endif;

		//view
		$view->set_global('title', '足場組み');
		$view->base_assign();
		$this->template->content = $view;
	}

}
