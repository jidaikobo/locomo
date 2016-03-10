<?php
namespace Locomo;
class Presenter_Base extends \Presenter
{
	// $model_name
	public static $model_name = '';

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
		$name = \Request::active()->action;
		while (\Locomo\Fieldset::instance($name))
		{
			$name = $name.'_form';
		}
		$form = \Fieldset::forge($name);

		// populate
		$form->add_model($obj)->populate($obj, true);

		// submit
		$form->add('submit', '', array('type' => 'submit', 'value' => '保存する', 'class' => 'button primary'))->set_template('<div class="lcmbar_bottom"><div class="submit_button">{field}</div><!-- /.submit_button --></div><!-- /.lcmbar_bottom -->');

		return $form;
	}

	/**
	 * bulk()
	 * @return obj instanceof \Form
	 */
	public static function bulk($name = null, $obj = null)
	{
		$form = \Fieldset::forge($name);

		$form->add_model($obj)->populate($obj, true);

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
		$model = static::$model_name ?: str_replace('Controller_', 'Model_', \Request::main()->controller);
		\Pagination::set_config('sort_info_model', $model);
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

		$action_url = is_int(\Pagination::instance()->config['uri_segment']) ? preg_replace('/\/[0-9]+$/u', "/", \Uri::current()) : \Uri::current();

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
					<form class="search" action="' . $action_url . '">
			');

		$form
			->add('clear_results', '', array('type' => 'text'))
			->set_value('')
			->set_template('
				<div class="submit_button">'.
				\Html::anchor(\Uri::current(), '絞り込みを解除', ['class' => 'button']));

		// limit
		$options = array(
			10 => 10,
			25 => 25,
			50 => 50,
			100 => 100,
			250 => 250,
		);

		$form
			->add('limit', '', array('type' => 'select', 'class'=>'w5em', 'title'=>'表示件数', 'options' => $options))
			->set_value(\Input::get('limit', 25))
			->set_template('{field}件&nbsp;');

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

	/**
	 * create_ctrls()
	 */
	public static function create_ctrls($obj)
	{
		$html = '';
		$crtl = \Inflector::ctrl_to_dir(\Request::main()->controller);
		$crtl_name = \Inflector::add_head_backslash(\Request::main()->controller);

		if (\Auth::has_access($crtl_name.'/view'))
		{
			$html.= \Html::anchor($crtl.'/view/'.$obj->id, '閲覧', array('class' => 'view button small'));
		}
		if (\Auth::has_access($crtl_name.'/edit'))
		{
			$html.= \Html::anchor($crtl.'/edit/'.$obj->id, '編集', array('class' => 'edit button small'));
		}

		if (is_subclass_of($obj, '\Orm\Model_Soft'))
		{
			if (\Auth::has_access($crtl_name.'/delete'))
			{
				if ($obj['deleted_at'])
				{
					$html.= \Html::anchor($crtl.'/undelete/'.$obj->id, '復活', array('class' => 'undelete confirm button small'));
					if (\Auth::has_access($crtl_name.'/purge_confirm'))
					{
						$html.= \Html::anchor($crtl.'/purge_confirm/'.$obj->id, '完全に削除', array('class' => 'delete button small'));
					}
				}
				else
				{
					$html.= \Html::anchor($crtl.'/delete/'.$obj->id, '削除', array('class' => 'delete confirm button small'));
				}
			}
		}
		else
		{
			if (\Auth::has_access($crtl_name.'/purge_confirm'))
			{
				$html.= \Html::anchor($crtl.'/purge_confirm/'.$obj->id, '完全に削除', array('class' => 'delete confirm button small'));
			}
		}
		$html = $html ? '<div class="btn_group">'.$html.'</div>' : '' ;

		return $html;
	}

	/*
	 * index_admin_toolbar()
	 */
	public static function index_admin_toolbar($model = null, $name_num = '', $is_print_all = true, $is_repeat = true)
	{
		if (!$model) return '';

		$index_toolbar_btns = '';
		if (\Request::main()->action == 'index_admin')
		{
			$opt = static::index_admin_toolbar_format($model, $name_num, $is_print_all, $is_repeat);

			$index_toolbar_btns = html_tag('div', array(
				'class' => 'index_toolbar_buttons'
			), $opt);
		}

		$pagination = \Pagination::create_links();

		$result_str = html_tag('div', array(
			'class' => 'index_toolbar clearfix'
		), $index_toolbar_btns . $pagination);

		return $result_str;
	}


	public static function index_admin_toolbar_format($model = null, $name_num = '', $is_print_all = true, $is_repeat = true)
	{
		$opt = '';
		$opt.= \Form::button('全てチェック', null, array('class' => 'check_all button small'));
		$opt.= \Form::button('全て外す',     null, array('class' => 'uncheck_all button small'));

		$model::$_options['where'][] = array(
			array('is_draft', false),
		);
		$formats = $model::find('all', $model::$_options);

		$opt .= '<select name="format'.$name_num.'" class="select_format">';
		$opt .= '<option value=""> ▼ 選択して下さい ▼ </option>';
		foreach ($formats as $format)
		{
			$multiple = $format->is_multiple ? 'multiple' : '';
			$cells = $multiple  ? $format->cols * $format->rows : 1;
			$selected = \Input::post('format'.$name_num) == $format->id ? ' selected="selected"' : '';
			$opt .= '<option value="'.$format->id.'" class="'.$multiple.' "'.$selected.' data-cells="'.$cells.'">'.$format->name.'</option>';
		}
		$opt .= '</select>';

		$opt .= '<div class="pdf_start_cell nowrap" style="display:inline;">セル';
		$opt .= '<select name="start_cell'.$name_num.'" class="start_cell">';
		$opt .= '<option value=""></option>';
		for ($i=1; $i<=10; $i++)
		{
			$opt .= '<option value="'.$i.'">'.$i.'</option>';
		}
		$opt .= '</select>';
		$opt .= '個目から、</div>';

		if ($is_repeat)
		{
			$opt .= '<div class="pdf_repeat nowrap" style="display:inline;">繰り返し';
			$opt .= '<select name="print_repeat'.$name_num.'" class="print_repeat">';
			for ($i=1; $i<=12; $i++)
			{
				$selected = (\Input::post('print_repeat') == $i) ? 'selected="selected"' : '';
				$opt .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			}
			$opt .= '</select>';
			$opt .= '件ずつ</div>';
		}

		$opt.= \Form::hidden('count_total', \Pagination::get("total_items"), array('class' => 'count_total'));
		$opt.= \Form::submit('submit'.$name_num, 'チェック項目出力', array('class'=>'button small primary'));
		if ($is_print_all) $opt.= \Form::submit('print_all'.$name_num, '全て出力 ('. \Pagination::get("total_items").')', array('class'=>'button small primary print_all'));

		// 印刷設定へリンク
		$format_slug = strtolower(str_replace('_', '/', str_replace('\Model_', '/', ltrim($model, '\\')))) . '/index_admin';
		if (\Auth::has_access($format_slug)) $opt .= \Html::anchor($format_slug, '印刷設定へ', array());

		return $opt;
	}
}

