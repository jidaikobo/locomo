<?php
namespace Locomo;
class Controller_Scffld extends \Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'                => '足場組み',
		'explanation'             => 'モジュールやコントローラの基礎を構築します。',
		'main_action'             => 'main',
		'main_action_name'        => '足場組み',
		'main_action_explanation' => 'モジュールやコントローラの基礎を構築します。',
		'show_at_menu'            => true,
		'is_for_admin'            => true,
		'order'                   => 1150,
		'no_acl'                  => true,
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
					APPPATH.'lang/',
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
			$is_output   = \Input::post('output', 0);

			// repopulate for error
			\Session::set_flash('cmd_raw', $cmd_raw);
			\Session::set_flash('type', $scfld_type);
			\Session::set_flash('model', $scfld_model);
			\Session::set_flash('output', $is_output);

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
			$banned = array(
				'__halt_compiler', 'abstract', 'and', 'array', 'as',
				'break', 'callable', 'case', 'catch', 'class', 'clone',
				'const', 'continue', 'declare', 'default', 'die', 'do',
				'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor',
				'endforeach', 'endif', 'endswitch', 'endwhile', 'eval',
				'exit', 'extends', 'final', 'for', 'foreach', 'function',
				'global', 'goto', 'if', 'implements', 'include',
				'include_once', 'instanceof', 'insteadof', 'interface',
				'isset', 'list', 'namespace', 'new', 'or', 'print',
				'private', 'protected', 'public', 'require', 'require_once',
				'return', 'static', 'switch', 'throw', 'trait', 'try',
				'unset', 'use', 'var', 'while', 'xor');
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
			if ( !
					 in_array(
						 $scfld_model,
						 array(
							 'Model_Base',
							 'Model_Base_Soft',
							 'Model_Base_Temporal',
							 'Model_Base_Nestedset'
						 )
					 )
			)
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
			$migration  = \Controller_Scffld_Helper_Migration::generate(
				$orig_name,
				$subjects,
				$cmds);

			$controller = \Controller_Scffld_Helper_Controller::generate(
				$orig_name,
				$cmd_orig,
				$scfld_type,
				$scfld_model);

			$actionset  = \Controller_Scffld_Helper_Actionset::generate(
				$orig_name,
				$cmd_orig,
				$scfld_type,
				$scfld_model);

			$model      = \Controller_Scffld_Helper_Model::generate(
				$orig_name,
				$cmd_orig,
				$scfld_type,
				$scfld_model);
			if ($model == 'model_soft_error')
			{
				\Session::set_flash('error',
														'\Orm\Model_Softを使うときには、deleted_atが必須です。');
				\Response::redirect(\Uri::create('/scffld/main'));
			}

			// molding lang
			$lang       = \Controller_Scffld_Helper_Lang::generate(
				$orig_name,
				$cmd_orig);
			$fallback   = \Controller_Scffld_Helper_Lang::generate(
				$orig_name,
				$cmd_orig,
				true);

			// molding config
			$config     = \Controller_Scffld_Helper_Config::generate(
				$orig_name,
				$cmd_orig);

			// molding presenter
			$presenter_index = \Controller_Scffld_Helper_Presenter::generate(
				$orig_name,
				$scfld_type,
				'index');
			$presenter_edit  = \Controller_Scffld_Helper_Presenter::generate(
				$orig_name,
				$scfld_type,
				'edit');
			$presenter_view  = \Controller_Scffld_Helper_Presenter::generate(
				$orig_name,
				$scfld_type,
				'view');

			// molding - view
			$tpl_index       = \Controller_Scffld_Helper_Views_Index::generate(
				$orig_name,
				$cmd_orig);
			$tpl_index_admin = \Controller_Scffld_Helper_Views_Index::generate(
				$orig_name,
				$cmd_orig,
				true,
				$scfld_model);
			$tpl_view        = \Controller_Scffld_Helper_Views_View::generate(
				$orig_name,
				$cmd_orig);
			$tpl_edit        = \Controller_Scffld_Helper_Views_Edit::generate(
				$orig_name,
				$cmds);

			// molding output
			$output_controller = \Controller_Scffld_Helper_Output_Controller::generate(
				$orig_name);

			// molding format
			$format_config      = \Controller_Scffld_Helper_Format_Config::generate(
				$orig_name,
				$cmd_orig);
			$format_actionset   = \Controller_Scffld_Helper_Format_Actionset::generate(
				$orig_name);
			$format_controller  = \Controller_Scffld_Helper_Format_Controller::generate(
				$orig_name,
				$cmd_orig);
			$format_model       = \Controller_Scffld_Helper_Format_Model::generate(
				$orig_name,
				$cmd_orig);
			$format_model_table = \Controller_Scffld_Helper_Format_Model_Table::generate(
				$orig_name,
				$cmd_orig);

			// pathes
			$scfldpath        = $scfld_type == 'module' ?
												$scfldbase.'modules/'.$name.DS :
												$scfldbase;
			$viewspath        = $scfldpath.'views/';
			$migrationpath    = $scfldpath.'migrations/';
			$configpath       = $scfldpath.'config/';
			$classespath      = $scfldpath.'classes/';
			$baselangpath     = $scfldpath.'lang/';
			$langpath         = $baselangpath.\Config::get('language', 'en').DS;
			$fallbacklangpath = $baselangpath.\Config::get('language_fallback', 'en').DS;
			$controllerpath   = $classespath.'controller/';
			$modelpath        = $classespath.'model/';
			$presenterpath    = $classespath.'presenter/';
			$actionsetpath    = $classespath.'actionset/';

			// output pathes
			$outputpath           = $scfldbase.'modules/output/';
			$outputclassespath    = $outputpath.'classes/';
			$outputcontrollerpath = $outputclassespath.'controller/';

			// format pathes
			$formatpath           = $scfldbase.'modules/format/';
			$formatconfigpath     = $formatpath.'config/';
			$formatclassespath    = $formatpath.'/classes/';
			$formatactionsetpath  = $formatclassespath.'actionset/';
			$formatcontrollerpath = $formatclassespath.'controller/';
			$formatmodelpath      = $formatclassespath.'model/';
			$formatpresenterpath  = $formatclassespath.'presenter/';

			// path - module or output
			if ($scfld_type == 'module' || $is_output)
			{
				// $scfldpath
				\File::create_dir_if_not_exist($scfldbase, 'modules');
				\File::create_dir_if_not_exist($scfldbase.'/modules', $name);
			}

			// path - module
			if ($scfld_type == 'module')
			{
				// error
				if (strpos($orig_name, '_') !== false)
				{
					\Session::set_flash('error',
															'モジュール名にアンダーバーを含めないでください。');
					\Response::redirect(\Uri::create('/scffld/main'));
				}

				// $migrationpath
				\File::create_dir_if_not_exist($scfldpath, 'migrations');

				// $configpath
				\File::create_dir_if_not_exist($scfldpath, 'config');

				// $viewspath
				\File::create_dir_if_not_exist($scfldpath, 'views');
				\File::create_dir_if_not_exist($scfldpath.'views', $name);

				// $langpath
				\File::create_dir_if_not_exist($scfldpath, 'lang');
				\File::create_dir_if_not_exist(dirname($fallbacklangpath),
																			 basename($fallbacklangpath));
				\File::create_dir_if_not_exist(dirname($langpath),
																			 basename($langpath));

				// $classespath
				\File::create_dir_if_not_exist($scfldpath, 'classes');

				foreach (array('actionset', 'controller', 'model', 'presenter') as $d)
				{
					\File::create_dir_if_not_exist($classespath, $d);
				}
				\File::create_dir_if_not_exist(dirname($presenterpath), $name);
				\File::create_dir_if_not_exist($presenterpath, 'index');
				$presenteridxpath = $presenterpath.'index/';
			}

			// path - app
			if ($scfld_type == 'app' || $scfld_type == 'view' || $scfld_type == 'model')
			{
				// $classpath
				\File::create_dir_if_not_exist($scfldpath, 'classes');

				// $migrationpath
				\File::create_dir_if_not_exist($scfldpath, 'migrations');

				// $langpath
				\File::create_dir_if_not_exist($scfldpath, 'lang');
				\File::create_dir_if_not_exist(dirname($fallbacklangpath),
																			 basename($fallbacklangpath));
				\File::create_dir_if_not_exist(dirname($langpath),
																			 basename($langpath));
				foreach ($names as $p)
				{
					\File::create_dir_if_not_exist($fallbacklangpath, $p);
					\File::create_dir_if_not_exist($langpath, $p);
					$fallbacklangpath.= $p.DS;
					$langpath.= $p.DS;
				}

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

			// path - output and format
			if ($is_output)
			{
				// output pathes
				\File::create_dir_if_not_exist($outputpath);
				\File::create_dir_if_not_exist($outputclassespath);
				\File::create_dir_if_not_exist($outputcontrollerpath);
				foreach ($names as $p)
				{
					\File::create_dir_if_not_exist($outputcontrollerpath, $p);
					$outputcontrollerpath.= $p.DS;
				}

				// output
				\File::update($outputcontrollerpath, $filename, $output_controller);
				$files[] = $outputcontrollerpath.$filename;

				// format pathes
				\File::create_dir_if_not_exist($formatpath);
				\File::create_dir_if_not_exist($formatconfigpath);
				\File::create_dir_if_not_exist($formatclassespath);
				\File::create_dir_if_not_exist($formatactionsetpath);
				\File::create_dir_if_not_exist($formatcontrollerpath);
				\File::create_dir_if_not_exist($formatmodelpath);
				\File::create_dir_if_not_exist($formatpresenterpath);
				foreach ($names as $p)
				{
					\File::create_dir_if_not_exist($formatconfigpath, $p);
					\File::create_dir_if_not_exist($formatactionsetpath, $p);
					\File::create_dir_if_not_exist($formatcontrollerpath, $p);
					\File::create_dir_if_not_exist($formatmodelpath, $p);
					\File::create_dir_if_not_exist($formatpresenterpath, $p);
					$formatconfigpath.= $p.DS;
					$formatactionsetpath.= $p.DS;
					$formatcontrollerpath.= $p.DS;
					$formatmodelpath.= $p.DS;
					$formatpresenterpath.= $p.DS;
				}
				\File::create_dir_if_not_exist($formatmodelpath, $name);
				$formatmodeltablepath = $formatmodelpath.$name;

				// format
				\File::update($formatconfigpath, $filename, $format_config);
				$files[] = $formatconfigpath.$filename;

				\File::update($formatactionsetpath, $filename, $format_actionset);
				$files[] = $formatactionsetpath.$filename;

				\File::update($formatcontrollerpath, $filename, $format_controller);
				$files[] = $formatcontrollerpath.$filename;

				\File::update($formatmodelpath, $filename, $format_model);
				$files[] = $formatmodelpath.$filename;

				\File::update($formatmodeltablepath, 'table.php', $format_model_table);
				$files[] = $formatmodeltablepath.'table.php';
			}

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

			// lang
			if ($lang)
			{
				\File::update($langpath, $filename, $lang);
				$files[] = $langpath.$filename;

				// message
				$messages[] = "langのファイルを生成しました。";
			}

			// fallbacklang
			if ($fallback)
			{
				\File::update($fallbacklangpath, $filename, $fallback);
				$files[] = $fallbacklangpath.$filename;

				// message
				$messages[] = "フォールバックのlangのファイルを生成しました。";
			}

			// messages
			if ($scfld_type == 'app')
			{
				$messages[] = "migrationを調整したら、コマンドラインで";
				$messages[] = "php oil refine migrate:up";
				$messages[] = "を実行してください。";
			}

			// log
			$log_dir = APPPATH.'logs/scffld/'.$name;
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
			\Session::set_flash('error',
													'ワンタイムトークンが失効しています。送信し直してみてください。');
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
