<?php
namespace Scaffold;
class Helper_Scaffold
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
	public static function migration($name, $subjects, $cmds)
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

		//template
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'migrations.php');
		$migration = str_replace('===MN===',   $migration_name, $val);
		$migration = str_replace('===UP===',   $up,             $migration);
		$migration = str_replace('===DOWN===', $down,           $migration);

		return $migration;
	}

	/**
	 * generate_controller()
	 */
	public static function generate_controller($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'controller.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_actionset_index()
	 */
	public static function generate_actionset_index($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'actionset_index.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_actionset_base()
	 */
	public static function generate_actionset_base($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'actionset_base.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_actionset_owner()
	 */
	public static function generate_actionset_owner($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'actionset_owner.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_actionset_option()
	 */
	public static function generate_actionset_option($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'actionset_option.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_model()
	 */
	public static function generate_model($name, $cmd_orig)
	{
		//vals
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);//remove name
		$name = ucfirst($name);
		$table_name = \Inflector::tableize($name);

		//fieldset
		$field_str = '';
		$form_definition_str = '';
		$field_str.= "\t\t'id',\n";//fuel's spec
		foreach($cmds as $field):
			list($field, $attr) = explode(':', $field);
			$nicename = self::get_nicename($field);
			$field    = self::remove_nicename($field);
			
			//field_str
			$field_str.= "\t\t'{$field}',\n";

			//attribute
			$max  = preg_match('/\[(.*?)\]/', $attr, $m) ? intval($m[1]) : 0 ;
			$size = $max >= 30 ? 30 : $max ;
			$size = $max == 0  ? 30 : $max ;

			//form_definition
			$form_definition_str.= "\t\t//{$field}\n";
			$form_definition_str.= "\t\t\$form->add(\n";
			$form_definition_str.= "\t\t\t'{$field}',\n";
			$form_definition_str.= $nicename ? "\t\t\t'{$nicename}',\n" : "\t\t\t'{$field}',\n";
			//field
			if(in_array($field, array('name', 'title', 'subject', 'text', 'memo', 'body', 'content', 'etc', 'message'))):
				//textarea
				$form_definition_str.= "\t\t\tarray('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;')\n";
			elseif(in_array($field, array('status'))):
				//status - temporary
				$form_definition_str.= "\t\t\tarray('type' => 'hidden')\n";
			elseif(substr($field,0,3)=='is_'):
				//bool
				$form_definition_str.= "\t\t\tarray('type' => 'checkbox', 'options' => array(0, 1))\n";
			elseif(substr($field,-3)=='_at'):
				//date
				$form_definition_str.= "\t\t\tarray('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))\n";
			else:
				//text
				$form_definition_str.= "\t\t\tarray('type' => 'text', 'size' => {$size})\n";
			endif;
			$form_definition_str.= "\t\t)\n";
			//require
			if(in_array($field, array('name', 'title', 'subject'))):
				$form_definition_str.= "\t\t->add_rule('required')\n";
			endif;
			//require
			if($max):
				$form_definition_str.= "\t\t->add_rule('max_length', {$max})\n";
			endif;

			//default value
			if($field == 'created_at'):
				//created_at
				$form_definition_str.= "\t\t->set_value(isset(\$obj->created_at) ? \$obj->created_at : date('Y-m-d H:i:s'));\n\n";
			else:
				//others
				$form_definition_str.= "\t\t->set_value(@\$obj->{$field});\n\n";
			endif;
		endforeach;

		//template
		$str = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'model.php');
		$str = str_replace('===NAME===',       $name,       $str);
		$str = str_replace('===TABLE_NAME===', $table_name, $str);
		$str = str_replace('===FIELD_STR===',  $field_str,  $str);
		$str = str_replace('===FORM_DEFINITION===',  $form_definition_str,  $str);

		return $str;
	}

	/**
	 * generate_view()
	 */
	public static function generate_view($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'viewmodel.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_index()
	 */
	public static function generate_views_index($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'index.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_view()
	 */
	public static function generate_views_view($name, $cmds)
	{
		$fields = '' ;

		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$fields.= '<tr>'."\n" ;
			$fields.= "\t<th>".$field."</th>\n" ;
			if(substr($field,0,3)=='is_'):
				$fields.= "\t<td><?php echo \$item->{$field} ? 'Yes' : 'No' ; ?></td>\n";
			else:
				$fields.= "\t<td><?php echo \$item->{$field}; ?></td>\n";
			endif;
			$fields.= '</tr>'."\n\n" ;
		}
		
		//mold
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'view.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###fields###', $fields , $val) ;
	
		return $val;
	}

	/**
	 * generate_views_options()
	 */
	public static function generate_views_options($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'option_samples.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_form()
	 */
	public static function generate_views_form($name, $cmds)
	{
		$hiddens = array('status');
		$banned = array('modified_at', 'updated_at', 'deleted_at', 'workflow_status');

		$fields = '' ;
		$hidden_fields = '' ;
		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			if(in_array($field, $banned)) continue;

			//hidden
			if(in_array($field, $hiddens)):
				$hidden_fields.= "\techo \$form->field('{$field}')->set_template('{error_msg}{field}');\n" ;
			else:
				$fields.= '<tr>'."\n" ;
				//label
				$fields.= "\t<th><?php echo \$form->field('{$field}')->set_template('{label}{required}'); ?></th>\n" ;
				
				//field
				if(substr($field,0,3)=='is_'){//checkbox
					$fields.= "\t<td><?php echo \$form->field('{$field}')->set_template('{fields} {field} {label}<br /> {fields}'); ?></td>\n" ;
				}else{//input
					$fields.= "\t<td><?php echo \$form->field('{$field}')->set_template('{error_msg}{field}'); ?></td>\n" ;
				}
				$fields.= '</tr>'."\n\n" ;
			endif;
		}
		
		//mold
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'_form.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###FIELDS###', $fields , $val) ;
		$val = str_replace ('###HIDDEN_FIELDS###', $hidden_fields , $val) ;

		return $val;
	}

	/**
	 * generate_views_create()
	 */
	public static function generate_views_create($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'create.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_edit()
	 */
	public static function generate_views_edit($name)
	{
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'edit.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_config()
	 */
	public static function generate_config($cmd_orig)
	{
		//vals
		$cmds = explode(' ', $cmd_orig);
		$name = array_shift($cmds);
		$nicename = self::get_nicename($name);

		//template
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'config.php');
		$val = str_replace ('###nicename###', $nicename , $val) ;
		return $val;
	}

	/**
	 * replaces()
	 */
	public static function replaces($name,$tpl)
	{
		$tpl = str_replace ('XXX', ucfirst($name) , $tpl);
		$tpl = str_replace ('xxx', strtolower($name) , $tpl);
		$tpl = str_replace ('YYY', $name , $tpl);
		return $tpl;
	}

	/**
	 * putfiles()
	 */
	public static function putfiles($path, $val)
	{
		touch($path) ;
		$fp = fopen($path, 'w');
		fwrite($fp, $val);
//			fwrite($fp, pack('C*',0xEF,0xBB,0xBF));//BOM -> php unaccept BOM
		fclose($fp) ;
		@chmod($path, 0777);
	}


	/**
	 * get_nicename()
	 */
	public static function get_nicename($str)
	{
		preg_match('/\((.*?)\)/', $str, $m);
		return @$m[1];
	}

	/**
	 * remove_nicename()
	 */
	public static function remove_nicename($str)
	{
		return preg_replace('/\(.*?\)/', '', $str);
	}
}
