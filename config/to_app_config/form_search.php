<?php

return array(
	// regular form definitions
	'prep_value'                 => true,
	'auto_id'                    => true,
	'auto_id_prefix'             => 'form_',
	'form_method'                => 'post',
//	'form_template'              => "\n\t\t{open}\n\t\t<table>\n{fields}\n\t\t</table>\n\t\t{close}\n",
//	'form_template'              => "<section class=\"search form_group lcm_focus\">\n{fields}\n\t\t</section>",
	'form_template'              => "{fields}",
//	'fieldset_template'          => "\n\t\t<tr><td colspan=\"2\">{open}<table>\n{fields}</table></td></tr>\n\t\t{close}\n",
	'field_template'             => "\t<div class=\"input_group\"><h2 class=\"{error_class}\">{label}{required}</h2>\n\t\t<span class=\"{error_class}\">{field} <span>{description}</span> {error_msg}</span></div>\n",
	// 'multi_field_template'       => "\t\t<h2 class=\"{error_class}\">{group_label}{required}</h2>\n\t\t\t<span class=\"{error_class}\">{fields}\n\t\t\t\t<label>{field} {label}</label><br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</span>\n",
	// 改行抜いた
	'multi_field_template'       => "\t<h2 class=\"{error_class}\">{group_label}{required}</h2>\n\t\t\t<span class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</span>\n",
	'error_template'             => '{error_msg}',
	'group_label'	             => '{label}',
	'required_mark'              => '<em class=\"require\">必須</em>',
	'inline_errors'              => true,
	'error_class'                => 'validation_error',
	'label_class'                => null,

	// tabular form definitions
	'tabular_form_template'      => "<table>{fields}</table>\n",
	'tabular_field_template'     => "{field}",
	'tabular_row_template'       => "<tr>{fields}</tr>\n",
	'tabular_row_field_template' => "\t\t\t<td>{label}{required}&nbsp;{field} {error_msg}</td>\n",
	'tabular_delete_label'       => "",
);
