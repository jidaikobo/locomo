<?php
class Presenter_Flr_Dir_Edit extends \Presenter_Base
{
	/**
	 * form()
	 */
	public static function form($obj = NULL)
	{
		$form = parent::form($obj);

		// list of upload directories - edit
		$selected_path = '';
		$selected_id = \Request::main()->id ;
		if ($selected_id)
		{
			$selected_obj = \Model_Flr::find($selected_id);
			if (\Request::main()->action == 'edit')
			{
				$selected_path = $selected_obj ? dirname(rtrim($selected_obj->path, DS)).DS : $selected_path;
			} else {
				$selected_path = $selected_obj ? rtrim($selected_obj->path, DS).DS : $selected_path;
			}
		}

		$current_dir = @$obj->path ?: '';
		$selected = $selected_path ?: $current_dir ;
		$dirs = \Util::get_file_list(LOCOMOFLRUPLOADPATH, $type = 'dir');
		$options = array();

		foreach ($dirs as $dir)
		{
			$dir = substr($dir, strlen(LOCOMOFLRUPLOADPATH));

			// is exist on database
			if( ! \Model_Flr::find('first', array('where' => array(array('path', $dir))))) continue;

			// cannot choose myself and children
			if ($current_dir && substr($dir, 0, strlen($current_dir)) == $current_dir) continue;

			// check auth
			if ( ! \Controller_Flr::check_auth($dir, 'create_dir')) continue;

			$options[$dir] = urldecode($dir);
		}

		$form->add_after(
				'parent',
				'親ディレクトリ',
				array('type' => 'select', 'options' => $options, 'style' => 'width: 10em;'),
				array(),
				'name'
			)
			->set_value($selected);

		// genre
		$form->field('genre')->set_type('hidden')->set_value('dir');

		// is_sticky
		$form->delete('is_sticky');

		return $form;
	}
}
