<?php
namespace Scaffold;
class Helper_Model extends Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig)
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
			$is_required = strpos($field, 'null') !== false ? false : true;
			list($field, $attr) = explode(':', $field);
			$nicename = self::get_nicename($field);
			$field    = self::remove_nicename($field);
			$class    = ", 'class' => '".self::remove_length($attr)."'";
			$cmd_mods[] = $field;

			//attribute
			$default = '';
			$size = 0;
			$max = 0;
			if (preg_match('/\[(.*?)\]/', $attr, $m))
			{
				if (is_numeric($m[1]))
				{
					$max  = $m[1] ? intval($m[1]) : 0;
					$size = ($max >= 30) ? 30 : $max;
					$size = ($max == 0)  ? 30 : $size;
				}
				else
				//scalar
				{
					$default = $m[1];
				}
			}

			//field_str
			$items = array();

			if ( ! in_array($field, $banned))
			{
				//label
				if ($nicename)
				{
					$properties[$field]['label'] = $nicename;
				}
	
				//data_type
				if ($attr)
				{
					$properties[$field]['data_type'] = str_replace(array('[',']'), array('(',')'), $attr);
				}
	
				//form
				$form = array();
				if (in_array($field, array('text', 'memo', 'body', 'content', 'etc', 'message'))):
					//textarea
					$form = array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;');
				elseif (substr($field,0,3)=='is_'):
					//bool
					$form = array('type' => 'select', 'options' => array(0, 1));
				elseif (substr($field,-3)=='_at'):
					//date
					$form = array('type' => 'text', 'size' => 20);
				else:
					//text
					$form = array('type' => 'text', 'size' => $size);
				endif;
				if ($form)
				{
					$form['class'] = self::remove_length($attr);
					$properties[$field]['form'] = $form;
				}
	
				//validation
				$validation = array();
				if (in_array($field, array('name', 'title', 'subject')) || $is_required)
				{
					//require
					$validation['required'] ='';
				}
	
				if ($max)
				{
					//max
					$validation['max_length'] = array($max => '');
				}
	
				if ($validation)
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
		if (in_array('deleted_at', $cmd_mods)):
			$dlt_fld = "\tprotected static \$_soft_delete = array(\n\t\t'deleted_field'   => 'deleted_at',\n\t\t'mysql_timestamp' => true,\n\t);\n";
		endif;

		//observers
		$observers = '';
		if (in_array('created_at', $cmd_mods)):
			$observers.= "\t\t'Locomo\Observer_Created' => array(\n\t\t\t'events' => array('before_insert', 'before_save'),\n\t\t\t'mysql_timestamp' => true,\n\t\t),\n";
		endif;
		if (in_array('updated_at', $cmd_mods)):
			$observers.= "\t\t'Orm\Observer_UpdatedAt' => array(\n\t\t\t\t'events' => array('before_save'),\n\t\t\t\t'mysql_timestamp' => true,\n\t\t\t),\n";
		endif;
		if (in_array('expired_at', $cmd_mods)):
			$observers.= "\t\t'Locomo\Observer_Expired' => array(\n\t\t\t\t'events' => array('before_insert', 'before_save'),\n\t\t\t\t'properties' => array('expired_at'),\n\t\t\t),\n";
		endif;
		if (in_array('creator_id', $cmd_mods) || in_array('modifier_id', $cmd_mods)):
			$observers.= "\t\t'Locomo\Observer_Userids' => array(\n\t\t\t'events' => array('before_insert', 'before_save'),\n\t\t),\n";
		endif;
		$observers.= "//\t\t'Workflow\Observer_Workflow' => array(\n//\t\t\t'events' => array('before_insert', 'before_save','after_load'),\n//\t\t),\n";
		$observers.= "//\t\t'Revision\Observer_Revision' => array(\n//\t\t\t'events' => array('after_insert', 'after_save', 'before_delete'),\n//\t\t),\n";

		//admins
		$frmdfn = '';
		foreach ($admins as $admin)
		{
			$frmdfn.= "\$form->field('{$admin}')->set_type('hidden')->set_value(\$obj->{$admin} ?: 1)";
		}
		if ($frmdfn)
		{
			$frmdfn = "\t\tif ( ! \Auth::is_admin())\n\t\t{\n".$frmdfn."\n\t\t}\n";
		}

		//$field_str
		$field_str = var_export($properties, true);
		$field_str = str_replace('  ', "\t", $field_str);
		$field_str = preg_replace("/^/m", "\t", $field_str);
		$field_str = str_replace(" => '',", ",", $field_str);

		//template
		$str = static::fetch_temlpate('model.php');
		$str = self::replaces($name, $str);
		$str = str_replace('###FRMDFN###',     $frmdfn,     $str);
		$str = str_replace('###DLT_FLD###',    $dlt_fld,    $str);
		$str = str_replace('###OBSRVR###',     $observers,  $str);
		$str = str_replace('###TABLE_NAME###', $table_name, $str);
		$str = str_replace('###FIELD_STR###',  $field_str,  $str);

		return $str;
	}
}
