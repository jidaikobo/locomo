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
		$is_scaffold_tmp = \Config::get('is_scaffold_tmp');

		// only at development
		if (\Fuel::$env == 'development')
		{
			if ($is_scaffold_tmp)
			{
				$arrs = array(APPPATH.'tmp/');
			}
			else
			{
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
			}

			foreach ($arrs as $arr)
			{
				if ('0777' !== \File::get_permissions($arr))
				{
					throw new \Exception($arr.'のパーミッションを確認してください。');
				}
			}
		}

		// keep permission
		$current_permission = umask();

		// view
		$view = \View::forge('scffld/main');

		// scaffold
		if (\Input::method() == 'POST' && \Security::check_token())
		{
			// post
			$cmd_raw     = \Input::post('cmd');
			$scfld_type  = \Input::post('type', 'app');
			$scfld_model = \Input::post('model', 'model');

			// repopulate for error
			\Session::set_flash('cmd_raw', $cmd_raw);
			\Session::set_flash('type', $scfld_type);
			\Session::set_flash('model', $scfld_model);

			// vals
			$cmd_orig = str_replace(array("\n","\r"), "\n", $cmd_raw);
			$cmd_orig = join(explode("\n", $cmd_orig),' ');
			$cmd_orig = trim(preg_replace("/ +/", ' ', $cmd_orig));
			$cmd  = \Controller_Scffld_Helper::remove_nicename($cmd_orig);
			$cmds = explode(' ', $cmd);
			$files = array();

			// errors
			$errors = array();
			if ( ! isset($cmds[0]) || $cmds[0] == '')
			{
				$errors[] = 'first line must be controller/module name.';
			}

			// banned name - reserved keyword
			$banned = array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor');
			if ($scfld_type == 'module' && in_array(strtolower($cmds[0]), $banned))
			{
				$errors[] = 'reserved keyword. cannot use "'.$cmds[0].'" for mudule\'s namespace. ';
			}

			// empty
			if ( ! $cmd_orig)
			{
				$errors[] = 'invalid value sent.';
			}

			// redirection
			if ($errors)
			{
				\Session::set_flash('error', $errors);
				\Response::redirect(\Uri::create('/scffld/main'));
			}

			// invalid model
			if ( ! in_array($scfld_model, array('Model_Base', 'Model_Base_Soft', 'Model_Base_Temporal', 'Model_Base_Nestedset')))
			{
				throw new \Exception('invalid model choosen.');
			}

			// change permission
			umask(002);

			// vals
			$orig_name  = array_shift($cmds);
			$orig_name  = strtolower($orig_name);
			$names      = explode('_', $orig_name);
			$name       = array_pop($names);
			$table_name = \Inflector::pluralize($orig_name);
			$subjects   = array($table_name, $table_name);
			$filename   = $name.'.php';
			$scfldbase  = $is_scaffold_tmp ? APPPATH.'tmp/' : APPPATH;

			// molding - logic
			$migration  = \Controller_Scffld_Helper_Migration::generate($orig_name, $subjects, $cmds);
			$controller = \Controller_Scffld_Helper_Controller::generate($orig_name, $cmd_orig, $scfld_type, $scfld_model);
			$actionset  = \Controller_Scffld_Helper_Actionset::generate($orig_name, $cmd_orig, $scfld_type, $scfld_model);
			$model      = \Controller_Scffld_Helper_Model::generate($orig_name, $cmd_orig, $scfld_type, $scfld_model);
			$config     = \Controller_Scffld_Helper_Config::generate($orig_name, $cmd_orig);

			// error
			if ($model == 'model_soft_error')
			{
				\Session::set_flash('error', '\Orm\Model_Softを使うときには、deleted_atが必須です。');
				\Response::redirect(\Uri::create('/scffld/main'));
			}

			// molding presenter
			$presenter_index = \Controller_Scffld_Helper_Presenter::generate($orig_name, $scfld_type, 'index');
			$presenter_edit  = \Controller_Scffld_Helper_Presenter::generate($orig_name, $scfld_type, 'edit');
			$presenter_view  = \Controller_Scffld_Helper_Presenter::generate($orig_name, $scfld_type, 'view');

			// molding - view
			$tpl_index       = \Controller_Scffld_Helper_Views_Index::generate($orig_name, $cmd_orig);
			$tpl_index_admin = \Controller_Scffld_Helper_Views_Index::generate($orig_name, $cmd_orig, true, $scfld_model);
			$tpl_view        = \Controller_Scffld_Helper_Views_View::generate($orig_name, $cmd_orig);
			$tpl_edit        = \Controller_Scffld_Helper_Views_Edit::generate($orig_name, $cmds);

			// path - module
			if ($scfld_type == 'module')
			{
				// error
				if (strpos($orig_name, '_') !== false)
				{
					\Session::set_flash('error', 'モジュール名にアンダーバーを含めないでください。');
					\Response::redirect(\Uri::create('/scffld/main'));
				}

				// $scfldpath
				\File::create_dir_if_not_exist($scfldbase, 'modules');

				// pathes
				$scfldpath        = $scfldbase.'modules/'.$name.DS;
				$migrationpath    = $scfldpath.'migrations/';
				$configpath       = $scfldpath.'config/';
				$viewspath        = $scfldpath.'views/'.$name;
				$classespath      = $scfldpath.'classes/';
				$actionsetpath   = $classespath.'actionset/';
				$controllerpath   = $classespath.'controller/';
				$modelpath        = $classespath.'model/';
				$presenterpath    = $classespath.'presenter/'.$name.DS;
				$presenteridxpath = $presenterpath.'index/';

				// $scfldpath
				\File::create_dir_if_not_exist(dirname($scfldpath), $name);

				// $migrationpath
				\File::create_dir_if_not_exist($scfldpath, 'migrations');

				// $configpath
				\File::create_dir_if_not_exist($scfldpath, 'config');

				// $viewspath
				\File::create_dir_if_not_exist($scfldpath, 'views');
				\File::create_dir_if_not_exist($scfldpath.'views', $name);

				// $classespath
				\File::create_dir_if_not_exist($scfldpath, 'classes');

				foreach (array('actionset', 'controller', 'model', 'presenter') as $d)
				{
					\File::create_dir_if_not_exist($classespath, $d);
				}

				\File::create_dir_if_not_exist(dirname($presenterpath), $name);
				\File::create_dir_if_not_exist($presenterpath, 'index');
			}

			// path - app
			if ($scfld_type == 'app' || $scfld_type == 'view' || $scfld_type == 'model')
			{
				$scfldpath        = $scfldbase;
				$viewspath        = $scfldpath.'views/';
				$migrationpath    = $scfldpath.'migrations/';
				$classespath      = $scfldpath.'classes/';
				$controllerpath   = $classespath.'controller/';
				$modelpath        = $classespath.'model/';
				$presenterpath    = $classespath.'presenter/';
				$actionsetpath    = $classespath.'actionset/';

				// $classpath
				\File::create_dir_if_not_exist($scfldpath, 'classes');

				// $migrationpath
				\File::create_dir_if_not_exist($scfldpath, 'migrations');

				// 通常のコントローラ類とview and presenter
				if ($scfld_type == 'app' || $scfld_type == 'view')
				{
					// $migrationpath
					\File::create_dir_if_not_exist($scfldpath, 'views');

					// $viewpath
					foreach ($names as $p)
					{
						\File::create_dir_if_not_exist($viewspath, $p);
						$viewspath.= $p.DS;
					}
					\File::create_dir_if_not_exist($viewspath, $name);
					$viewspath.= $name.DS;

					// $presenterpath
					\File::create_dir_if_not_exist($classespath, 'presenter');
					foreach ($names as $p)
					{
						\File::create_dir_if_not_exist($presenterpath, $p);
						$presenterpath.= $p.DS;
					}
					\File::create_dir_if_not_exist($presenterpath, $name);
					$presenterpath.= $name.DS;
					\File::create_dir_if_not_exist($presenterpath, 'index');
					$presenteridxpath = $presenterpath.'index/';

					// $controllerpath
					\File::create_dir_if_not_exist($classespath, 'controller');
					foreach ($names as $p)
					{
						\File::create_dir_if_not_exist($controllerpath, $p);
						$controllerpath.= $p.DS;
					}

					// $actionsetpath
					\File::create_dir_if_not_exist($classespath, 'actionset');
					foreach ($names as $p)
					{
						\File::create_dir_if_not_exist($actionsetpath, $p);
						$actionsetpath.= $p.DS;
					}
				}

				// 通常のコントローラあるいはモデルとマイグレーションのみ
				if ($scfld_type == 'app' || $scfld_type == 'model')
				{
					\File::create_dir_if_not_exist($classespath, 'model');
					foreach ($names as $p)
					{
						\File::create_dir_if_not_exist($modelpath, $p);
						$modelpath.= $p.DS;
					}
				}
			}
			$log_dir = APPPATH.'logs/scffld/'.$name;

			// messages
			$messages = array();

			// migrations
			$latest = \Util::get_latestprefix($migrationpath);
			$migrate_file = $latest.'_create_'.$orig_name.'.php';

			// model and migration
			if ($scfld_type == 'model' || $scfld_type == 'app' || $scfld_type == 'module')
			{
				// migrations
				\File::update($migrationpath, $migrate_file, $migration);
				$files[] = $migrationpath.$migrate_file;

				// model
				\File::update($modelpath, $filename, $model);
				$files[] = $modelpath.$filename;

				// message
				$messages[] = "modelとmigrationを生成しました。";
			}

			// views
			if ($scfld_type == 'view' || $scfld_type == 'app' || $scfld_type == 'module')
			{
				\File::update($viewspath, 'index.php', $tpl_index);
				\File::update($viewspath, 'index_admin.php', $tpl_index_admin);
				\File::update($viewspath, 'view.php', $tpl_view);
				\File::update($viewspath, 'edit.php', $tpl_edit);
				$files[] = $viewspath;

				// prensenter
				\File::update($presenteridxpath, 'admin.php', $presenter_index);
				\File::update($presenterpath, 'view.php', $presenter_view);
				\File::update($presenterpath, 'edit.php', $presenter_edit);
				$files[] = $presenterpath;

				// message
				$messages[] = "viewsとpresenterのファイル群を生成しました。";
			}

			// controller and actionset
			if ($scfld_type == 'app' || $scfld_type == 'module')
			{
				\File::update($controllerpath, $filename, $controller);
				\File::update($actionsetpath, $filename, $actionset);
				$files[] = $controllerpath.$filename;
				$files[] = $actionsetpath.$name;

				// message
				$messages[] = "controllerとactionsetのファイル群を生成しました。";
			}

			// config
			if ($scfld_type == 'module')
			{
				\File::update($configpath, $filename, $config);
				$files[] = $configpath.'modules/'.$filename;

				// message
				$messages[] = "configのファイルを生成しました。";
				$messages[] = "migrationとconfigを調整したら、コマンドラインで";
				$messages[] = "php oil refine migrate:up --modules={$name}";
				$messages[] = "を実行してください。";
			}

			// messages
			if ($scfld_type == 'app')
			{
				$messages[] = "migrationを調整したら、コマンドラインで";
				$messages[] = "php oil refine migrate:up";
				$messages[] = "を実行してください。";
			}

			// log
			\File::create_dir_if_not_exist(APPPATH.'logs', 'scffld/'.$name);
			$latest = \Util::get_latestprefix($log_dir);
			\File::update($log_dir, $latest.'_scaffold.txt', $cmd_raw);
			\File::update($log_dir, 'files.php', '<?php'."\n".'$scfflds = '.var_export($files, 1).';');

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

		// umask chack
		umask($current_permission);
		if (umask() != $current_permission)
		{
			die('An error occurred while changing back the umask');
		}

		// view
		$view->set_global('title', '足場組み');
		$this->template->content = $view;
	}

	/**
	 * action_destory()
	 */
	public function action_destory()
	{
		// view
		$view = \View::forge('scffld/destory');
		if (\Input::method() == 'POST' && \Security::check_token())
		{
			$cmd_raw     = \Input::post('cmd');
			$scfld_type  = \Input::post('type', 'app');
			$scfld_model = \Input::post('model', 'model');

			// repopulate for error
			\Session::set_flash('cmd_raw', $cmd_raw);
			\Session::set_flash('type', $scfld_type);
			\Session::set_flash('model', $scfld_model);
		}

		// set errors
		if (\Input::method() == 'POST' && ! \Security::check_token())
		{
			\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
		}

		// view
		$view->set_global('title', '削除');
		$this->template->content = $view;
	}
}
