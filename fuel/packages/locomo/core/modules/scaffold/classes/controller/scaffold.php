<?php
namespace Locomo_Core_Module\Scaffold;
class Controller_Scaffold extends \Locomo\Controller_Crud
{
	/**
	 * action_main()
	 */
	public function action_main()
	{
		//only at development
//		if(\Fuel::$env != 'development') die();

		//call helper
		require(dirname(__DIR__).'/helper/scaffold.php');

		//view
		$view = \View::forge('main');

		//scaffold
		if(\Input::method() == 'POST'):

			if( ! \Security::check_token()):
				\Session::set_flash('error', 'please check token');
				return \Response::redirect(\Uri::create('/scaffold/main/'));
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
			$migration       = Helper_Scaffold::migration($name, $subjects, $cmds);
			$controller      = Helper_Scaffold::generate_controller($name);
			$actionset       = Helper_Scaffold::generate_actionset($name);
			$actionset_owner = Helper_Scaffold::generate_actionset_owner($name);
			$model           = Helper_Scaffold::generate_model($name, $cmd_orig);
			$viewmodel       = Helper_Scaffold::generate_view($name);
			$config          = Helper_Scaffold::generate_config($cmd_orig);

			//molding - view
			$tpl_index  = Helper_Scaffold::generate_views_index($name);
			$tpl_view   = Helper_Scaffold::generate_views_view($name, $cmds);
			$tpl_form   = Helper_Scaffold::generate_views_form($name, $cmds);
			$tpl_create = Helper_Scaffold::generate_views_create($name);
			$tpl_edit   = Helper_Scaffold::generate_views_edit($name);
			$tpl_option = Helper_Scaffold::generate_views_options($name);

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
			Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/'.$name.'.php', $actionset) ;
			Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/'.$name.'_owner.php', $actionset_owner) ;

			//model
			if( ! file_exists($scfldpath.'/classes/model')) mkdir($scfldpath.'/classes/model');
			Helper_Scaffold::putfiles($scfldpath.'/classes/model/'.$name.'.php', $model) ;

			//viewmodel
			if( ! file_exists($scfldpath.'/classes/view')) mkdir($scfldpath.'/classes/view');
			Helper_Scaffold::putfiles($scfldpath.'/classes/view/'.$name.'.php', $viewmodel) ;

			//config
			if( ! file_exists($scfldpath.'/config')) mkdir($scfldpath.'/config');
			Helper_Scaffold::putfiles($scfldpath.'/config/'.$name.'.php', $config) ;

			//views
			if( ! file_exists($scfldpath.'/views')) mkdir($scfldpath.'/views');
			Helper_Scaffold::putfiles($scfldpath.'/views/index.php', $tpl_index) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/index_admin.php', $tpl_index) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/view.php', $tpl_view) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/_form.php', $tpl_form) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/create.php', $tpl_create) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/edit.php', $tpl_edit) ;
			Helper_Scaffold::putfiles($scfldpath.'/views/option_samples.php', $tpl_option) ;

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
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

}