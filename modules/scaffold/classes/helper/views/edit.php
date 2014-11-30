<?php
namespace Scaffold;
class Helper_Views_edit extends Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmds)
	{
		$hiddens = array('status');
		$admins  = array('is_visible');
		$banned = array('modified_at', 'updated_at', 'deleted_at', 'workflow_status', 'creator_id', 'modifier_id');

		$fields = '';
		$admin_fields = '';
		$admin_hidden_fields = '';
		$hidden_fields = '';
		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			if (in_array($field, $banned)) continue;

			//hidden
			if (in_array($field, $hiddens)):
				$hidden_fields.= "\techo \$form->field('{$field}')->set_template('{error_msg}{field}');\n";
			else:
				//admin
				if (in_array($field, $admins)):
					$admin_hidden_fields.= "\t\techo \$form->field('{$field}')->set_template('{error_msg}{field}');\n";
					$fields.= '<?php if (\Auth::is_admin()): ?>'."\n";
				endif;

				$fields.= '<tr>'."\n";
				//label
				$fields.= "\t<th><?php echo \$form->field('{$field}')->set_template('{label}{required}'); ?></th>\n";
				
				//field
				if (substr($field,0,3)=='is_'){//checkbox
					$fields.= "\t<td><?php echo \$form->field('{$field}')->set_template('{error_msg}{field}'); ?></td>\n";
				}else{//input
					$fields.= "\t<td><?php echo \$form->field('{$field}')->set_template('{error_msg}{field}'); ?></td>\n";
				}
				$fields.= '</tr>'."\n\n";
				if (in_array($field, $admins)):
					$fields.= '<?php endif; ?>'."\n";
				endif;
			endif;
		}
		
		if ($admin_hidden_fields):
			$hidden_fields.= 'if ( ! \Auth::is_admin()):'."\n{$admin_hidden_fields}\n";
			$hidden_fields.= 'endif;'."\n";
		endif;

		//mold
		$val = static::fetch_temlpate('edit.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###FIELDS###', $fields, $val);
		$val = str_replace ('###HIDDEN_FIELDS###', $hidden_fields, $val);

		return $val;
	}
}
