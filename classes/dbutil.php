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

	public static function create_table_for_soft(
		$table,
		$fields,
		$primary_keys = array(),
		$if_not_exists = true,
		$engine = false,
		$charset = null,
		$foreign_keys = array(),
		$db = null
	)
	{
		$fields = static::fields_to_array($fields);

		$p_fields = array('id' => array('type' => 'int', 'auto_increment' => true, 'unsigned' => true));

		$fields = array_merge($p_fields, $fields);
		$fields = array_merge($fields, array(
			'creator_id' => array('type' => 'int', 'constraint' => 5),
			'updater_id' => array('type' => 'int', 'constraint' => 5),
			'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
			'updated_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
			'deleted_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		));

		return static::create_table(
			$table,
			$fields,
			array('id')
		);
	}

	public static function add_fields_for_soft(
		$table,
		$fields,
		$primary_keys = array(),
		$if_not_exists = true,
		$engine = false,
		$charset = null,
		$foreign_keys = array(),
		$db = null
	)
	{
		$fields = static::fields_to_array($fields);

		return static::add_fields(
			$table,
			$fields
		);
	}

	public static function modify_fields_for_soft(
		$table,
		$fields,
		$primary_keys = array(),
		$if_not_exists = true,
		$engine = false,
		$charset = null,
		$foreign_keys = array(),
		$db = null
	)
	{
		$fields = static::fields_to_array($fields);

		return static::modify_fields(
			$table,
			$fields
		);
	}


	protected static $_fields_to_array = array(
		'bool'     => array('type' => 'bool',                         'default' => 0),
		'int'      => array('type' => 'int',     'constraint' => 11,  'default' => 0),
		'float'    => array('type' => 'float',                        'default' => 0),
		'double'   => array('type' => 'double',                       'default' => 0),
		'varchar'  => array('type' => 'varchar', 'constraint' => 255, 'default' => ''),
		'text'     => array('type' => 'text',                         'default' => ''),
		'datetime' => array('type' => 'datetime',     'null' => true, 'default' => null),
		'date'     => array('type' => 'date',         'null' => true, 'default' => null),
		'time'     => array('type' => 'time',         'null' => true, 'default' => null),
	);

	public static function fields_to_array($fields)
	{
		foreach ($fields as $key => $value)
		{
			if (!is_array($value) && array_key_exists($value, static::$_fields_to_array))
			{
				 $fields[$key] = static::$_fields_to_array[$value];
			}
		}

		return $fields;
	}


}


