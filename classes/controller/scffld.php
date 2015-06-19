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
	 * _init()
	 */
	public static function _init()
	{
		// only at development
		if (\Fuel::$env != 'development') throw new \Exception('scaffold is only worked under development environment.');

		// permission check
		$arrs = array(
//			APPPATH.'classes/',
			APPPATH.'migrations/',
			APPPATH.'classes/controller/',
			APPPATH.'classes/actionset/',
			APPPATH.'classes/model/',
			APPPATH.'classes/presenter/',
			APPPATH.'views/',
			APPPATH.'modules/',
		);
		foreach ($arrs as $arr)
		{
			if ('0777' !== \File::get_permissions($arr))
			{
				throw new \Exception($arr.'のパーミッションを確認してください。');
			}
		}
	}

	/**
	 * action_main()
	 */
	public function action_main()
	{
		// view
		$view = \View::forge('scffld/main');

		// scaffold
		if (\Input::method() == 'POST' && \Security::check_token())
		{
			// post
			$cmd_raw = \Input::post('cmd');
			$type = \Input::post('type', 'app');
			$scfld_model = \Input::post('model', 'model');

			// populate
			\Session::set_flash('scfld_cmd', $cmd_raw);
			\Session::set_flash('scfld_type', $type);
			\Session::set_flash('scfld_model', $scfld_model);

			// vals
			$cmd_orig = str_replace(array("\n","\r"), "\n", $cmd_raw);
			$cmd_orig = join(explode("\n", $cmd_orig),' ');
			$cmd_orig = trim(preg_replace("/ +/", ' ', $cmd_orig));
			$cmd  = \Controller_Scffld_Helper::remove_nicename($cmd_orig);
			$cmds = explode(' ', $cmd);

			if ( ! $cmd_orig)
			{
				\Session::set_flash('error', 'invalid value sent');
				\Response::redirect(\Uri::create('/scffld/main'));
			}

			// invalid model
			if ( ! in_array($scfld_model, array('Model_Base', 'Model_Base_Soft', 'Model_Base_Temporal', 'Model_Base_Nestedset')))
			{
				throw new \Exception('invalid model choosen.');
			}

			// migration
			$name       = array_shift($cmds);
			$name       = strtolower($name);
			$table_name = \Inflector::pluralize($name);
			$subjects   = array($table_name, $table_name);
			$filename   = $name.'.php';

			// molding - logic
			$migration  = \Controller_Scffld_Helper_Migration::generate($name, $subjects, $cmds);
			$controller = \Controller_Scffld_Helper_Controller::generate($name, $cmd_orig, $type, $scfld_model);
			$actionset  = \Controller_Scffld_Helper_Actionset::generate($name, $cmd_orig, $type, $scfld_model);
			$model      = \Controller_Scffld_Helper_Model::generate($name, $cmd_orig, $type, $scfld_model);
			$config     = \Controller_Scffld_Helper_Config::generate($name, $cmd_orig);

			// error
			if ($model == 'model_soft_error')
			{
				\Session::set_flash('error', '\Orm\Model_Softを使うときには、deleted_atが必須です。');
				\Response::redirect(\Uri::create('/scffld/main'));
			}

			// molding presenter
			$presenter_index = \Controller_Scffld_Helper_Presenter::generate($name, $type, 'index');
			$presenter_edit  = \Controller_Scffld_Helper_Presenter::generate($name, $type, 'edit');
			$presenter_view  = \Controller_Scffld_Helper_Presenter::generate($name, $type, 'view');

			// molding - view
			$tpl_index       = \Controller_Scffld_Helper_Views_Index::generate($name, $cmd_orig);
			$tpl_index_admin = \Controller_Scffld_Helper_Views_Index::generate($name, $cmd_orig, true, $scfld_model);
			$tpl_view        = \Controller_Scffld_Helper_Views_View::generate($name, $cmd_orig);
			$tpl_edit        = \Controller_Scffld_Helper_Views_Edit::generate($name, $cmds);

			// path - module
			if ($type == 'module')
			{
				$scfldpath = APPPATH.'modules/';
				if (\File::create_dir($scfldpath, $name))        $scfldpath = APPPATH.'modules/'.$name.DS;
				if (\File::create_dir($scfldpath, 'migrations')) $migrationpath = $scfldpath.'migrations/';
				if (\File::create_dir($scfldpath, 'config'))     $configpath = $scfldpath.'config/';
				if (\File::create_dir($scfldpath, 'views'))      $viewpath = $scfldpath.'views/';
				if (\File::create_dir($viewpath, $name))         $viewpath.= $name.DS;
				if (\File::create_dir($scfldpath, 'classes'))    $classpath = $scfldpath.'classes/';
				if (\File::create_dir($classpath, 'controller')) $controllerpath = $classpath.'controller/';
				if (\File::create_dir($classpath, 'model'))      $modelpath = $classpath.'model/';
				if (\File::create_dir($classpath, 'presenter'))  $presenterpath = $classpath.'presenter/';
				if (\File::create_dir($presenterpath, $name))    $presenterpath.= $name.DS;
				if (\File::create_dir($presenterpath, 'index'))  $presenteridxpath = $presenterpath.'index/';
				if (\File::create_dir($classpath, 'actionset'))  $actionsetpath = $classpath.'actionset/';
			}

			// path - app
			if ($type == 'app' || $type == 'view')
			{
				$scfldpath      = APPPATH;
				$migrationpath  = APPPATH.'migrations/';
				$classpath      = $scfldpath.'classes/';
				$controllerpath = $classpath.'controller/';
				$modelpath      = $classpath.'model/';
				$presenterpath  = $classpath.'presenter/';
				$actionsetpath  = $classpath.'actionset/';
				if (\File::create_dir($scfldpath.'views/', $name)) $viewpath = APPPATH.'views/'.$name;
				if (\File::create_dir($presenterpath, $name)) $presenterpath.= $name.DS;
				if (\File::create_dir($presenterpath, 'index')) $presenteridxpath = $presenterpath.'index/';
			}
			$log_dir = APPPATH.'logs/scffld/'.$name;

			// messages
			$messages = array();

			// migrations
			$latest = \Util::get_latestprefix(APPPATH.'migrations');
			$migrate_file = $latest.'_create_'.$filename;

			// model and migration
			if ($type == 'model' || $type == 'app' || $type == 'module')
			{
				// migrations
				\File::update($migrationpath, $migrate_file, $migration);

				// model
				\File::update($modelpath, $filename, $model);

				// message
				$messages[] = "modelとmigrationを生成しました。";
//				$messages[] = "sudo chmod 777 ".$migrationpath.$migrate_file;
//				$messages[] = "sudo chmod -R 777 ".$modelpath.$filename;
			}

			// views
			if ($type == 'view' || $type == 'app' || $type == 'module')
			{
				\File::update($viewpath, 'index.php', $tpl_index);
				\File::update($viewpath, 'index_admin.php', $tpl_index_admin);
				\File::update($viewpath, 'view.php', $tpl_view);
				\File::update($viewpath, 'edit.php', $tpl_edit);

				// prensenter
				\File::update($presenteridxpath, 'admin.php', $presenter_index);
				\File::update($presenterpath, 'view.php', $presenter_view);
				\File::update($presenterpath, 'edit.php', $presenter_edit);

				// message
				$messages[] = "viewsとpresenterのファイル群を生成しました。";
			}

			// controller and actionset
			if ($type == 'app' || $type == 'module')
			{
				\File::update($controllerpath, $filename, $controller);
				\File::update($actionsetpath, $filename, $actionset);

				// message
				$messages[] = "controllerとactionsetのファイル群を生成しました。";
			}

			// config
			if ($type == 'module')
			{
				\File::update($configpath, $filename, $config);

				// message
				$messages[] = "configのファイルを生成しました。";
				$messages[] = "migrationとconfigを調整したら、コマンドラインで";
				$messages[] = "php oil refine migrate:up --modules={$name}";
				$messages[] = "を実行してください。";
			}

			// messages
			if ($type == 'app')
			{
				$messages[] = "migrationを調整したら、コマンドラインで";
				$messages[] = "php oil refine migrate:up";
				$messages[] = "を実行してください。";
			}

			// clear population
			\Session::set_flash('scfld_type', '');
			\Session::set_flash('scfld_cmd', '');
			\Session::set_flash('scfld_model', '');

			// log
			if ( ! file_exists($log_dir)) \File::create_dir(APPPATH.'logs', 'scffld/'.$name);
			$latest = \Util::get_latestprefix($log_dir);
			\File::update($log_dir, $latest.'_scaffold.txt', $cmd_raw);
	
			// message
			$messages[] = "{$log_dir}";
			$messages[] = "にpostされた文字列を保存しています。";
			\Session::set_flash('success', $messages);
			\Response::redirect(\Uri::create('/scffld/main'));
		}

		// set errors
		if (\Input::method() == 'POST' && ! \Security::check_token())
		{
			\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
		}

		// view
		$view->set_global('title', '足場組み');
		$this->template->content = $view;
	}

}
