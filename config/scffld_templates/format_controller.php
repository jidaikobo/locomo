<?php
namespace Format;
class Controller_XXX extends \Locomo\Controller_Frmt
{
	public $model_name = '\Format\Model_XXX';

	public $output_url = 'output/xxx/output';

	// locomo
	public static $locomo = array(
		'nicename'                => '###name###印刷 データ設定', // for human's name
		'explanation'             => '###name###印刷 データ設定をします',
		'main_action'             => 'index_admin', // main action
		'main_action_name'        => '###name###印刷フォーマット一覧',
		'main_action_explanation' => '###name###印刷フォーマットを管理します。',
		'show_at_menu'            => true,  // true: show at admin bar and admin/home
		'is_for_admin'            => false, // true: hide from admin bar
		'order'                   => 100,   // order of appearance
	);
}
