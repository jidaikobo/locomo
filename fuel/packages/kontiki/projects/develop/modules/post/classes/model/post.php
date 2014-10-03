<?php
namespace Post;
class Model_Post extends \Kontiki\Model_Crud
{
	/**
	 * values
	 */
	protected static $_table_name = 'posts';
	protected static $_primary_name = 'title';

	protected static $_properties = array(
		'id',
		'title',
		'body',
		'user_id',
		'workflow_status',
	);

	/**
	 * find_item()
	 */
	public static function find_item($id = null)
	{
		//parent
		$item = parent::find_item($id);

		//オプションの取得
		if($item):
			$item->postcategories = \Post\Model_Post::get_selected_options('postcategories', $id);
		endif;

		return $item;
	}
}