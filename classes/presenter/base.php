<?php
namespace Locomo;
class Presenter_Base extends \Presenter
{
	/**
	 * plain()
	 * @return obj instanceof \Form
	 */
	public static function plain($obj = null)
	{
		return static::form($obj)->build_plain();
	}

	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		// forge
		$form = \Fieldset::forge(\Request::active()->action);

		// populate
		$form->add_model($obj)->populate($obj, true);

		// submit
		$form->add('submit', '', array('type' => 'submit', 'value' => '保存', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}</div>');;

		return $form;
	}

	/**
	 * search_form()
	 * @return obj instanceof \Form
	 */
	public static function search_form($title)
	{
		// forge
		$form_name = \Request::active()->action.'_search_form';
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge($form_name, $config);

		// add opener before unrefine
		\Pagination::set_config('sort_info_model', get_called_class());
		$sortinfo     = \Pagination::sort_info();
		$total        = \Pagination::get("total_items");
		$current_page = \Pagination::get("current_page");
		$per_page     = \Pagination::get("per_page");
		$refined      = \Pagination::$refined_items;

		// search_form() require $refined
		if ($refined === false) throw new \Exception("search_form()を使う場合は、当該コントローラでfind('all')の結果をcount()して\Paginationに渡してください。例）\Pagination::\$refined_items = count(\$items);");

		// from and to
		$from = $current_page == 1 ? 1 : ($current_page - 1) * $per_page + 1;
		$to   = $refined <= $per_page ? $from + $refined - 1 : $from + $per_page - 1;

		// information
		$pagenate_txt = ($per_page < $total) ? number_format($from).'から'.number_format($to).'件目 / ' : '';
		$sortinfo_txt = "{$sortinfo} <span class=\"nowrap\">{$pagenate_txt}全".number_format($total)."件</span>";
		$sortinfo     = $total ? $sortinfo_txt : '項目が存在しません' ;

		// form
		$form
			->add('opener','',array('type' => 'text'))
			->set_template('
				<h1 id="page_title" class="clearfix">
					<a href="javascript: void(0);" class="toggle_item disclosure nomarker">
						'.$title.'
						<span class="sort_info">'.$sortinfo.'</span>
						<span class="icon fr ">
							<img src="'.\Uri::base().'lcm_assets/img/system/mark_search.png" alt="">
							<span class="hide_if_smalldisplay" aria-hidden="true" role="presentation">検索</span>
							<span class="skip"> エンターで検索条件を開きます</span>
						</span>
					</a>
				</h1>
				<div class="hidden_item form_group">
				<section>
					<h1 class="skip">検索</h1>
					<form class="search">
			');

		// limit
		$options = array(
			10 => 10,
			25 => 25,
			50 => 50,
			100 => 100,
			250 => 250,
			24 => '24(タックシール一枚分)',
		);

		$form
			->add('limit', '', array('type' => 'select', 'class'=>'w5em', 'title'=>'表示件数', 'options' => $options))
			->set_value(\Input::get('limit', 25))
			->set_template('
				<div class="submit_button">'.
				\Html::anchor(\Uri::current(), '絞り込みを解除', ['class' => 'button']).
				'{field}件&nbsp;
			');

		// submit
		$form
			->add('submit', '', array('type' => 'submit', 'value' => '検索', 'class' => 'button primary'))
			->set_template('
				{field}
				</div><!--/.submit_button-->
				</form>
			</section>
			</div><!-- /.hidden_item.form_group -->'
			);

		return $form;
	}
}
