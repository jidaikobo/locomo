<?php
namespace Locomo;
trait Controller_Output_Excel
{


	public function excel($format, $objects)
	{
		$excel = $this->excel;

		//シートを設定する
		$excel->setActiveSheetIndex(0);
		$sheet = $excel->getActiveSheet();
		$excel->setSheet($sheet);

		// header(label)部をインプットする
		$excel->setXY('A', 1);
		foreach($format->element as $element)
		{
			$excel->Box($element->name, 'R');
		}

		$excel->downWard();

		// 要素をインプットする
		foreach($objects as $object)
		{
			$excel->setX('A');
			foreach($format->element as $element)
			{
				$txt = '';
				$fields = explode('}', str_replace('{', '}' , $element->txt) );
				foreach ($fields as $field)
				{
					if (isset($object->{$field})) {
						$txt.= $object->{$field};
					} else {
						$txt .= $field;
					}
				}
				$excel->Box($txt, 'R');
			}
			$excel->downWard();
		}

		$excel->output($format->name .'('.date('Ymd').')');
	}

	public function csv($format, $objects)
	{
	}
}
