<?php
class Presenter_Flr_File_View extends \Presenter_Base
{
	/**
	 * plain()
	 */
	public static function plain($obj = NULL)
	{
		// attention! not use parent::plain()
		$form = parent::form($obj);

		// uri
		$url  = \Uri::create('flr/file/dl/?p='.\Model_Flr::enc_url($obj->path, true));
		$url = \Inflector::get_root_relative_path($url);

		// size
		$size = \Num::format_bytes(\File::get_size(LOCOMOFLRUPLOADPATH.$obj->path));

		// html
		$html = '';
		if (\Locomo\File::get_file_genre($url) == 'image')
		{
			// name add image
			$html = '<a href="'.$url.'" class="lb">'.$obj->name.'</a>';
			$html2show = '<a href="'.$url.'" class="lb" style="
				display: block;
				height: 150px;
				width: 150px;
				border: 1px #eee solid;
				background-image: url(\''.$url.'\');
				background-repeat: no-repeat;
				background-color: #fff;
			"><span class="skip">'.$obj->name.'を拡大</span></a><a href="'.$url.'&dl=1">'.$obj->name.'をダウンロード ('.$size.')</a>';

			$tpl = \Config::get('form')['field_template'];
			$tpl.= $html2show;
			$form->field('name')->set_template($tpl);
		} else {
			// name add download link
			$html = '<a href="'.$url.'">'.$obj->name.' ('.$size.')'.'</a>';
			$tpl = \Config::get('form')['field_template'];
			$tpl = str_replace('{field}', '<a href="'.$url.'">{field}</a>', $tpl);
			$form->field('name')->set_template($tpl);
		}
		$html = htmlspecialchars($html);

		// download_url
		$form->add_after(
			'download_url',
			'ダウンロードURL用文字列',
			array('type' => 'text'),
			array(),
			'name'
		)
		->set_value('<textarea class="textarea" id="download_str" style="height:5em;font-family:monospace;">'.$url.'</textarea><!--<div class="ar"><a href="">クリップボードにコピーする</a></div>-->');

		// download_html
		$form->add_after(
			'download_html',
			'HTML',
			array('type' => 'text'),
			array(),
			'download_url'
		)
		->set_value('<textarea class="textarea" style="height:5em;font-family:monospace;">'.$html.'</textarea><!--<div class="ar"><a href="">クリップボードにコピーする</a></div>-->');

		return $form->build_plain();
	}
}
