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

//Autoloader::add_namespace()
Autoloader::add_namespace('Kontiki', PKGPATH.'kontiki/');

Autoloader::add_classes(array(
	//base abstract classes
	'Kontiki\Controller'          => __DIR__.'/classes/abstract/controller.php',
	'Kontiki\Controller_Workflow' => __DIR__.'/classes/abstract/controller_workflow.php',
	'Kontiki\Controller_Crud'     => __DIR__.'/classes/abstract/controller_crud.php',
	'Kontiki\Model'               => __DIR__.'/classes/abstract/model.php',
	'Kontiki\ViewModel'           => __DIR__.'/classes/abstract/view.php',

	//base abstract modules
	//content
	'Kontiki\Controller_Content_Abstract' => __DIR__.'/modules_abstract/content/classes/controller/Content.php',
	'Kontiki\Model_Content_Abstract'      => __DIR__.'/modules_abstract/content/classes/model/Content.php',
	'Kontiki\View_Content_Abstract'       => __DIR__.'/modules_abstract/content/classes/view/Content.php',

	//user
	'Kontiki\Controller_User_Abstract' => __DIR__.'/modules_abstract/user/classes/controller/user.php',
	'Kontiki\Model_User_Abstract'      => __DIR__.'/modules_abstract/user/classes/model/user.php',
	'Kontiki\View_User_Abstract'       => __DIR__.'/modules_abstract/user/classes/view/user.php',

	//usergroup
	'Kontiki\Controller_Usergroup_Abstract' => __DIR__.'/modules_abstract/usergroup/classes/controller/usergroup.php',
	'Kontiki\Model_Usergroup_Abstract'      => __DIR__.'/modules_abstract/usergroup/classes/model/usergroup.php',
	'Kontiki\View_Usergroup_Abstract'       => __DIR__.'/modules_abstract/usergroup/classes/view/usergroup.php',

	//acl
	'Kontiki\Controller_Acl_Abstract' => __DIR__.'/modules_abstract/acl/classes/controller/acl.php',
	'Kontiki\Model_Acl_Abstract'      => __DIR__.'/modules_abstract/acl/classes/model/acl.php',
	'Kontiki\View_Acl_Abstract'       => __DIR__.'/modules_abstract/acl/classes/view/acl.php',

	//revision
	'Kontiki\Controller_Revision_Abstract' => __DIR__.'/modules_abstract/revision/classes/controller/revision.php',
	'Kontiki\Model_Revision_Abstract'      => __DIR__.'/modules_abstract/revision/classes/model/revision.php',
	'Kontiki\View_Revision_Abstract'       => __DIR__.'/modules_abstract/revision/classes/view/revision.php',

	//workflow
	'Kontiki\Controller_Workflow_Abstract' => __DIR__.'/modules_abstract/workflow/classes/controller/workflow.php',
	'Kontiki\Model_Workflow_Abstract'      => __DIR__.'/modules_abstract/workflow/classes/model/workflow.php',
	'Kontiki\View_Workflow_Abstract'       => __DIR__.'/modules_abstract/workflow/classes/view/workflow.php',

	//validation
	'Kontiki\Validation' => __DIR__.'/classes/validation.php',

	//actionset
	'Kontiki\Actionset' => __DIR__.'/classes/actionset.php',
	'Kontiki\Actionset_Owner' => __DIR__.'/classes/actionset_owner.php',
));

// Register the autoloader
Autoloader::register();
Autoloader::add_namespace('Kontiki_Observer', __DIR__.'/classes/observers/');

// load  the package with the config file.
if(file_exists(PKGPATH.'kontiki/config/packageconfig.php')):
	Config::load('packageconfig.php');
else:
	Config::load('packageconfig.default.php');
endif;

/* End of file bootstrap.php */
