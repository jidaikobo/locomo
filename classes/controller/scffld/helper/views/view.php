<?php
namespace Locomo;
class Controller_Scffld_Helper_Views_view extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig)
	{
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);//remove name
		$banned = array('workflow_status', 'creator_id', 'modifier_id', 'is_visible');

		$fields = '';

		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$nicename = self::get_nicename($field);
			$field    = self::remove_nicename($field);
			if (in_array($field, $banned)) continue;

			$fields.= "<?php if (\$item->{$field}): ?>\n";
			$fields.= '<tr>'."\n";
			$fields.= "\t<th>".$nicename."</th>\n";
			if (substr($field,0,3)=='is_'):
				$fields.= "\t<td><?php echo \$item->{$field} ? 'Yes' : 'No'; ?></td>\n";
			else:
				$fields.= "\t<td><?php echo \$item->{$field}; ?></td>\n";
			endif;
			$fields.= '</tr>'."\n\n";
			$fields.= '<?php endif; ?>'."\n";
		}
		
		//mold
		$val = static::fetch_temlpate('view.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###fields###', $fields, $val);
	
		return $val;
	}
}
