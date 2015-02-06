<?php
namespace Locomo;
class Model_Flr_Usergroup extends \Model_Base
{
	/**
	 * vals
	 */
	protected static $_table_name = 'lcm_flr_permissions';

	// $_conditions
	protected static $_conditions = array(
		'where' => array(
			array('user_id', 'is', null)
		)
	);
	public static $_options = array();

	/**
	 * $_properties
	 */
	public static $_properties = array(
		'id',
		'flr_id' => array(
			'form' => array (
				'type' => 'hidden',
			)
		),
		'usergroup_id' => array (
			'label' => 'ユーザグループ',
			'data_type' => 'int',
			'form' => array (), // override by \Locomo\Model_Flr::form_definition() & Model_Flr_Dir::form_definition()
		),
		'access_level' => array(
			'label' => 'ディレクトリの権限',
			'form' => array(
				'type' => 'select',
				'options' => array(
					''  => '権限を選択してください',
					'1' => '閲覧／ダウンロード権限',
					'2' => 'アップロード／ファイル削除権限',
					'3' => 'ディレクトリ作成権限',
					'4' => 'ディレクトリ名称変更／移動権限',
					'5' => 'ディレクトリ削除権限',
				),
			),
		),
	);

}
