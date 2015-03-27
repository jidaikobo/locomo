<?php
namespace Locomo;
class Actionset_Usrgrp extends \Actionset
{
	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
			'\Controller_Usrgrp/index_admin',
			'\Controller_Usrgrp/create',
			'\Controller_Usrgrp/edit',
			'\Controller_Usrgrp/view',
			'\Controller_Usrgrp/delete',
			'\Controller_Usrgrp/undelete',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		\Arr::set($retvals, 'action_name', 'ユーザグループへのアクセス権');
		\Arr::set($retvals, 'acl_exp', 'ユーザグループへのアクセス権です。');
		return $retvals;
	}

	/**
	 * actionset_index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => array(array('usrgrp/index_admin?create=1', '新規作成')) ,
			'action_name'  => '新規作成',
			'show_at_top'  => true,
			'explanation'  => 'ユーザグループを新規作成します。',
			'order'        => 10,
		);
		return $retvals;
	}

	/**
	 * actionset_index_revision()
	 */
	public static function actionset_index_revision($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => array(array('usrgrp/index_revision', '履歴')) ,
			'action_name'  => '履歴',
			'show_at_top'  => true,
			'explanation'  => 'ユーザグループの編集履歴です。',
			'order'        => 15,
		);
		return $retvals;
	}
}
