<?php
namespace Locomo;
class Controller_Scffld_Helper_Views_Index extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig, $is_admin = false, $model = 'Model_Base', $is_module = false)
	{
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);//remove name

		$thead = "\t\t\t<th class=\"ar min\"><?php echo \Pagination::sort('id', 'ID', false);?></th>\n";
		$tbody = "\t<td><?php echo \$item->id; ?></td>\n";

		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$nicename = self::get_nicename($field);
			$field    = self::remove_nicename($field);
			if (empty($nicename)) continue;

			//th
			if ($is_admin):
				$thead.= "\t\t\t<th><?php echo \Pagination::sort('{$field}', '{$nicename}', false);?></th>\n";
			else:
				$thead.= "\t\t\t<th>".$nicename."</th>\n";
			endif;

			//td
			if (substr($field,0,3)=='is_'):
				$tdv = "<?php echo \$item->{$field} ? 'Yes' : 'No'; ?>";
			else:
				$tdv = "<?php echo \$item->{$field}; ?>";
			endif;

			if ($is_admin):
				$tbody.= "\t<td><div class=\"col_scrollable\">{$tdv}</div></td>\n";
			else:
				$tbody.= "\t<td>{$tdv}</td>\n";
			endif;
		}

		//mold
		$tpl_adm = $model == 'Model_Base_Soft' ? 'index_admin_model_soft.php' : 'index_admin.php';
		$tpl = $is_admin ? $tpl_adm : 'index.php';
		$val = static::fetch_temlpate($tpl);
		if ( ! $is_module)
		{
			$val = str_replace ('\\XXX\\Presenter', '\\Presenter', $val);
			$val = str_replace ('\\XXX\\Controller', '\\Controller', $val);
		}
		$val = self::replaces($name, $val);
		$val = str_replace ('###THEAD###', $thead, $val);
		$val = str_replace ('###TBODY###', $tbody, $val);

		return $val;
	}
}
