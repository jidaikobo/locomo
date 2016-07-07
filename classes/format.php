<?php
namespace Locomo;

class Format extends \Fuel\Core\Format
{
	/**
	 * Import CSV data
	 *
	 * @param   string  $string
	 * @param   bool    $no_headings
	 * @return  array
	 */
	protected function _from_csv($string, $no_headings = false)
	{
		$data = array();

		// csv config
		$newline = \Config::get('format.csv.regex_newline', "\n");
		$delimiter = \Config::get('format.csv.delimiter', \Config::get('format.csv.import.delimiter', ','));
		$escape = \Config::get('format.csv.escape', \Config::get('format.csv.import.escape', '"'));
		// have to do this in two steps, empty string is a valid value for enclosure!
		$enclosure = \Config::get('format.csv.enclosure', \Config::get('format.csv.import.enclosure', null));
		$enclosure === null and $enclosure = '"';

		if (empty($enclosure))
		{
			$rows = preg_split('/(['.$newline.'])/m', trim($string), -1, PREG_SPLIT_NO_EMPTY);
		}
		else
		{
			// $rows = preg_split('/(?<=[0-9'.preg_quote($enclosure).'])'.$newline.'/', trim($string)); おかしい
			$rows = preg_split('/(?<=['.preg_quote($enclosure).'])'.$newline.'/', trim($string));
		}

		// Get the headings
		if ($no_headings !== false)
		{
			$headings = str_replace($escape.$enclosure, $enclosure, str_getcsv(array_shift($rows), $delimiter, $enclosure, $escape));
			$headcount = count($headings);
		}

		// Process the rows
		$incomplete = '';
		foreach ($rows as $row)
		{
			// process the row
			$data_fields = str_replace($escape.$enclosure, $enclosure, str_getcsv($incomplete.($incomplete?$newline:'').$row, $delimiter, $enclosure, $escape));

			// if we didn't have headers, the first row determines the number of fields
			if ( ! isset($headcount))
			{
				$headcount = count($data_fields);
			}

			// finish the row if the have the correct field count, otherwise add the data to the next row
			if (count($data_fields) == $headcount)
			{
				$data[] = $no_headings === false ? $data_fields : array_combine($headings, $data_fields);
				$incomplete = '';
			}
			else
			{
				$incomplete = $row;
			}
		}

		return $data;
	}


}
