<?php
namespace Supportcontribute;
/*abstract*/ class Controller_Supportcontribute extends \Locomo\Controller_Crud
{
	//trait
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;


	protected $pagination_config = array(
		'uri_segment' => 3,
		'num_links' => 5,
		'per_page' => 20,
		'template' => array(
			'wrapper_start' => '<div class="pagination">',
			'wrapper_end' => '</div>',
			'active_start' => '<span class="current">',
			'active_end' => '</span>',
		),
	);


	public function action_home() {
		$menus = array();
		$menus[\Html::anchor('support/home', '寄付情報')] = '寄附金の登録や礼状印刷を行います';
		$menus[\Html::anchor('support/index', '寄付情報一覧')] = '■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ';
		$menus[\Html::anchor('contribute/home', '後援会情報')] = '■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ';
		$menus[\Html::anchor('contribute/index', '後援会情報一覧')] = '■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ';
		$menus[\Html::anchor('supportcontribute/summary', '寄附金・後援会入金集計')] = '■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ';
		$view = \View::forge(dirname(dirname(__DIR__)) . '/views/home.php');
		$view->set_safe('menus', $menus);
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}


	/*
	 * index_admin
	 */
	public function action_index_admin() {


		$model = $this->model_name;

		$this->_template = 'templates/src';

		// 検索の整形
		$options = array();
		if (\Input::get('summary')) {
			$date = \Input::get('date') ?: date('Y-m-d H:i:s');

			switch(\Input::get('summary')) {
				case 'daily':
					$model::$_conditions['where'][] = array('receipt_at', 'like', date('Y-m-d', strtotime($date)) . '%');
					break;
				case 'monthly':
					$model::$_conditions['where'][] = array('receipt_at', 'like', date('Y-m', strtotime($date)) . '%');
					break;
				case 'yearly':
					//var_dump(\Util::get_fiscal($date)); die();
					$model::$_conditions['where'][] = array('receipt_at', 'between', \Util::get_fiscal($date));
					break;
				default:
					break;
			}
		}
		if (\Input::get('is_support_money')) $model::$_conditions['where'][] = array('support_money', '>', 0);

		// 検索用form
		$src = \Fieldset::forge('src');
		$src->add('summary', '科目', array(
			'type' => 'select',
			'options' => array(
				'' => ' - 絞り込みなし - ',
				'daily' => '日計',
				'monthly' => '月計',
				'yearly' => '年度計',
			),
			'value' => \Input::get('summary'),
		));
		$src->add('date', '日付', array(
			'value' => \Input::get('date'),
			'class' => 'datetime',
			'placeholder' => date('Y-m') . '未入力で現在の日付を使用します',
		));
		$subject_model = \Inflector::get_namespace(get_called_class()) . 'Model_Subject';
		$src->add('searches[subject_id]', '科目', array(
			'type' => 'select',
			'options' => array_merge(array('' => '') , $subject_model::get_options(array(), 'name')),
			'value' => \Input::get('searches')['subject_id'],
		));
		$src->add('searches[support_aim]', '目的', array(
			'type' => 'select',
			'options' => array_merge(array('' => '') , $model::$_type_config['support_aim']),
			'value' => \Input::get('searches')['support_aim']
		));
		$src->add('searches[consignee_type]', '受け取り方法', array(
			'type' => 'select',
			'options' => array_merge(array('' => '') , $model::$_type_config['consignee_type']),
			'value' => \Input::get('searches')['consignee_type'],
		));
		$src->add('is_support_money', '寄付金額0円を除外する', array(
			'type' => 'checkbox',
			'value' => 1,
			'checked' => \Input::get('is_support_money'),
		));//->set_value(\Input::get('is_support_money'));
		$src->add('submit', '検索', array('type' => 'submit', 'value' => '検索', 'class' => 'button primary'));

		$src->set_config('form_method', 'get');
		$src->set_config('form_attributes', array('class' => ' table table-striped'));

		$this->template->set_safe('src', $src);

		parent::action_index_admin();
		$controller = strtolower(str_replace('Controller_', '', substr(\Request::main()->controller, -strrpos(\Request::main()->controller, '\\'))));

		$this->template->content->set('controller', $controller);
	}


	/*
	 * action_custoemr
	 * 新規寄付登録 顧客検索
	 */
	public function action_customer() {

		$this->_template = 'templates/src';

		// 検索用form
		$src = \Fieldset::forge('src');

		$src->add('searches[id]', '顧客ID', array('type' => 'text'));
		$src->add('likes[name]', '顧客名', array('type' => 'text'));
		$src->add('likes[kana]', '顧客フリガナ', array('type' => 'text'));
		$src->add('searches[address]', '顧客住所', array('type' => 'text'));
		$src->add('searches[tel]', '電話番号', array('type' => 'text'));
		$src->add('searches[type]', '会員区分', array('type' => 'text')); // todo Model_Customer 決まり次第

		$src->add('submit', '検索', array('type' => 'submit', 'value' => '検索', 'class' => 'button primary'));

		$src->set_config('form_method', 'get');
		$src->set_config('form_attributes', array('class' => ' table table-striped'));

		$this->template->set_safe('src', $src);

		\Module::load('customer');
		$view = \View::forge('customer');

		$view->set('items', \Customer\Model_Customer::paginated_find());
		
		$controller = strtolower(str_replace('Controller_', '', substr(\Request::main()->controller, -strrpos(\Request::main()->controller, '\\'))));
		$controller == 'support' ? $view->set_global('title', '寄付者選択') : $view->set_global('title', '後援会員選択') ;


		$view->set('controller', $controller);
		$view->base_assign();
		$this->template->content = $view;
	}



	/*
	 * action_custoemr
	 * 寄付登録・編集 (顧客検索後)
	 */
	public function action_edit($id = null) {
		// crud
		$model = $this->model_name ;

		$pagination_config = $this->pagination_config;
		$pagination_config['per_page'] = 10000; // 無限に出来ないの? null?
		$options = array(
			'where' => array(
				array('customer_id', '=', $id),
			),
		);
		// termplate history
		$histories = $model::find('all', $options);

		if ($id) {
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));
			if( ! $obj){
				$page = \Request::forge('content/403')->execute();
				return new \Response($page, 403);
			}
			$title = '#' . $id . ' ' . self::$nicename . '編集';
		} else {
			$obj = $model::forge();
			$title = self::$nicename . '新規作成';
		}
		$form = $model::form_definition('edit', $obj);

		//
		/*
		 * save
		 */
		if (\Input::post()) :
			if (
				$obj->cascade_set(\Input::post(), $form, $repopulate = true) &&
				 \Security::check_token()
			):
			//save
				if ($obj->save(null, true)):
					//success
					\Session::set_flash(
						'success',
						sprintf('%1$sの #%2$d を更新しました', self::$nicename, $obj->id)
					);
					\Response::redirect(\Uri::create($this->request->module.'/edit/'.$obj->id));
				else:
					//save failed
					\Session::set_flash(
						'error',
						sprintf('%1$sの #%2$d を更新できませんでした', self::$nicename, $id)
					);
				endif;
			else:
				//edit view or validation failed of CSRF suspected
				if (\Input::method() == 'POST'):
					\Session::set_flash('error', $form->error());
				endif;
			endif;
		endif;

		//set _single_item
		$this->_single_item = $obj;

		//view
		$view = \View::forge('edit');
		$view->set_global('title', $title);
		$view->set_global('item', $this->_single_item, false);
		$view->set_global('form', $form, false);

		$view->set('customer', $obj->customer);
		if (is_null($obj->customer) and \Input::get('customer_id')) {
			$view->set('customer', \Customer\Model_Customer::find(\Input::get('customer_id')));
		}
		$view->set('histories', $histories);

		$view->base_assign();
		$this->template->content = $view;
	}


	/*
	 * 礼状印刷
	 * 検索画面
	 */
	public function action_letter() {

		//$this->template = 'index_src';
		\Pagination::set_config('per_page', PHP_INT_MAX);

		$model = $this->model_name;
		$view = \View::forge('letter');
		$controller = strtolower(str_replace('Controller_', '', substr(\Request::main()->controller, -strrpos(\Request::main()->controller, '\\'))));


		// todo 初期値を 0件 に
		$date = \Input::get('date') ?: date('Y-m-d H:i:s');
		$options['related']['customer']['join_type'] = 'inner';
		switch(\Input::get('summary')) {
			case 'daily':
				$model::$_conditions['where'][] = array('receipt_at', 'like', date('Y-m-d', strtotime($date)) . '%');
				break;
			case 'monthly':
				$model::$_conditions['where'][] = array('receipt_at', 'like', date('Y-m', strtotime($date)) . '%');
				break;
			default:
				break;
		}
		if (\Input::get('id')) $options['related']['customer']['where'][] = array(array('id', '=', \Input::get('id')));
		if (\Input::get('name')) $options['related']['customer']['where'][] = array(array('name', 'like', '%' . \Input::get('name') . '%'));
		if (\Input::get('kana')) $options['related']['customer']['where'][] = array(array('kana', 'like', '%' . \Input::get('kana') . '%'));
		if (\Input::get('address')) $options['related']['customer']['where'][] = array(array('address', 'like', '%' . \Input::get('address') . '%'));
		if (\Input::get('tel')) $options['related']['customer']['where'][] = array(array('tel', 'like', '%' . \Input::get('tel') . '%'));

		$view->set('items',  $model::find('all', $options));

		// save
		if (\Input::post('submit')) {
		}

		// 全選択, 解除
		if (\Input::post('checked_all')) {
			$view->set('checked_all', true);
		} else {
			$view->set('checked_all', false);
		}


		$view->base_assign();
		$view->set_global('title', static::$nicename);
		$this->template->content = $view;
	}

	/*
	 * 物品寄付集計
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

			$result = \DB::select(\DB::expr('article_delivery_type, SUM(support_money) AS sum, COUNT(id) AS cnt'))
				->from($table)
				->group_by('article_delivery_type')
				->where('receipt_at', 'like', $_year . '-' . $_month . '%')
				->where('support_article', '!=', '')
				->execute()->as_array();

			$monthly[$_month]['cnt'] = \Arr::assoc_to_keyval($result, 'article_delivery_type', 'cnt');
			$monthly[$_month]['sum'] = \Arr::assoc_to_keyval($result, 'article_delivery_type', 'sum');
			$monthly[$_month]['cnt_total'] = \Arr::sum($result, 'cnt');
			$monthly[$_month]['sum_total'] = \Arr::sum($result, 'sum');

		}

		// var_dump($monthly);
		// 一年分合計
		$result = \DB::select(\DB::expr('article_delivery_type, SUM(support_money) AS sum, COUNT(id) AS cnt'))
			->from($table)
			->group_by('article_delivery_type')
			->where('receipt_at', 'BETWEEN', \Util::get_fiscal($year))
			->where('support_article', '!=', '')
			->execute()->as_array();

		$total['cnt'] = \Arr::assoc_to_keyval($result, 'article_delivery_type', 'cnt');
		$total['sum'] = \Arr::assoc_to_keyval($result, 'article_delivery_type', 'sum');
		$total['cnt_total'] = \Arr::sum($result, 'cnt');
		$total['sum_total'] = \Arr::sum($result, 'sum');


		$view->set('title', $year . '年度 寄付金集計');
		$view->set('year', $year);
		$view->set('monthly', $monthly);
		$view->set('total', $total);
		$view->set('table_keys', $model::$_type_config['article_delivery_type']);


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
