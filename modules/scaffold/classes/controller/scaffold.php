<?php
namespace Scaffold;
class Controller_Scaffold extends \Locomo\Controller_Base
{
	// locomo
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
		// only at development
//		if(\Fuel::$env != 'development') die();

		//view
		$view = \View::forge('main');

		// scaffold
		if(\Input::method() == 'POST'):

			if( ! \Security::check_token()):
				\Session::set_flash('error', 'please check token');
//				return \Response::redirect(\Uri::create('/scaffold/scaffold/main'));
			endif;

			// vals
			$cmd_raw = \Input::post('cmd');
			$cmd_orig = str_replace(array("\n","\r"), "\n", $cmd_raw );
			$cmd_orig = join(explode("\n", $cmd_orig),' ');
			$cmd_orig = trim(preg_replace("/ +/", ' ', $cmd_orig ));
			$cmd  = Helper::remove_nicename($cmd_orig);
			$cmds = explode(' ', $cmd);

			if( ! $cmd_orig):
				\Session::set_flash('error', 'invalid value sent');
				return \Response::redirect(\Uri::create('/scaffold/scaffold/main'));
			endif;

			// migration
			$name       = array_shift($cmds);
			$table_name = \Inflector::pluralize($name);
			$subjects   = array($table_name, $table_name);
			$filename   = $name.'.php';

			//add_core_namespace for logic
			\Autoloader::add_core_namespace('Scaffold');
			$helpers = array('migration', 'controller', 'actionset', 'model', 'config', 'index', 'view', 'edit');
			foreach($helpers as $helper)
			{
				$helper_file = $helper.'.php';
				$helper_class = ucfirst($helper);
				//views
				if(in_array($helper, array('index', 'view', 'edit')))
				{
					$helper_file = 'views/'.$helper.'.php';
					$helper_class = 'Views_'.ucfirst($helper);
				}
				//add_class
				if(file_exists(APPPATH.'classes/scaffold/helper/'.$helper_file))
				{
					\Autoloader::add_class(
						'Scaffold\\Helper_'.$helper_class,
						APPPATH.'classes/scaffold/helper/'.$helper_file
					);
				}else{
					\Autoloader::add_class(
						'Scaffold\\Helper_'.$helper_class,
						dirname(__DIR__).'/helper/'.$helper_file
					);
				}
			}

			// molding - logic
			$migration        = \Helper_Migration::generate($name, $subjects, $cmds);
			$controller       = \Helper_Controller::generate($name, $cmd_orig);
			$actionset_index  = \Helper_Actionset::generate($name, 'index');
			$actionset_base   = \Helper_Actionset::generate($name, 'base');
			$actionset_option = \Helper_Actionset::generate($name, 'option');
			$model            = \Helper_Model::generate($name, $cmd_orig);
			$config           = \Helper_Config::generate($name, $cmd_orig);

			// molding - view
			$tpl_index        = \Helper_Views_Index::generate($name, $cmd_orig);
			$tpl_index_admin  = \Helper_Views_Index::generate($name, $cmd_orig, true);
			$tpl_view         = \Helper_Views_View::generate($name, $cmd_orig);
			$tpl_edit         = \Helper_Views_Edit::generate($name, $cmds);

			// mkdir for module
			$scfldpath = APPPATH.'modules/';
			if(\Input::post('type') == 'all')
			{
				if(\File::create_dir($scfldpath, $name))
				{
					$scfldpath = APPPATH.'modules/'.$name;
				}
			}

			// path
			if(\Input::post('type') == 'all2' || \Input::post('type') == 'view')
			{
				$scfldpath = APPPATH;
			}
			$log_dir = APPPATH.'logs/scaffold/'.$name;

			// put files
			$name = strtolower($name);
			$messages = array();

			//migrations
			$latest = \Util::get_latestprefix(APPPATH.'migrations');
			$migrate_file = $latest.'_create_'.$filename;

