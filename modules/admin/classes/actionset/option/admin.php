<?php
namespace Admin;
class Actionset_Option_Admin extends \Actionset
{
	/**
	 * actionset_edit_dashboard()
	 * @return  array
	 */
	public static function actionset_edit_dashboard($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'  => array(\Html::anchor('/admin/admin/edit/'.\Auth::get('id'), 'ダッシュボードの編集')) ,
			'order' => 10,
		);
		return $retvals;
	}
}
