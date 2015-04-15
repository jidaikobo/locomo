<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */


return array(
	// regular form definitions
	'prep_value'                 => true,
	'auto_id'                    => true,
	'auto_id_prefix'             => 'form_',
	'form_method'                => 'post',
	'form_template'              => "\n{fields}\n\t\t",
	'fieldset_template'          => "{fields}",
//	'fieldset_template'          => "\n\t\t<div class=\"input_group fieldset\" title=\"{title_contents}\" data-jslcm-tooltip=\"{error_msg}\">\n{fields}</div>\n",
	'field_template'             => "
			\t\t<div class=\"input_group\">\n
			\t\t\t<h2>{required}{label}</h2>\n
			\t\t\t<div class=\"field\">\n
				\t\t\t\t<em class=\"exp\">{description}</em>\n
				\t\t\t\t{field}{error_alert_link}{error_msg}\n
			\t\t\t</div>\n
			\t\t</div>\n
		",
	'multi_field_template'       => "
			\t\t<div class=\"input_group lcm_focus label_fb {type}\" tabindex=\"0\" title=\"{title_contents} {error_msg}\" data-jslcm-tooltip=\"{error_msg}\">\n
			\t\t<h2>{required}{group_label}</h2>\n
			\t\t\t<div class=\"field\">{fields}\n
			\t\t\t\t{field}{label}\n
			\t\t\t{fields}\n
			\t\t{error_alert_link}</div></div>\n
		",
	'error_template'             => '{error_msg}',
	'group_label'	             => '{label}',
	'required_mark'              => '<span class="label_required">必須</span>',
	'inline_errors'              => true,
	'error_class'                => '',
	'label_class'                => null,

	// tabular form definitions
	'tabular_form_template'      => "<table>{fields}</table>\n", // tabular
	'tabular_field_template'     => "{field}",
	'tabular_row_template'       => "<tr>{fields}</tr>\n",
	'tabular_row_field_template' => "\t\t\t<td>{required}{field} {label}{error_msg}</td>\n",
	'tabular_delete_label'       => "削除",

	/*
	 * locomo 独自の config
	 */
	// flash error に戻るリンク ( 無効 => false )
	'error_alert_link'           => "<a class=\"skip show_if_focus link_alert_error\" href=\"#anchor_alert_error\">エラー一覧にもどる</a>",
	// 暗黙的ラベルの有効 (bool)
	"implicit_label" => true,
	// multi_field_template に対して {title_contents} の有効 ( true => array(label, error, description), array(description, error, または get_attribute() で取れるもの), false => 無効)
	// "title_contents"            => array('error', 'description', 'type'),
	"title_contents"            => true,

	// tabular
	'tabular_delete_label_field'        => "このフィールドを削除",

	// multi_field_template を使うと h2 の描画が邪魔になる
	'multi_field_template_tabular'      => "<td>
			\t\t<div class=\"input_group lcm_focus label_fb {type}\" tabindex=\"0\" title=\"{title_contents}\" data-jslcm-tooltip=\"{error_msg}\">\n
			\t\t\t<div class=\"field\">{fields}\n
			\t\t\t\t{field}{label}\n
			\t\t\t{fields}\n
			\t\t</div></div>\n
			</td>",

	'opener_template' => "<div class='input_group opener'>",
	'closer_template' => "</div>",


	/*
	 * build_plain_template
	 * suffix "_plain"

	'auto_id_prefix_plain'             => 'form_',
	'form_template_plain'              => "\n{fields}\n\t\t",
	'fieldset_template_plain'          => "\n\t\t<table>\n{fields}</table>\n",
	'field_template_plain'             => "
			\t\t\t<h2>{required}{label}</h2>\n
			\t\t\t<div class=\"field\">\n
			\t\t\t\t<em class=\"exp\">{description}</em>\n
			\t\t\t\t{field}{error_alert_link}{error_msg}\n
			\t\t\t</div>\n
		",
	'multi_field_template_plain'       => "
			\t\t<div class=\"input_group lcm_focus label_fb {type}\" tabindex=\"0\" title=\"{title_contents} {error_msg}\" data-jslcm-tooltip=\"{error_msg}\">\n
			\t\t<h2>{required}{group_label}</h2>\n
			\t\t\t<div class=\"field\">{fields}\n
			\t\t\t\t{field}{label}\n
			\t\t\t{fields}\n
			\t\t{error_alert_link}</div></div>\n
		",
	'group_label_plain'                => '{label}',
	'required_mark_plain'              => '<span class="label_required">必須</span>',
		 */
	// build_plain 時間を出力する時のフォーマット 時間が 00:00:00 に評価される時は無視
	'date_format_plain'                => 'Y年 n月 j日 H:i:s',
);



$default = array(
	// regular form definitions
	'prep_value'                 => true,
	'auto_id'                    => true,
	'auto_id_prefix'             => 'form_',
	'form_method'                => 'post',
	'form_template'              => "\n
			\t\t{open}\n
			\t\t<table>\n
			{fields}\n
			\t\t</table>\n
			\t\t{close}\n
		",
	'fieldset_template'          => "\n
			\t\t<tr><td colspan=\"2\">{open}<table>\n
			{fields}</table></td></tr>\n
			\t\t{close}\n
		",
	'field_template'             => "
			\t\t<tr>\n
			\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n
			\t\t\t<td class=\"{error_class}\">{field} <span>{description}</span> {error_msg}</td>\n
			\t\t</tr>\n
		",
	'multi_field_template'       => "
			\t\t<tr>\n
			\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n
			\t\t\t<td class=\"{error_class}\">{fields}\n
			\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n",
	'error_template'             => '<span>{error_msg}</span>',
	'group_label'	             => '<span>{label}</span>',
	'required_mark'              => '*',
	'inline_errors'              => false,
	'error_class'                => null,
	'label_class'                => null,

	// tabular form definitions
	'tabular_form_template'      => "<table>{fields}</table>\n",
	'tabular_field_template'     => "{field}",
	'tabular_row_template'       => "<tr>{fields}</tr>\n",
	'tabular_row_field_template' => "\t\t\t<td>{label}{required}&nbsp;{field} {error_msg}</td>\n",
	'tabular_delete_label'       => "削除",
);
