<?php
namespace Scaffold;
class Controller_Scaffold extends \Locomo\Controller_Base
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 150,
		'is_for_admin' => true,
		'admin_home' => '\\Scaffold\\Controller_Scaffold/main',
		'nicename' => '足場組み',
		'actionset_classes' =>array(
			'base'   => '\\Scaffold\\Actionset_Base_Scaffold',
		),
	);

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
//				return \Response::redirect(\Uri::create('/scaffold/scaffold/main'));
			endif;

			//vals
			$cmd_orig = \Input::post('cmd');
			$cmd_orig = str_replace(array("\n","\r"), "\n", $cmd_orig );
			$cmd_orig = join(explode("\n", $cmd_orig),' ');
			$cmd_orig = trim(preg_replace("/ +/", ' ', $cmd_orig ));
			$cmd  = Helper_Scaffold::remove_nicename($cmd_orig);
			$cmds = explode(' ', $cmd);

			if( ! $cmd_orig):
				\Session::set_flash('error', 'invalid value sent');
				return \Response::redirect(\Uri::create('/scaffold/scaffold/main'));
			endif;

			//migration
			$name       = array_shift($cmds);
			$table_name = \Inflector::pluralize($name);
			$subjects   = array($table_name, $table_name);

			//molding - logic
			$migration        = Helper_Scaffold::migration($name, $subjects, $cmds);
			$controller       = Helper_Scaffold::generate_controller($name, $cmd_orig);
			$actionset_index  = Helper_Scaffold::generate_actionset_index($name);
			$actionset_base   = Helper_Scaffold::generate_actionset_base($name);
			$actionset_option = Helper_Scaffold::generate_actionset_option($name);
			$model            = Helper_Scaffold::generate_model($name, $cmd_orig);
			$config           = Helper_Scaffold::generate_config($name, $cmd_orig);

			//molding - view
			$tpl_index        = Helper_Scaffold::generate_views_index($name, $cmd_orig);
			$tpl_index_admin  = Helper_Scaffold::generate_views_index($name, $cmd_orig, true);
			$tpl_view         = Helper_Scaffold::generate_views_view($name, $cmd_orig);
			$tpl_edit         = Helper_Scaffold::generate_views_edit($name, $cmds);

			//mkdir
			$scfldpath = APPPATH.'modules/'.$name;
			if( ! file_exists($scfldpath)) mkdir($scfldpath);

			// path
			$viewpath = $scfldpath.'/views';
			if(\Input::post('type') == 'all2' || \Input::post('type') == 'view')
			{
				$scfldpath = APPPATH;
				$viewpath = APPPATH.'/views/'.$name;
			}

			// put files
			$name = strtolower($name);
			$messages = array();
			$error_message = 'maybe same name files are already exist.';

			//migrate_path
			$migrations = \File::read_dir(APPPATH.'migrations');
			sort($migrations);
			$latest_one = array_pop($migrations);
			$latest_prefix = intval(substr($latest_one, 0, strpos($latest_one, '_')));
			$latest_prefix = sprintf("%03d" , $latest_prefix + 1);
			$migrate_path = APPPATH.'migrations/'.$latest_prefix.'_create_'.$name.'.php';

			//generate
			if(\Input::post('type') == 'model')
			{
				// existence check
				if(file_exists($migrate_path)):
					\Session::set_flash('error', $error_message);
					return \Response::redirect(\Uri::create('/scaffold/scaffold/main'));
				endif;

				//migrations
				Helper_Scaffold::putfiles($migrate_path, $migration);

				//model
				$model_path = APPPATH.'classes/model/'.$name.'.php';
				Helper_Scaffold::putfiles($model_path, $model);

				//message
				$messages[] = "modelとmigrationを生成しました。編集するためにコマンドラインからパーミッションを調整してください。";
				$messages[] = "sudo chmod 777 {$migrate_path}";
				$messages[] = "sudo chmod -R 777 {$model_path}";
				$messages[] = "migrationを調整したら、コマンドラインで";
				$messages[] = "cd ".DOCROOT;
				$messages[] = "php oil refine migrate:up";
				$messages[] = "を実行してください。";
			}
			elseif(\Input::post('type') == 'view')
			{
				// existence check
				if(file_exists($viewpath.'/index.php')):
					\Session::set_flash('error', $error_message);
					return \Response::redirect(\Uri::create('/scaffold/scaffold/main'));
				endif;

				//views
				if( ! file_exists($viewpath)) mkdir($viewpath);
				Helper_Scaffold::putfiles($viewpath.'/index.php', $tpl_index) ;
				Helper_Scaffold::putfiles($viewpath.'/index_admin.php', $tpl_index_admin) ;
				Helper_Scaffold::putfiles($viewpath.'/view.php', $tpl_view) ;
				Helper_Scaffold::putfiles($viewpath.'/edit.php', $tpl_edit) ;

				//message
				$messages[] = "viewsのファイル群を生成しました。編集するためにコマンドラインからパーミッションを調整してください。";
				$messages[] = "sudo chmod -R 777 {$viewpath}";
			}
			else
			{
				//migrations
				if( ! file_exists($scfldpath.'/migrations')) mkdir($scfldpath.'/migrations');
				if(\Input::post('type') == 'all2')
				{
					// existence check
					if(file_exists($migrate_path)):
						\Session::set_flash('error', $error_message);
						return \Response::redirect(\Uri::create('/scaffold/scaffold/main'));
					endif;
				}
				else
				{
					// existence check
					$migrate_path = $scfldpath.'/migrations/001_create_'.$name.'.php';
					if(file_exists($migrate_path)):
						\Session::set_flash('error', $error_message);
						return \Response::redirect(\Uri::create('/scaffold/scaffold/main'));
					endif;
				}
				Helper_Scaffold::putfiles($migrate_path, $migration);

				//controller
				if( ! file_exists($scfldpath.'/classes')) mkdir($scfldpath.'/classes');
				if( ! file_exists($scfldpath.'/classes/controller')) mkdir($scfldpath.'/classes/controller');
				Helper_Scaffold::putfiles($scfldpath.'/classes/controller/'.$name.'.php', $controller) ;
	
				//actionset
				if( ! file_exists($scfldpath.'/classes/actionset')) mkdir($scfldpath.'/classes/actionset');
				if( ! file_exists($scfldpath.'/classes/actionset/index')) mkdir($scfldpath.'/classes/actionset/index');
				if( ! file_exists($scfldpath.'/classes/actionset/base')) mkdir($scfldpath.'/classes/actionset/base');
				if( ! file_exists($scfldpath.'/classes/actionset/option')) mkdir($scfldpath.'/classes/actionset/option');
				Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/index/'.$name.'.php', $actionset_index) ;
				Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/base/'.$name.'.php', $actionset_base) ;
				Helper_Scaffold::putfiles($scfldpath.'/classes/actionset/option/'.$name.'.php', $actionset_option) ;
	
				//model
				if( ! file_exists($scfldpath.'/classes/model')) mkdir($scfldpath.'/classes/model');
				Helper_Scaffold::putfiles($scfldpath.'/classes/model/'.$name.'.php', $model) ;
	
				//config
				if( ! file_exists($scfldpath.'/config')) mkdir($scfldpath.'/config');
				Helper_Scaffold::putfiles($scfldpath.'/config/'.$name.'.php', $config) ;
	
				//views
				if( ! file_exists($scfldpath.'/views')) mkdir($scfldpath.'/views');
				Helper_Scaffold::putfiles($viewpath.'/index.php', $tpl_index) ;
				Helper_Scaffold::putfiles($viewpath.'/index_admin.php', $tpl_index_admin) ;
				Helper_Scaffold::putfiles($viewpath.'/view.php', $tpl_view) ;
				Helper_Scaffold::putfiles($viewpath.'/edit.php', $tpl_edit) ;

				//messages
				if(\Input::post('type') == 'all2')
				{
					$messages[] = "モジュールを生成しました。編集するためにコマンドラインからパーミッションを調整してください。";
					$messages[] = "sudo chmod -R 777 ".APPPATH.'class/controller';
					$messages[] = "sudo chmod -R 777 ".APPPATH.'class/model';
					$messages[] = "sudo chmod -R 777 ".APPPATH.'class/config';
					$messages[] = "sudo chmod -R 777 ".APPPATH.'class/migration';
					$messages[] = "sudo chmod -R 777 ".APPPATH.'class/views';
					$messages[] = "migrationとconfigを調整したら、コマンドラインで";
					$messages[] = "cd ".DOCROOT;
					$messages[] = "php oil refine migrate:up";
					$messages[] = "を実行してください。";
				}
				else
				{
					$messages[] = "モジュールを生成しました。編集するためにコマンドラインからパーミッションを調整してください。";
					$messages[] = "sudo chmod -R 777 {$scfldpath}";
					$messages[] = "migrationとconfigを調整したら、コマンドラインで";
					$messages[] = "cd ".DOCROOT;
					$messages[] = "php oil refine migrate:up --modules={$name}";
					$messages[] = "を実行してください。";
				}
			}

			\Session::set_flash('success', $messages);
			\Response::redirect(\Uri::create('/scaffold/scaffold/main'));
		endif;

		//view
		$view->set_global('title', '足場組み');
		$view->base_assign();
		$this->template->content = $view;
	}

}
