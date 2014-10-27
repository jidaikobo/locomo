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

	// tabular form definitions
	'bulk_form_template'      => "<table>{fields}</table>\n",
	'bulk_field_template'     => "{field}",
	'bulk_row_template'       => "<tr>{fields}</tr>\n",
	'bulk_row_field_template' => "\t\t\t<td>{required}&nbsp;{field} {error_msg}</td>\n",
	'bulk_delete_label'       => "Del",
);

