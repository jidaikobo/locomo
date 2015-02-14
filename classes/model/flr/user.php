<?php
namespace Locomo;
class Model_Flr_User extends \Model_Base
{
	/**
	 * vals
	 */
	protected static $_table_name = 'lcm_flr_permissions';

	// $_conditions
	protected static $_conditions = array(
		'where' => array(
			array('usergroup_id', 'is', null)
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
		'user_id' => array (
			'label' => 'ユーザ',
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
			'default' => 0,
		),
	);

	/**
	 * $_has_one
	 */
	protected static $_has_one = array(
		'usr' => array(
			'key_from' => 'user_id',
			'model_to' => '\Model_Usr',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);
}
