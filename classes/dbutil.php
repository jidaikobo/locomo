<?php
namespace Locomo;
class DBUtil extends \Fuel\Core\DBUtil
{

	/*
	 * @param string $table    | table name
	 * @param string $sum      | field use sum(***)
	 * @param string $group_by | group by rows
	 * @param array  $cols     | where condition
	 * @param array  $options  | conditions
	 * @return array cnt, sum, cnt_total, sumtotal
	 */
	public static function summary(
		$table,
		$sum,
		$group_by,
		$options = array()
	) {

		$results = array();

		$query = \DB::select(\DB::expr($group_by . ', SUM(' . $sum . ') AS sum, COUNT(id) AS cnt'))
			->from($table)
			->group_by($group_by);

		if (isset($options['where'])) {
			foreach ($options['where'] as $wh) {
				if (count($wh)>2)
				{
					$query->where($wh[0], $wh[1], $wh[2]);
				}
				else
				{
					$query->where($wh[0], $wh[1]);
				}
			}
		}

		$result = $query->execute()->as_array();

		// if ($i == 11) var_dump(\DB::last_query());
		$results['cnt'] = \Arr::assoc_to_keyval($result, $group_by, 'cnt');
		$results['sum'] = \Arr::assoc_to_keyval($result, $group_by, 'sum');
		$results['cnt_total'] = \Arr::sum($result, 'cnt');
		$results['sum_total'] = \Arr::sum($result, 'sum');

		return $results;
	}


	/*
	 * fiscal_summary()
	 * summary の年度集計版
	 * @param int    $year       | year
	 * @param string $date_field | field of date ***_at
	 * @param string $table      | table name
	 * @param string $sum        | field use sum(***)
	 * @param string $group_by   | group by rows
	 * @param array  $cols       | where condition
	 * @param array  $options    | conditions
	 * @return array cnt, sum, cnt_total, sumtotal
	 */
	public static function fiscal_summary(
		$year,
		$date_field,
		$table,
		$sum,
		$group_by,
		$options = array()
	) {
		$results = array();

		for ($i = 4; $i < 16; $i++)
		{
			if ($i > 12) {
				$_month = sprintf('%02d', $i%12);
				$_year = $year + 1;
			} else {
				$_month = sprintf('%02d', $i);
				$_year = $year;
			}

			$monthly_options = $options;

			$monthly_options['where'][] = array($date_field, 'like', $_year . '-' . $_month . '%');

			$results[$_month] = static::summary(
				$table,
				$sum,
				$group_by,
				$monthly_options
			);
		}

		return $results;
	}

}


