<?php
namespace Scaffold;
class Controller_Scaffold extends \Kontiki\Controller_Crud
{
	/**
	* @var string name for human
	*/
	public static $nicename = '足場組み';

	/**
	 * set_actionset()
	 */
	public function set_actionset()
	{
		parent::set_actionset();
		require(__DIR__.'/actionset.php');
		self::$actionset = Actionset::actionItems();
		self::$actionset_owner = array();
	}

	/**
	 * action_main()
	 */
	public function action_main()
	{
		//only at development
//		if(\Fuel::$env != 'development') die();

		//call helper
		require(__DIR__.'/helper.php');
		$scaffold_helper = new \Scaffold\Helper();

		//view
		$view = \View::forge('main');

		//scaffold
		if(\Input::method() == 'POST'):
//			if( ! \Security::check_token()) die();
			$cmd = \Input::post('cmd');
			$cmd = str_replace(array('php oil g '), '', $cmd);
			$cmds = explode(' ', $cmd);

			//migration
			$type       = array_shift($cmds);
			$name       = array_shift($cmds);
			$table_name = \Inflector::pluralize($name);
			$subjects   = array($table_name, $table_name);

			//molding - logic
			$migration  = $scaffold_helper->migration($name, $subjects, $cmds);
			$controller = $scaffold_helper->generate_controller($name);
			$model      = $scaffold_helper->generate_model($name, $cmds);
			$viewmodel  = $scaffold_helper->generate_view($name);

			//molding - view
			$tpl_index  = $scaffold_helper->generate_views_index($name);
			$tpl_view   = $scaffold_helper->generate_views_view($name, $cmds);
			$tpl_form   = $scaffold_helper->generate_views_form($name, $cmds);
			$tpl_create = $scaffold_helper->generate_views_create($name);
			$tpl_edit   = $scaffold_helper->generate_views_edit($name);

			//mkdir
			$scfldpath = PKGPATH.'kontiki/modules/'.$name;
			if( ! file_exists($scfldpath)) mkdir($scfldpath);

			//put files
			$name = strtolower($name);
			if( ! file_exists($scfldpath.'/migrations')) mkdir($scfldpath.'/migrations');
			$scaffold_helper->putfiles($scfldpath.'/migrations/'.$name.'.php', $migration) ;
			if( ! file_exists($scfldpath.'/classes')) mkdir($scfldpath.'/classes');
			if( ! file_exists($scfldpath.'/classes/controller')) mkdir($scfldpath.'/classes/controller');
			$scaffold_helper->putfiles($scfldpath.'/classes/controller/'.$name.'.php', $controller) ;
			if( ! file_exists($scfldpath.'/classes/model')) mkdir($scfldpath.'/classes/model');
			$scaffold_helper->putfiles($scfldpath.'/classes/model/'.$name.'.php', $model) ;
			if( ! file_exists($scfldpath.'/classes/view')) mkdir($scfldpath.'/classes/view');
			$scaffold_helper->putfiles($scfldpath.'/classes/view/'.$name.'.php', $viewmodel) ;
			if( ! file_exists($scfldpath.'/views')) mkdir($scfldpath.'/views');
			$scaffold_helper->putfiles($scfldpath.'/views/index.php', $tpl_index) ;
			$scaffold_helper->putfiles($scfldpath.'/views/index_admin.php', $tpl_index) ;
			$scaffold_helper->putfiles($scfldpath.'/views/view.php', $tpl_view) ;
			$scaffold_helper->putfiles($scfldpath.'/views/_form.php', $tpl_form) ;
			$scaffold_helper->putfiles($scfldpath.'/views/create.php', $tpl_create) ;
			$scaffold_helper->putfiles($scfldpath.'/views/edit.php', $tpl_edit) ;

			$explanation = 'scaffoldしました。';
			$view->set('explanation', $explanation, false);
		endif;

		//view
		$view->set_global('title', '足場組み');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}


	/**
	 * action_migration()
	 */
	public function action_migration()
	{

		//view
		$view = \View::forge('migration');

		//対象モジュール
		include(PKGPATH.'kontiki/modules/post_user_r/migrations/post_user_r.php');
		$obj = new \Fuel\Migrations\Create_post_user_r;
		$obj->up();

echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
var_dump( $obj ) ;
echo '</textarea>' ;
die();


		//scaffold
		if(\Input::method() == 'POST'):
//			if( ! \Security::check_token()) die();



			$view->set('explanation', $explanation, false);
		endif;

		//view
		$view->set_global('title', '足場組み：マイグレーション');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

}
