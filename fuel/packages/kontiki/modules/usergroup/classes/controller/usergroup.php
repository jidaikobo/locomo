<?php
namespace Kontiki;
abstract class Controller_Usergroup_Abstract extends \Kontiki\Controller
{
	/**
	* @var string name for human
	*/
	public static $nicename = 'ユーザグループ管理';

	/**
	 * messages
	 * 
	 */
	protected $messages = array(
		'auth_error'       => 'You are not permitted.',
		'view_error'       => 'ユーザグループID %2$d は見つかりませんでした。',
		'create_success'   => 'ユーザグループID %2$d を新規作成しました。',
		'create_error'     => 'Could not save %s.',
		'edit_success'     => 'Updated %s #%d.',
		'edit_error'       => 'Could not update %s #%d.',
		'delete_success'   => 'Deleted %s #%d.',
		'delete_error'     => 'Could not delete %s #%d.',
		'undelete_success' => 'Undeleted %s #%d.',
		'undelete_error'   => 'Could not undelete %s #%d.',
		'purge_success'    => 'Completely deleted %s #%d.',
		'purge_error'      => 'Could not delete %s #%d.',
	);

	/**
	 * page titles
	 * 
	 */
	protected $titles = array(
		'index'          => '%1$s.',
		'view'           => '%1$s.',
		'create'         => 'Create %1$s.',
		'edit'           => 'Edit %1$s.',
		'index_deleted'  => 'Delete List %1$s.',
		'view_deleted'   => 'Deleted %1$s.',
		'edit_deleted'   => 'Edit Deleted %1$s.',
		'confirm_delete' => 'Are you sure to Permanently Delete a %1$s?',
		'delete_deleted' => 'Completely Delete a %1$s.',
	);

	/**
	 * test datas
	 * 
	 */
	protected $test_datas = array(
		'usergroup_name' => 'text',
	);

	/**
	 * action_add_testdata()
	 * 
	 */
	public function action_add_testdata($num = 10)
	{
		parent::action_add_testdata($num);
	}
}
