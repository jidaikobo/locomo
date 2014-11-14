<?php
namespace Office;
class Controller_Donate extends \Office\Controller_Office
{

	public function action_home() {
		$menus = array();
		$menus[\Html::anchor('support/index', '寄付情報一覧')] = '■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ';
		$menus[\Html::anchor('support/customer',  '寄付登録')] = '■ ■ ■ ■ ■ ■ ■ ■ ■ ■';
		$menus[\Html::anchor('support/home', '礼状印刷')] = '■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ';
		$menus[\Html::anchor('support/index', '寄付情報編集')] = '■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ';
		$view = \View::forge('home');
		$view->set_safe('menus', $menus);
		$view->set_global('title', self::$nicename);
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}


	/*
	 * 寄付金集計
	 * controller で振り分ける
	 * support_contribute => 
	 * support =>
	 * contribute =>
	 */
	public function action_summary() {

		if (\Request::is_hmvc()) {
			$view = \View::forge('support/summary');
		} else {
			$view = \View::forge('support/summary');
		}

		$model = $this->model_name;
		$year = \Input::get('year') ?: date('Y');


		$table = $model::table();

		for ($i = 4; $i < 16; $i++) {

			if ($i > 12) {
				$_month = sprintf('%02d', $i%12);
				$_year = $year + 1;
			} else {
				$_month = sprintf('%02d', $i);
				$_year = $year;
			}

			$result = \DB::select(\DB::expr('support_type, SUM(support_money) AS sum, COUNT(id) AS cnt'))
				->from($table)
				->group_by('support_type')
				->where('receipt_at', 'like', $_year . '-' . $_month . '%')
				->execute()->as_array();

			$monthly[$_month]['cnt'] = \Arr::assoc_to_keyval($result, 'support_type', 'cnt');
			$monthly[$_month]['sum'] = \Arr::assoc_to_keyval($result, 'support_type', 'sum');
			$monthly[$_month]['cnt_total'] = \Arr::sum($result, 'cnt');
			$monthly[$_month]['sum_total'] = \Arr::sum($result, 'sum');

		}

		// var_dump($monthly);
		// 一年分合計
		$result = \DB::select(\DB::expr('support_type, SUM(support_money) AS sum, COUNT(id) AS cnt'))
			->from($table)
			->group_by('support_type')
			->where('receipt_at', 'BETWEEN', \Util::get_fiscal($year))
			->execute()->as_array();

		$total['cnt'] = \Arr::assoc_to_keyval($result, 'support_type', 'cnt');
		$total['sum'] = \Arr::assoc_to_keyval($result, 'support_type', 'sum');
		$total['cnt_total'] = \Arr::sum($result, 'cnt');
		$total['sum_total'] = \Arr::sum($result, 'sum');


		$view->set('title', $year . '年度 寄付金集計');
		$view->set('year', $year);
		$view->set('monthly', $monthly);
		$view->set('total', $total);
		$view->set('table_keys', $model::$_type_config['support_type']);


		//$results$model::query()
		//var_dump($results);

		$view->set_global('title', self::$nicename);
		$view->base_assign();

		if (\Request::is_hmvc()) {
			\Fuel::$profiling = false;
			return (string)$view;
		}

		$this->template->content = $view;
	}




}




		/*
		$model::query()
		->where('receipt_at', 'BETWEEN', \Util::get_fiscal($date));
		 */

		//$sum_q = \DB::expr('SUM(support_money) AS sum');
		//$cnt_q = \DB::expr('COUNT(id) AS cnt');
		// var_dump($sum_q); die();
		/*
		$options = array(
			'select' => array(array(\DB::expr('SUM(support_money) AS sum, support_type'))),
			'group_by' => array('support_type'),
			'where' => array(
				array('receipt_at', 'BETWEEN', \Util::get_fiscal($date)),
			),
		);
		$results = $model::find('all', $options);

		 */




			/*
			$result = \DB::select(\DB::expr('support_type, SUM(support_money) AS sum, COUNT(id) AS cnt'))
				->from($table)
				->group_by('support_type')
				->where('receipt_at', 'like', $_year . '-' . $month . '%')
				->execute()->as_array();
			$arr = array();
			foreach($result as $v) {
				$arr[$v['support_type']] = $v;
			}
			$monthly[$month] = $arr;
			 */

		/*
		$results = \DB::select(\DB::expr('support_type, SUM(support_money) AS sum'))
			->from($table)
			->group_by('support_type')
			->where()
			->execute()->as_array();
		 */

