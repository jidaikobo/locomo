<?php
class Presenter_Flr_Index_Files extends \Presenter_Base
{
	/*
	 * search_form
	 */
	public static function search_form($title)
	{
		// parent
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('flr', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// 登録日 - 開始
		$form->add(
				'from',
				'登録日',
				array(
					'type'        => 'text',
					'value'       => \Input::get('from'),
					'id'          => 'registration_date_start',
					'class'       => 'date',
					'placeholder' => date('Y-n-j', time() - 86400 * 365),
					'title'       => '登録日 開始 ハイフン区切りで入力してください',
				)
			)
			->set_template('
				<div class="input_group">
				<h2>登録日</h2>
				{field}&nbsp;から
			');

		// 登録日 - ここまで
		$form->add(
				'to',
				'登録日',
				array(
					'type'        => 'text',
					'value'       => \Input::get('to'),
					'id'          => 'registration_date_end',
					'class'       => 'date',
					'placeholder' => date('Y-n-j'),
					'title'       => '登録日 ここまで ハイフン区切りで入力してください',
				)
			)
			->set_template('
				{field}</div><!--/.input_group-->
			');

		// wrap
		$parent = parent::search_form($title);
		$parent->add_after($form, 'msgbrd', array(), array(), 'opener');

		if ( ! \Input::get('submit'))
		{
			$pattern  = '/<span class="sort_info">.+?<\/span>/';
			$count = \Model_Flr::count() - 1; //ルートディレクトリを除く
			$replace  = '<span class="sort_info">全'.$count.'件のファイル／ディレクトリがあります。</span>';
			$subject  = (string) $parent->field('opener');
			$template = preg_replace($pattern, $replace, $subject);
			$parent->field('opener')->set_template($template);
		}

		$parent->delete('limit');

		return $parent;
	}
}
