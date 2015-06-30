<?php
namespace Locomo;
class Controller_Scffld_Helper_Migration extends Controller_Scffld_Helper
{
	/**
	* @var array $_default_constraints
	*/
	private static $_default_constraints = array(
		'varchar' => 255,
		'char'    => 255,
		'int'     => 11
	);

	/**
	 * generate()
	 * /packages/oil/classes/generate.php から移設
	 */
	public static function generate($name, $subjects, $cmds)
	{
		// We always pass in fields to a migration, so lets sort them out here.
		$fields = array();
		foreach ($cmds as $field)
		{
			$field_array = array();
	
			// Each paramater for a field is seperated by the : character
			$parts = explode(":", $field);
	
			// We must have the 'name:type' if nothing else!
			if (count($parts) >= 2)
			{
				$field_array['name'] = array_shift($parts);
				foreach ($parts as $part_i => $part)
				{
					// locomo: add \'
					preg_match('/([a-z0-9_-]+)(?:\[([\'0-9a-z_\-\,\s]+)\])?/i', $part, $part_matches);
//					preg_match('/([a-z0-9_-]+)(?:\[([0-9a-z_\-\,\s]+)\])?/i', $part, $part_matches);
					array_shift($part_matches);
	
					if (count($part_matches) < 1)
					{
						// Move onto the next part, something is wrong here...
						continue;
					}
	
					$option_name = ''; // This is the name of the option to be passed to the action in a field
					$option = $part_matches;
	
					// The first option always has to be the field type
					if ($part_i == 0)
					{
						$option_name = 'type';
						$type = $option[0];
						if ($type === 'string')
						{
							$type = 'varchar';
						}
						else if ($type === 'integer')
						{
							$type = 'int';
						}
	
						if ( ! in_array($type, array('text', 'blob', 'datetime', 'date', 'timestamp', 'time')))
						{
							if ( ! isset($option[1]) || $option[1] == NULL)
							{
								if (isset(self::$_default_constraints[$type]))
								{
									$field_array['constraint'] = self::$_default_constraints[$type];
								}
							}
							else
							{
								// should support field_name:enum[value1,value2]
								if ($type === 'enum')
								{
									$values = explode(',', $option[1]);
									$option[1] = '"'.implode('","', $values).'"';
	
									$field_array['constraint'] = $option[1];
								}
								// should support field_name:decimal[10,2]
								elseif (in_array($type, array('decimal', 'float')))
								{
									$field_array['constraint'] = $option[1];
								}
								else
								{
									$field_array['constraint'] = (int) $option[1];
								}
	
							}
						}
						$option = $type;
					}
					else
					{
						// This allows you to put any number of :option or :option[val] into your field and these will...
						// ... always be passed through to the action making it really easy to add extra options for a field
						$option_name = array_shift($option);

						//locomo - start
						if ($option_name == 'default' && $option[0] == "''")
						{
							$option = '';
						}
						//locomo - end
						elseif (count($option) > 0)
						{
							$option = $option[0];
						}
						else
						{
							$option = true;
						}
					}
	
					// deal with some special cases
					switch ($option_name)
					{
						case 'auto_increment':
						case 'null':
						case 'unsigned':
							$option = (bool) $option;
							break;
					}
	
					$field_array[$option_name] = $option;
	
				}
				$fields[] = $field_array;
			}
			else
			{
				// Invalid field passed in
				continue;
			}
		}
		require(PKGPATH.'oil/classes/generate/migration/actions.php');
		$migration = call_user_func("\Oil\Generate_Migration_Actions::create", $subjects, $fields);
		list($up, $down) = $migration;
		$migration_name = strtolower($name);

		//template
		$val = static::fetch_temlpate('migrations.php');
		$migration = str_replace('###MN###',   $migration_name, $val);
		$migration = str_replace('###UP###',   $up,             $migration);
		$migration = str_replace('###DOWN###', $down,           $migration);

		return $migration;
	}
}
