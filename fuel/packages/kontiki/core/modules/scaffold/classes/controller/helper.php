<?php
namespace Scaffold;
class Helper
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
	 * migration()
	 * /packages/oil/classes/generate.php から移設
	 */
	public function migration($name, $subjects, $cmds)
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
					preg_match('/([a-z0-9_-]+)(?:\[([0-9a-z_\-\,\s]+)\])?/i', $part, $part_matches);
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
						if (count($option) > 0)
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

		$migration = <<<MIGRATION
<?php
namespace Fuel\Migrations;
class Create_{$migration_name}
{
	public function up()
	{
{$up}
	}

	public function down()
	{
{$down}
	}
}
MIGRATION;
		return $migration;
	}

	/**
	 * generate_controller()
	 */
	public function generate_controller($name)
	{
		$val = file_get_contents(dirname(__DIR__).'/templates/controller.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_actionset()
	 */
	public function generate_actionset($name)
	{
		$val = file_get_contents(dirname(__DIR__).'/templates/actionset.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_actionset_owner()
	 */
	public function generate_actionset_owner($name)
	{
		$val = file_get_contents(dirname(__DIR__).'/templates/actionset_owner.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_model()
	 */
	public function generate_model($name, $cmds)
	{
		$name = ucfirst($name);
		$table_name = \Inflector::tableize($name);
		$field_str = '';
		$field_str.= "\t\t'id',\n";//fuel's spec
		foreach($cmds as $field):
			list($field, $attr) = explode(':', $field);
			$field_str.= "\t\t'".$field."',\n";
		endforeach;

		$str = <<<FILES
<?php
namespace {$name};
class Model_{$name} extends \Kontiki\Model_Crud
{
	protected static \$_table_name = '{$table_name}';
	protected static \$_primary_name = '';

	protected static \$_properties = array(
{$field_str}
// 'workflow_status',
	);
}
FILES;
		return $str;
	}

	/**
	 * generate_view()
	 */
	public function generate_view($name)
	{
		$name = ucfirst($name);
		$str = <<<FILES
<?php
namespace {$name};
class View_{$name} extends \Kontiki\View
{
}
FILES;
		return $str;
	}

	/**
	 * generate_views_index()
	 */
	public function generate_views_index($name)
	{
		$val = file_get_contents(dirname(__DIR__).'/templates/index.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_view()
	 */
	public function generate_views_view($name, $cmds)
	{
		$fields = '' ;

		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$fields.= '<tr>'."\n" ;
			$fields.= "\t<th>".$field."</th>\n" ;
			if(substr($field,0,3)=='is_'){
				$fields.= "\t".'<td><?php echo $item->'.$field.'; ?>Yes<?php else: ?>No<?php endif; ?></td>'."\n";
			}else{
				$fields.= "\t<td>".'<?php echo $item->'.$field.'; ?>'."</td>\n" ;
			}
			$fields.= '</tr>'."\n\n" ;
		}
		
		//mold
		$val = file_get_contents(dirname(__DIR__).'/templates/view.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###fields###', $fields , $val) ;
	
		return $val;
	}

	/**
	 * generate_views_options()
	 */
	public function generate_views_options($name)
	{
		$val = file_get_contents(dirname(__DIR__).'/templates/options_samples.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_form()
	 */
	public function generate_views_form($name, $cmds)
	{
		$fields = '' ;
		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$fields.= '<tr>'."\n" ;
			//label
			$fields.= "\t<th><?php echo \Form::label('{$field}', '{$field}'); ?></th>\n" ;
			
			//field
			if(substr($field,0,4)=='name'){//textarea - recently name tend to be multi line.
				$fields.= "\t".'<td><textarea class="subject" id="'.$field.'" name="'.$field.'"><{$vals.'.$field.'|escape}></textarea></td>'."\n" ;
			}elseif(substr($field,0,3)=='is_'){//checkbox
				$fields.= "\t\$checked = Input::post('{$field}') ? ' checked=\"checked\" : null;'\n";
				$fields.= "\t<td><label><?php echo \Form::checkbox(\"{$field}\", 1, array('class' => '', \$checked)).{$field}.' ?></label></td>\n";
			}else{//input
				$fields.= "\t<td><?php echo \Form::input('{$field}', Input::post('{$field}', isset(\$item) ? \$item->{$field} : ''), array('placeholder' => '{$field}')); ?></td>\n" ;
			}
			$fields.= '</tr>'."\n\n" ;
		}
		
		//mold
		$val = file_get_contents(dirname(__DIR__).'/templates/_form.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###fields###', $fields , $val) ;

		return $val;
	}

	/**
	 * generate_views_create()
	 */
	public function generate_views_create($name)
	{
		$val = file_get_contents(dirname(__DIR__).'/templates/create.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_edit()
	 */
	public function generate_views_edit($name)
	{
		$val = file_get_contents(dirname(__DIR__).'/templates/edit.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_config()
	 */
	public function generate_config($name)
	{
		$val = file_get_contents(dirname(__DIR__).'/templates/config.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	//===replaces===
	public function replaces($name,$tpl)
	{
		$tpl = str_replace ('XXX', ucfirst($name) , $tpl);
		$tpl = str_replace ('xxx', strtolower($name) , $tpl);
		$tpl = str_replace ('YYY', $name , $tpl);
		return $tpl;
	}

	//===putfiles===
	public function putfiles($path, $val)
	{
		touch($path) ;
		$fp = fopen($path, 'w');
		fwrite($fp, $val);
//			fwrite($fp, pack('C*',0xEF,0xBB,0xBF));//BOM -> php unaccept BOM
		fclose($fp) ;
		chmod($path, 0777);
	}
}