			//generate
			if(\Input::post('type') == 'model')
			{
				//migrations
				\File::update(APPPATH.'migrations', $migrate_file, $migration);

				//model
				\File::update(APPPATH.'classes/model', $filename, $model);

				//message
				$messages[] = "modelとmigrationを生成しました。編集するためにコマンドラインからパーミッションを調整してください。";
				$messages[] = "sudo chmod 777 ".APPPATH.'migrations/'.$migrate_file;
				$messages[] = "sudo chmod -R 777 ".APPPATH.'classes/model/'.$filename;
				$messages[] = "migrationを調整したら、コマンドラインで";
				$messages[] = "cd ".DOCROOT;
				$messages[] = "php oil refine migrate:up";
				$messages[] = "を実行してください。";
			}
			elseif(\Input::post('type') == 'view')
			{
				//views
				if( ! file_exists(APPPATH.'views/'.$name)) \File::create_dir(APPPATH.'views', $name);
				$viewpath = APPPATH.'views/'.$name;
				\File::update($viewpath, '/index.php', $tpl_index);
				\File::update($viewpath, '/index_admin.php', $tpl_index_admin);
				\File::update($viewpath, '/view.php', $tpl_view);
				\File::update($viewpath, '/edit.php', $tpl_edit);

				//message
				$messages[] = "viewsのファイル群を生成しました。編集するためにコマンドラインからパーミッションを調整してください。";
				$messages[] = "sudo chmod -R 777 {$viewpath}";
			}
			else
			// all and all2
			{
				//migrations
				if( ! file_exists($scfldpath.'/migrations')) \File::create_dir($scfldpath, 'migrations');
				if(\Input::post('type') == 'all')
				{
					$migrate_file = '001_create_'.$filename;
				}
				\File::update($scfldpath.'/migrations', $migrate_file, $migration);

				//controller
				$classpath = $scfldpath.'/classes';
				if( ! file_exists($classpath.'/controller')) \File::create_dir($scfldpath, 'classes/controller');
				\File::update($classpath.'/controller', $filename, $controller);

				//actionset
				$actionsetpath = $classpath.'/actionset';
				if( ! file_exists($actionsetpath)) \File::create_dir($scfldpath, 'classes/actionset');
				if( ! file_exists($actionsetpath.'/index')) \File::create_dir($actionsetpath, 'index');
				if( ! file_exists($actionsetpath.'/base')) \File::create_dir($actionsetpath, 'base');
				if( ! file_exists($actionsetpath.'/option')) \File::create_dir($actionsetpath, 'option');
				\File::update($actionsetpath.'/index', $filename, $actionset_index);
				\File::update($actionsetpath.'/base', $filename, $actionset_base);
				\File::update($actionsetpath.'/option', $filename, $actionset_option);

				//model
				if( ! file_exists($classpath.'/model')) \File::create_dir($classpath, 'model');
				\File::update($classpath.'/model', $filename, $model);
	
				//config
				if( ! file_exists($scfldpath.'/config')) \File::create_dir($scfldpath, 'config');
				\File::update($scfldpath.'/config', $filename, $config);
	
				//views
				if( ! file_exists($scfldpath.'/views')) \File::create_dir($scfldpath, 'views');
				$viewpath = $scfldpath.'/views';
				if(\Input::post('type') == 'all2')
				{
					if( ! file_exists(APPPATH.'views/'.$name)) \File::create_dir(APPPATH.'views', $name);
					$viewpath = APPPATH.'views/'.$name;
				}
				\File::update($viewpath, 'index.php', $tpl_index);
				\File::update($viewpath, 'index_admin.php', $tpl_index_admin);
				\File::update($viewpath, 'view.php', $tpl_view);
				\File::update($viewpath, 'edit.php', $tpl_edit);

				//messages
				if(\Input::post('type') == 'all2')
				{
					$messages[] = "各ファイルを生成しました。編集するためにコマンドラインからパーミッションを調整してください。";
					$messages[] = "sudo chmod -R 777 ".APPPATH.'class';
					$messages[] = "migrationとconfigを調整したら、コマンドラインで";
					$messages[] = "cd ".DOCROOT;
					$messages[] = "php oil refine migrate:up";
					$messages[] = "を実行してください。";
					$messages[] = "名前空間は適宜修正してください。";
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

			//log
			if( ! file_exists($log_dir)) \File::create_dir(APPPATH.'logs', 'scaffold/'.$name);
			$latest = \Util::get_latestprefix($log_dir);
			\File::update($log_dir, $latest.'_scaffold.txt', $cmd_raw);
	
			//message
			$messages[] = "{$log_dir}";
			$messages[] = "にpostされた文字列を保存しています。";
			\Session::set_flash('success', $messages);
			\Response::redirect(\Uri::create('/scaffold/scaffold/main'));
		endif;

		//view
		$view->set_global('title', '足場組み');
		$view->base_assign();
		$this->template->content = $view;
	}

}
