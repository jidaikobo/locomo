<?php
namespace Office;
class Controller_Support extends \Office\Controller_Office
{
	// change views dir
	protected static $views_path_module = 'supportcontribute';

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

		$view = \View::forge('support/summary');
		$model = $this->model_name;
		$table = $model::table();
		$subject_table = Model_Subject::table();
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

		//var_dump($subject_table); die();
		$result = \DB::select(\DB::expr('subject_id, SUM(support_money) AS sum, COUNT(id) AS cnt'))
			->from($table)
			->where('receipt_at', 'like', $_year . '-' . $_month . '%')
			->where('is_support', true)->where('support_article', '=', '')
			->group_by('subject_id')
			->execute()->as_array();
			$monthly[$_month]['cnt'] = \Arr::assoc_to_keyval($result, 'subject_id', 'cnt');
			$monthly[$_month]['sum'] = \Arr::assoc_to_keyval($result, 'subject_id', 'sum');
			$monthly[$_month]['cnt_total'] = \Arr::sum($result, 'cnt');
			$monthly[$_month]['sum_total'] = \Arr::sum($result, 'sum');
		}

		$result = \DB::select(\DB::expr('subject_id, SUM(support_money) AS sum, COUNT(id) AS cnt'))
			->from($table)
			->group_by('subject_id')
			->where('receipt_at', 'BETWEEN', \Util::get_fiscal($year))
			->where('is_support', true)->where('support_article', '=', '')
			->execute()->as_array();

		$total['cnt'] = \Arr::assoc_to_keyval($result, 'subject_id', 'cnt');
		$total['sum'] = \Arr::assoc_to_keyval($result, 'subject_id', 'sum');
		$total['cnt_total'] = \Arr::sum($result, 'cnt');
		$total['sum_total'] = \Arr::sum($result, 'sum');


		$view->set('title', $year . '後援会集計');
		$view->set('year', $year);
		$view->set('monthly', $monthly);
		$view->set('total', $total);
		$view->set('table_keys', Model_Subject::get_options(array(), 'name'));



		$view->set_global('title', self::$nicename);
		$view->base_assign();

		$this->template->content = $view;

	}


}
