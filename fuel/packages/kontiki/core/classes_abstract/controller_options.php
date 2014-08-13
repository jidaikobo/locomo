<?php
namespace Kontiki;
abstract class Controller_Options_Abstract extends \Kontiki\Controller
{
	/**
	 * action_options()
	 */
	public function action_options($optname = null)
	{
		is_null($optname) and die();


echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
var_dump( $optname ) ;
echo '</textarea>' ;
die();







		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		if ( ! $data['item'] = $model::find_item($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], self::$nicename, $id)
			);
			\Response::redirect($this->request->module);
		endif;

		//view
		$view = \View::forge('view');
		$view->set_global('item', $data['item']);
		$view->set_global('title', sprintf($this->titles['view'], self::$nicename));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}
}
