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
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'migrations.php');
		$migration = str_replace('###MN###',   $migration_name, $val);
		$migration = str_replace('###UP###',   $up,             $migration);
		$migration = str_replace('###DOWN###', $down,           $migration);

		return $migration;
	}

	/**
	 * generate_controller()
	 */
	public static function generate_controller($name, $cmd_orig)
	{
		//nicename
		$cmds = explode(' ', $cmd_orig);
		$nicename = self::get_nicename(array_shift($cmds));

		// replace
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'controller.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###nicename###', $nicename , $val) ;
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
		$cmd_mods = array();
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);//remove name
		$name = ucfirst($name);
		$table_name = \Inflector::tableize($name);
		$admins  = array('is_visible');
		$banned = array('modified_at', 'updated_at', 'deleted_at', 'workflow_status', 'creator_id', 'modifier_id');

		//fieldset
		$field_str = '';
		$properties['id'] = '';//fuel's spec
		foreach($cmds as $field):
			$is_required = strpos($field, 'null') !== false ? false : true ;
			list($field, $attr) = explode(':', $field);
			$nicename = self::get_nicename($field);
			$field    = self::remove_nicename($field);
			$class    = ", 'class' => '".self::remove_length($attr)."'";
			$cmd_mods[] = $field;

			//attribute
			$default = '';
			$size = 0;
			$max = 0;
			if(preg_match('/\[(.*?)\]/', $attr, $m))
			{
				if(is_numeric($m[1]))
				{
					$max  = $m[1] ? intval($m[1]) : 0 ;
					$size = ($max >= 30) ? 30 : $max ;
					$size = ($max == 0)  ? 30 : $size ;
				}
				else
				//scalar
				{
					$default = $m[1];
				}
			}

			//field_str
			$items = array();

			if( ! in_array($field, $banned))
			{
				//label
				if($nicename)
				{
					$properties[$field]['label'] = $nicename;
				}
	
				//data_type
				if($attr)
				{
					$properties[$field]['data_type'] = str_replace(array('[',']'), array('(',')'), $attr);
				}
	
				//form
				$form = array();
				if(in_array($field, array('text', 'memo', 'body', 'content', 'etc', 'message'))):
					//textarea
					$form = array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;');
				elseif(substr($field,0,3)=='is_'):
					//bool
					$form = array('type' => 'select', 'options' => array(0, 1));
				elseif(substr($field,-3)=='_at'):
					//date
					$form = array('type' => 'text', 'size' => 20);
				else:
					//text
					$form = array('type' => 'text', 'size' => $size);
				endif;
				if($form)
				{
					$form['class'] = self::remove_length($attr);
					$properties[$field]['form'] = $form;
				}
	
				//validation
				$validation = array();
				if(in_array($field, array('name', 'title', 'subject')) || $is_required)
				{
					//require
					$validation['required'] ='';
				}
	
				if($max)
				{
					//max
					$validation['max_length'] = array($max => '');
				}
	
				if($validation)
				{
					$properties[$field]['validation'] = $validation;
				}
			}
			else
			{
				$properties[$field] = array('form' => array('type' => false));
			}
		endforeach;

		//soft_delete
		$dlt_fld = '';
		if(in_array('deleted_at', $cmd_mods)):
			$dlt_fld = "\tprotected static \$_soft_delete = array(\n\t\t'deleted_field'   => 'deleted_at',\n\t\t'mysql_timestamp' => true,\n\t);\n";
		endif;

		//observers
		$observers = '';
		if(in_array('created_at', $cmd_mods)):
			$observers.= "\t\t'Orm\Observer_CreatedAt' => array(\n\t\t\t'events' => array('before_insert'),\n\t\t\t'mysql_timestamp' => true,\n\t\t),\n";
		endif;
		if(in_array('updated_at', $cmd_mods)):
			$observers.= "\t\t'Orm\Observer_UpdatedAt' => array(\n\t\t\t\t'events' => array('before_save'),\n\t\t\t\t'mysql_timestamp' => true,\n\t\t\t),\n";
		endif;
		if(in_array('expired_at', $cmd_mods)):
			$observers.= "\t\t'Locomo\Observer_Expired' => array(\n\t\t\t\t'events' => array('before_insert', 'before_save'),\n\t\t\t\t'properties' => array('expired_at'),\n\t\t\t),\n";
		endif;
		if(in_array('creator_id', $cmd_mods) || in_array('modifier_id', $cmd_mods)):
			$observers.= "\t\t'Locomo\Observer_Userids' => array(\n\t\t\t'events' => array('before_insert', 'before_save'),\n\t\t),\n";
		endif;
		$observers.= "//\t\t'Workflow\Observer_Workflow' => array(\n//\t\t\t'events' => array('before_insert', 'before_save','after_load'),\n//\t\t),\n";
		$observers.= "//\t\t'Revision\Observer_Revision' => array(\n//\t\t\t'events' => array('after_insert', 'after_save', 'before_delete'),\n//\t\t),\n";

		//$field_str
		$field_str = var_export($properties, true);
		$field_str = str_replace('  ', "\t", $field_str);
		$field_str = preg_replace("/^/m", "\t", $field_str);
		$field_str = str_replace(" => '',", ",", $field_str);

		//template
		$str = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'model.php');
		$str = self::replaces($name, $str);
		$str = str_replace('###DLT_FLD###',    $dlt_fld,    $str);
		$str = str_replace('###OBSRVR###',     $observers,  $str);
		$str = str_replace('###NAME###',       $name,       $str);
		$str = str_replace('###TABLE_NAME###', $table_name, $str);
		$str = str_replace('###FIELD_STR###',  $field_str,  $str);

		return $str;
	}

	/**
	 * generate_views_index()
	 */
	public static function generate_views_index($name, $cmd_orig, $is_admin = false)
	{
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);//remove name

		$thead = "\t\t\t<th><?php echo \Pagination::sort('id', 'ID', false);?></th>\n";
		$tbody = "\t<td><?php echo \$item->id; ?></td>\n" ;

		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$nicename = self::get_nicename($field);
			$field    = self::remove_nicename($field);
			if(empty($nicename)) continue;

			//th
			if($is_admin):
				$thead.= "\t\t\t<th><?php echo \Pagination::sort('{$field}', '{$nicename}', false);?></th>\n";
			else:
				$thead.= "\t\t\t<th>".$nicename."</th>\n";
			endif;

			//td
			if(substr($field,0,3)=='is_'):
				$tdv = "<?php echo \$item->{$field} ? 'Yes' : 'No' ; ?>";
			else:
				$tdv = "<?php echo \$item->{$field}; ?>";
			endif;

			if($is_admin):
				$tbody.= "\t<td><div class=\"col_scrollable\" tabindex=\"-1\">{$tdv}</div></td>\n";
			else:
				$tbody.= "\t<td>{$tdv}</td>\n";
			endif;
		}
		
		//mold
		$tpl = $is_admin ? 'index_admin.php' : 'index.php' ;
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.$tpl);
		$val = self::replaces($name, $val);
		$val = str_replace ('###THEAD###', $thead , $val) ;
		$val = str_replace ('###TBODY###', $tbody , $val) ;

		return $val;
	}

	/**
	 * generate_views_view()
	 */
	public static function generate_views_view($name, $cmd_orig)
	{
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);//remove name
		$banned = array('workflow_status', 'creator_id', 'modifier_id', 'is_visible');

		$fields = '' ;

		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$nicename = self::get_nicename($field);
			$field    = self::remove_nicename($field);
			if(in_array($field, $banned)) continue;

			$fields.= "<?php if(\$item->{$field}): ?>\n" ;
			$fields.= '<tr>'."\n" ;
			$fields.= "\t<th>".$nicename."</th>\n" ;
			if(substr($field,0,3)=='is_'):
				$fields.= "\t<td><?php echo \$item->{$field} ? 'Yes' : 'No' ; ?></td>\n";
			else:
				$fields.= "\t<td><?php echo \$item->{$field}; ?></td>\n";
			endif;
			$fields.= '</tr>'."\n\n" ;
			$fields.= '<?php endif; ?>'."\n" ;
		}
		
		//mold
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'view.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###fields###', $fields , $val) ;
	
		return $val;
	}

	/**
	 * generate_views_edit()
	 */
	public static function generate_views_edit($name, $cmds)
	{
		$hiddens = array('status');
		$admins  = array('is_visible');
		$banned = array('modified_at', 'updated_at', 'deleted_at', 'workflow_status', 'creator_id', 'modifier_id');

		$fields = '' ;
		$admin_fields = '' ;
		$admin_hidden_fields = '' ;
		$hidden_fields = '' ;
		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			if(in_array($field, $banned)) continue;

			//hidden
			if(in_array($field, $hiddens)):
				$hidden_fields.= "\techo \$form->field('{$field}')->set_template('{error_msg}{field}');\n" ;
			else:
				//admin
				if(in_array($field, $admins)):
					$admin_hidden_fields.= "\t\techo \$form->field('{$field}')->set_template('{error_msg}{field}');\n" ;
					$fields.= '<?php if(\Auth::is_admin()): ?>'."\n" ;
				endif;

				$fields.= '<tr>'."\n" ;
				//label
				$fields.= "\t<th><?php echo \$form->field('{$field}')->set_template('{label}{required}'); ?></th>\n" ;
				
				//field
				if(substr($field,0,3)=='is_'){//checkbox
					$fields.= "\t<td><?php echo \$form->field('{$field}')->set_template('{error_msg}{field}'); ?></td>\n" ;
				}else{//input
					$fields.= "\t<td><?php echo \$form->field('{$field}')->set_template('{error_msg}{field}'); ?></td>\n" ;
				}
				$fields.= '</tr>'."\n\n" ;
				if(in_array($field, $admins)):
					$fields.= '<?php endif; ?>'."\n" ;
				endif;
			endif;
		}
		
		if($admin_hidden_fields):
			$hidden_fields.= 'if( ! \Auth::is_admin()):'."\n{$admin_hidden_fields}\n" ;
			$hidden_fields.= 'endif;'."\n" ;
		endif;

		//mold
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'edit.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###FIELDS###', $fields , $val) ;
		$val = str_replace ('###HIDDEN_FIELDS###', $hidden_fields , $val) ;

		return $val;
	}

	/**
	 * generate_config()
	 */
	public static function generate_config($name, $cmd_orig)
	{
		//vals
		$cmds = explode(' ', $cmd_orig);
		$nicename = self::get_nicename(array_shift($cmds));

		//template
		$val = file_get_contents(LOCOMO_SCFLD_TPL_PATH.'config.php');
		$val = self::replaces($name, $val);
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

	/**
	 * remove_length()
	 */
	public static function remove_length($str)
	{
		if(preg_match('/(.*?)\[.\d+\]/', $str, $m)){
			return @$m[1];
		}else{
			return $str;
		}
	}
}
