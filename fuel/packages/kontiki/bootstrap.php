<?php
/**
 * Kontiki
 *
 * @package    Kontiki
 * @version    1
 * @author     shibata@jidaikobo.com
 * @license    MIT License
 * @copyright  2014 jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

//Autoloader - kontiki
Autoloader::add_namespace('Kontiki', PKGPATH.'kontiki/');

//Autoloader - classes
$classes = array(
	'Controller',
	'Controller_Crud',
	'Model',
	'View',
	'Util',
	'Validation',
	'Actionset',
	'Actionset_Owner',
);
$class_names = array();
foreach($classes as $class):
	$l_class = strtolower($class);
	//abstract
	$class_names["Kontiki\\{$class}_Abstract"] = __DIR__."/classes_abstract/{$l_class}.php";
	//override
	$dirname = file_exists(PKGPATH."kontiki/classes/{$l_class}.php") ? 'classes' : 'classes_default';
	$class_names["Kontiki\\{$class}"] = __DIR__."/{$dirname}/{$l_class}.php";
endforeach;
Autoloader::add_classes($class_names);

//Autoloader - modules
Autoloader::add_classes(array(
	//base abstract classes
	'Kontiki\Controller_Abstract'      => __DIR__.'/classes_abstract/controller.php',
	'Kontiki\Controller_Crud_Abstract' => __DIR__.'/classes_abstract/controller_crud.php',
	'Kontiki\Model_Abstract'           => __DIR__.'/classes_abstract/model.php',
	'Kontiki\ViewModel_Abstract'       => __DIR__.'/classes_abstract/view.php',
	'Kontiki\Util_Abstract'            => __DIR__.'/classes_abstract/util.php',
	'Kontiki\Validation_Abstract'      => __DIR__.'/classes_abstract/validation.php',
	'Kontiki\Actionset_Abstract'       => __DIR__.'/classes_abstract/actionset.php',
	'Kontiki\Actionset_Owner_Abstract' => __DIR__.'/classes_abstract/actionset_owner.php',

	//base abstract modules
	//content
	'Kontiki\Controller_Content' => __DIR__.'/modules_default/content/classes/controller/content_abstract.php',
	'Kontiki\Model_Content'      => __DIR__.'/modules_default/content/classes/model/content_abstract.php',
	'Kontiki\View_Content'       => __DIR__.'/modules_default/content/classes/view/content_abstract.php',

	//user
	'Kontiki\Controller_User' => __DIR__.'/modules_default/user/classes/controller/user_abstract.php',
	'Kontiki\Model_User'      => __DIR__.'/modules_default/user/classes/model/user_abstract.php',
	'Kontiki\View_User'       => __DIR__.'/modules_default/user/classes/view/user_abstract.php',

	//usergroup
	'Kontiki\Controller_Usergroup' => __DIR__.'/modules_default/usergroup/classes/controller/usergroup_abstract.php',
	'Kontiki\Model_Usergroup'      => __DIR__.'/modules_default/usergroup/classes/model/usergroup_abstract.php',
	'Kontiki\View_Usergroup'       => __DIR__.'/modules_default/usergroup/classes/view/usergroup_abstract.php',

	//acl
	'Kontiki\Controller_Acl' => __DIR__.'/modules_default/acl/classes/controller/acl_abstract.php',
	'Kontiki\Model_Acl'      => __DIR__.'/modules_default/acl/classes/model/acl_abstract.php',
	'Kontiki\View_Acl'       => __DIR__.'/modules_default/acl/classes/view/acl_abstract.php',

	//acl
	'Kontiki\Controller_Scaffold' => __DIR__.'/modules_default/scaffold/classes/controller/scaffold_abstract.php',
	'Kontiki\Model_Scaffold'      => __DIR__.'/modules_default/scaffold/classes/model/scaffold_abstract.php',
	'Kontiki\View_Scaffold'       => __DIR__.'/modules_default/scaffold/classes/view/scaffold_abstract.php',

	//revision
	'Kontiki\Controller_Revision' => __DIR__.'/modules_default/revision/classes/controller/revision_abstract.php',
	'Kontiki\Model_Revision'      => __DIR__.'/modules_default/revision/classes/model/revision_abstract.php',
	'Kontiki\View_Revision'       => __DIR__.'/modules_default/revision/classes/view/revision_abstract.php',

	//workflowadmin
	'Kontiki\Controller_Workflowadmin' => __DIR__.'/modules_default/workflowadmin/classes/controller/workflowadmin_abstract.php',
	'Kontiki\Model_Workflowadmin'      => __DIR__.'/modules_default/workflowadmin/classes/model/workflowadmin_abstract.php',
	'Kontiki\View_Workflowadmin'       => __DIR__.'/modules_default/workflowadmin/classes/view/workflowadmin_abstract.php',

	//workflow
	'Kontiki\Controller_Workflow' => __DIR__.'/modules_default/workflow/classes/controller/workflow_abstract.php',
	'Kontiki\Model_Workflow'      => __DIR__.'/modules_default/workflow/classes/model/workflow_abstract.php',
	'Kontiki\View_Workflow'       => __DIR__.'/modules_default/workflow/classes/view/workflow_abstract.php',
));

// Register the autoloader
Autoloader::register();
Autoloader::add_namespace('Kontiki_Observer', __DIR__.'/observers/');

// load the package with the config file.
if(file_exists(PKGPATH.'kontiki/config/packageconfig.php')):
	Config::load('packageconfig.php');
else:
	Config::load('packageconfig.default.php');
endif;

//always load module
\Module::load('acl');
\Module::load('user');
\Module::load('usergroup');
\Module::load('revision');
\Module::load('workflow');

/* End of file bootstrap.php */
