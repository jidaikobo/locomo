<?php
namespace Locomo;
class Controller_Scffld extends \Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => '足場組み', // for human's name
		'explanation'  => 'モジュールやコントローラの基礎を構築します。', // for human's explanation
		'main_action'  => 'main', // main action
		'main_action_name' => '足場組み', // main action's name
		'main_action_explanation' => 'モジュールやコントローラの基礎を構築します。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => true, // true: hide from admin bar
		'order'        => 1150, // order of appearance
		'no_acl'       => true, // true: admin's action. it will not appear at acl.
	);

	/**
	 * action_main()
	 */
	public function action_main()
	{
		// only at development
		if (\Fuel::$env != 'development') die();

		//view
		$view = \View::forge('scffld/main');
		$type = \Input::post('type', 'all');

		// scaffold
		if (\Input::method() == 'POST' && \Security::check_token())
		{
			// permission check
			$arrs = array(
				APPPATH.'migrations/',
//				APPPATH.'classes/',
				APPPATH.'classes/controller/',
				APPPATH.'classes/actionset/',
				APPPATH.'classes/model/',
				APPPATH.'views/',
			);
			foreach ($arrs as $arr)
			{
				if ('0777' !== \File::get_permissions($arr))
				{
					\Session::set_flash('error', $arr.'のパーミッションを確認してください。');
					\Response::redirect(\Uri::create('/scffld/main'));
				}
			}
	
			// vals
			$cmd_raw = \Input::post('cmd');
			$cmd_orig = str_replace(array("\n","\r"), "\n", $cmd_raw );
			$cmd_orig = join(explode("\n", $cmd_orig),' ');
			$cmd_orig = trim(preg_replace("/ +/", ' ', $cmd_orig ));
			$cmd  = \Controller_Scffld_Helper::remove_nicename($cmd_orig);
			$cmds = explode(' ', $cmd);

			if ( ! $cmd_orig)
			{
				\Session::set_flash('error', 'invalid value sent');
				return \Response::redirect(\Uri::create('/scffld/main'));
			}

			// migration
			$name       = array_shift($cmds);
			$table_name = \Inflector::pluralize($name);
			$subjects   = array($table_name, $table_name);
			$filename   = $name.'.php';

			// molding - logic
			$migration  = \Controller_Scffld_Helper_Migration::generate($name, $subjects, $cmds);
			$controller = \Controller_Scffld_Helper_Controller::generate($name, $cmd_orig, $type);
			$actionset  = \Controller_Scffld_Helper_Actionset::generate($name, $type);
			$model      = \Controller_Scffld_Helper_Model::generate($name, $cmd_orig, $type);
			$config     = \Controller_Scffld_Helper_Config::generate($name, $cmd_orig);

			// molding - view
			$tpl_index        = \Controller_Scffld_Helper_Views_Index::generate($name, $cmd_orig);
			$tpl_index_admin  = \Controller_Scffld_Helper_Views_Index::generate($name, $cmd_orig, true);
			$tpl_view         = \Controller_Scffld_Helper_Views_View::generate($name, $cmd_orig);
			$tpl_edit         = \Controller_Scffld_Helper_Views_Edit::generate($name, $cmds);

			// mkdir for module
			$scfldpath = APPPATH.'modules/';
			if ($type == 'all')
			{
				if (\File::create_dir($scfldpath, $name))
				{
					$scfldpath = APPPATH.'modules/'.$name;
				}
			}

			// path
			if ($type == 'all2' || $type == 'view')
			{
				$scfldpath = APPPATH;
			}
			$log_dir = APPPATH.'logs/scffld/'.$name;

			// put files
			$name = strtolower($name);
			$messages = array();

			//migrations
			$latest = \Util::get_latestprefix(APPPATH.'migrations');
			$migrate_file = $latest.'_create_'.$filename;

			//generate
			if ($type == 'model')
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
			elseif ($type == 'view')
			{
				//views
				if ( ! file_exists(APPPATH.'views/'.$name)) \File::create_dir(APPPATH.'views', $name);
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
				if ( ! file_exists($scfldpath.'/migrations')) \File::create_dir($scfldpath, 'migrations');
				if ($type == 'all')
				{
					$migrate_file = '001_create_'.$filename;
				}
				\File::update($scfldpath.'/migrations', $migrate_file, $migration);

				//controller
				$classpath = $scfldpath.'/classes';
				if ( ! file_exists($classpath.'/controller')) \File::create_dir($scfldpath, 'classes/controller');
				\File::update($classpath.'/controller', $filename, $controller);

				//actionset
				$actionsetpath = $classpath.'/actionset';
				if ( ! file_exists($actionsetpath)) \File::create_dir($scfldpath, 'classes/actionset');
				\File::update($actionsetpath.DS, $filename, $actionset);

				//model
				if ( ! file_exists($classpath.'/model')) \File::create_dir($classpath, 'model');
				\File::update($classpath.'/model', $filename, $model);
	
				//config
				if ( ! file_exists($scfldpath.'/config')) \File::create_dir($scfldpath, 'config');
				\File::update($scfldpath.'/config', $filename, $config);
	
				//views
				if ( ! file_exists($scfldpath.'/views')) \File::create_dir($scfldpath, 'views');
				$viewpath = $scfldpath.'/views';
				if ($type == 'all2')
				{
					if ( ! file_exists(APPPATH.'views/'.$name)) \File::create_dir(APPPATH.'views', $name);
					$viewpath = APPPATH.'views/'.$name;
				}
				\File::update($viewpath, 'index.php', $tpl_index);
				\File::update($viewpath, 'index_admin.php', $tpl_index_admin);
				\File::update($viewpath, 'view.php', $tpl_view);
				\File::update($viewpath, 'edit.php', $tpl_edit);

				//messages
				if ($type == 'all2')
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
			if ( ! file_exists($log_dir)) \File::create_dir(APPPATH.'logs', 'scffld/'.$name);
			$latest = \Util::get_latestprefix($log_dir);
			\File::update($log_dir, $latest.'_scaffold.txt', $cmd_raw);
	
			//message
			$messages[] = "{$log_dir}";
			$messages[] = "にpostされた文字列を保存しています。";
			\Session::set_flash('success', $messages);
			\Response::redirect(\Uri::create('/scffld/main'));
		}

		// set errors
		if (\Input::method() == 'POST' && ! \Security::check_token())
		{
			$view->set('type', $type);
			\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
		}

		//view
		$view->set_global('title', '足場組み');
		$this->base_assign();
		$this->template->content = $view;
	}

}
