<?php
namespace Locomo;
class Controller_Scffld_Helper_Model extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig, $type, $model)
	{
		// vals
		$cmd_mods = array();
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);// remove name
		$name = ucfirst($name);
		$table_name = \Inflector::tableize($name);
		$banned = array('modified_at', 'updated_at', 'deleted_at', 'workflow_status', 'creator_id', 'updater_id');

		// fieldset
		$field_str = '';
		$properties['id'] = '';// fuel's spec
		foreach($cmds as $field)
		{
			$is_required = strpos($field, 'null') !== false ? false : true;
			$vals        = explode(':', $field);
			$field       = $vals[0];
			unset($vals[0]);
			$attr        = $vals[1];
			unset($vals[1]);

			$default = '';
			$is_unique = false ;
			foreach ($vals as $k => $v)
			{
				if (strpos($v, 'default') !== false)
				{
					$default = isset($v) ? self::modify_default($v) : '' ;
					unset($vals[$k]);
				}
				if (strpos($v, 'unique') !== false)
				{
					$is_unique = true ;
					unset($vals[$k]);
				}
			}
			$nicename    = self::get_nicename($field);
			$field       = self::remove_nicename($field);
			$attr_nolen  = self::remove_length($attr);
			$class       = ", 'class' => '".$attr_nolen."'";
			$cmd_mods[]  = $field;

			// attribute
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
			}

			// field_str
			$items = array();

			if ( ! in_array($field, $banned))
			{
				// label
				if ($nicename)
				{
					$properties[$field]['label'] = $nicename;
				}

				// data_type
				if ($attr)
				{
					$properties[$field]['data_type'] = $attr_nolen;
				}

				// form
				$form = array();
				if (in_array($field, array('text', 'memo', 'body', 'content', 'etc', 'message'))):
					// textarea
					$form = array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;');
				elseif (substr($field,0,3)=='is_'):
					// bool
					$form = array('type' => 'select', 'options' => array(0, 1));
				elseif (substr($field,-3)=='_at'):
					// date
					$form = array('type' => 'text', 'size' => 20);
				else:
					// text
					$form = array('type' => 'text', 'size' => $size);
				endif;
				if ($form)
				{
					$form['class'] = $attr_nolen;
					$properties[$field]['form'] = $form;
				}

				// validation
				$validation = array();
				if (in_array($field, array('name', 'title', 'subject')) || $is_required)
				{
					// require
					$validation['required'] = '';
				}

				if ($is_unique)
				{
					// require
					$validation['unique'] = array($name.'.'.$field);
				}

				if ($max)
				{
					// max
					$validation['max_length'] = array($max => '');
				}

				if ($validation)
				{
					$properties[$field]['validation'] = $validation;
				}

				if ($default !== false)
				{
					$properties[$field]['default'] = $default;
				}
			}
			else
			{
				$properties[$field] = array('form' => array('type' => false));
			}
		}

		// soft_delete
		$dlt_fld = '';
		if (in_array('deleted_at', $cmd_mods))
		{
			$dlt_fld = "\tprotected static \$_soft_delete = array(\n\t\t'deleted_field'   => 'deleted_at',\n\t\t'mysql_timestamp' => true,\n\t);\n";
		}

		// observers
		$observers = '';
		if (in_array('updated_at', $cmd_mods))
		{
			$observers.= "\t\t'Orm\Observer_UpdatedAt' => array(\n\t\t\t\t'events' => array('before_update'),\n\t\t\t\t'mysql_timestamp' => true,\n\t\t\t),\n";
		}
		if (in_array('created_at', $cmd_mods))
		{
			$observers.= "\t\t'Locomo\Observer_Created' => array(\n\t\t\t'events' => array('before_insert', 'before_save'),\n\t\t\t'mysql_timestamp' => true,\n\t\t),\n";
		}
		if (in_array('expired_at', $cmd_mods))
		{
			$observers.= "\t\t'Locomo\Observer_Expired' => array(\n\t\t\t\t'events' => array('before_insert', 'before_save'),\n\t\t\t\t'properties' => array('expired_at'),\n\t\t\t),\n";
		}
		if (in_array('creator_id', $cmd_mods) || in_array('updater_id', $cmd_mods))
		{
			$observers.= "\t\t\t'Locomo\Observer_Userids' => array(\n\t\t\t'events' => array('before_insert', 'before_save'),\n\t\t),\n";
		}
		$observers.= "//\t\t\t'Locomo\Observer_Wrkflw' => array(\n//\t\t\t'events' => array('before_insert', 'before_save','after_load'),\n//\t\t),\n";
		$observers.= "//\t\t\t'Locomo\Observer_Revision' => array(\n//\t\t\t'events' => array('after_insert', 'after_save', 'before_delete'),\n//\t\t),\n";

		// error
		if ($model == 'Model_Soft' && ! in_array('deleted_at', $cmd_mods))
		{
			return 'model_soft_error';
		}

		// $field_str
		$field_str = var_export($properties, true);
		$field_str = preg_replace("/=> \n +array \(/m",
															"=> array (",
															$field_str); // konagai request :-)
		$field_str = str_replace('  ',
														 "\t",
														 $field_str);
		$field_str = preg_replace("/^/m",
															"\t",
															$field_str);
		$field_str = str_replace(" => '',",
														 ",",
														 $field_str);
		$field_str = str_replace("'default' => '\\'\\'',",
														 "'default' => '',",
														 $field_str);
		$field_str = str_replace("'default' => 'null',",
														 "'default' => null,",
														 $field_str);
		$field_str = preg_replace("/array \(\n\t+?(\d+),\n\t+?\),/m",
															"array ($1),",
															$field_str);
		// to Lang
		$field_str = preg_replace("/^(\t\t)'(.+?)' => array \(\n(\t+?)'(.*?)' => '(.*?)',\n/m",
															"\n$1// $5\n$1'$2' => array(\n",
															$field_str);

		// template
		$str = static::fetch_temlpate('model.php');

		// $model
		$str = str_replace("Model_Base", $model, $str);

		// モジュール以外では名前空間を削除
		$str = $type !== 'module' ? str_replace("namespace XXX;\n", '', $str) : $str ;
		$str = self::replaces($name, $str);
		$str = str_replace ('###NICENAME###',  $nicename, $str);
		$str = str_replace('###DLT_FLD###',    $dlt_fld,    $str);
		$str = str_replace('###OBSRVR###',     $observers,  $str);
		$str = str_replace('###TABLE_NAME###', $table_name, $str);
		$str = str_replace('###FIELD_STR###',  $field_str,  $str);

		return $str;
	}
}
