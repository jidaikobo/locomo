<?php
namespace Output;

\Module::load('format');

class Controller_XXX extends \Locomo\Controller_Otpt
{
	public $model_name = '\Format\Model_XXX';

	public function action_output()
	{
		parent::action_output();
	}

	protected static function convert_objects($objects, $format)
	{
		foreach($objects as $key => $object)
		{
			// pdf
			if ($format->type == 'pdf')
			{
				// relation sample
				/*
				foreach ($object->relation_name as $kk => $vv)
				{
					$objects[$key]->relation_name[$kk]->relation_field_name = 'example';
				}
				*/
			}

			// excel
			if ($format->type == 'excel')
			{
			}
		}

		return $objects;
	}
}
