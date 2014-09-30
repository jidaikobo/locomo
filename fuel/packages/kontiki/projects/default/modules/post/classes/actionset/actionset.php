<?php
namespace Post;
class Actionset_Post extends \Kontiki\Actionset
{
	use \Revision\Actionset_Revision;
	use \Workflow\Actionset_Workflow;

	/**
	 * actionItems()
	 * @return  obj
	 */
	public static function actionItems($controller = null, $item = null)
	{
		$actions = parent::actionItems($controller, $item);
		$actions->postcategories = self::postcategories($controller, $item);

		//revision
		$actions->view_revision = self::view_revision($controller, $item);

		//workflow
		$actions->index_workflow   = self::index_workflow($controller, $item);
		$actions->workflow         = self::workflow($controller, $item);
		$actions->workflow_process = self::workflow_process($controller, $item);
		$actions->workflow_actions = self::workflow_actions($controller, $item);
		if(@$item->workflow_status == 'in_progress') unset($actions->edit);

		return $actions;
	}

	/**
	 * postcategories()
	 * @return  array
	 */
	private static function postcategories($controller, $item)
	{
		$url = parent::check_auth($controller, 'postcategories') ? "{$controller}/options/postcategories" : '';
		$url_rev = $url ? "{$controller}/options_revisions/postcategories" : '';
		$urls = array(
			array('カテゴリ設定', $url),
			array('カテゴリ設定履歴', $url_rev),
		);

		$retvals = array(
			'is_admin_only' => false,
			'is_index'      => true,
			'url'           => $urls,
			'id_segment'    => null,
			'action_name'   => 'カテゴリ',
			'explanation'   => 'postコントローラのカテゴリです。',
			'menu_str'      => '',
			'dependencies' => array(
				'options/postcategories',
				'options_revisions/postcategories',
			)
		);
		return $retvals;
	}
}
