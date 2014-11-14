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

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */


return array(
	// regular form definitions
	'prep_value'                 => true,
	'auto_id'                    => true,
	'auto_id_prefix'             => 'form_',
	'form_method'                => 'post',
	'form_template'              => "\n\t\t{open}\n\t\t<table>\n{fields}\n\t\t</table>\n\t\t{close}\n",
	'fieldset_template'          => "\n\t\t<tr><td colspan=\"2\">{open}<table>\n{fields}</table></td></tr>\n\t\t{close}\n",
	'field_template'             => "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} <span>{description}</span> {error_msg}</td>\n\t\t</tr>\n",
	'multi_field_template'       => "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n",
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
	'tabular_delete_label'       => "Delete?",


	/*
	 * ココから view の build_plain 用
	 */
	// Plain Templates
	'form_template_plain'         => "<table class=\"view\">{fields}</table>",
	'field_template_plain'        => "\t<tr><th>{label}<th>\n\t<td>{field}<td></tr>\n",
	'multi_field_template_plain'  => "\t\t<div class=\"multi {error_class}\">\n\t\t\t{group_label}{required}\n\t\t\t{fields}\n\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t\n\t\t</div>\n",
	'group_template' => "\t\t<tr>\n\t\t\t<td class=\"field_groups {error_class}\" colspan=\"2\">\n\t\t\t\t{field}\n\t\t\t</td>\n\t\t</tr>\n",
	'group_field_template' => "{label} {field} {description}",
 	'group_multi_field_template'  => "{group_label} {fields} {field} {label}<br />\n{fields}<span></span>",
	'group_error_template' => "<div>{error_msg}</div>",

	// tabular form definitions
	'tabular_form_template_plain'      => "<table>{fields}</table>\n",
	'tabular_field_template_plain'     => "{field}",
	'tabular_row_template_plain'       => "<tr class=''>{fields}</tr>\n",
	'tabular_row_field_template_plain' => "\t\t\t<td>{field}</td>\n",
	// 'tabular_delete_label'       => "列の削除",

);
