<?php
namespace Locomo;
trait Controller_Otpt_Excel
{


	public function excel($objects, $format)
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
				foreach ($fields as $field_name)
				{
					// field を元に, object を txt に変換
					if (is_object($object))
					{
						// リレーションの可能性有り
						$related_str = $this->create_related_str($object, $field_name);

						if ($related_str !== false && is_string($related_str))
						{
							$txt .= $related_str;
						} // ここまでリレーションの処理

						else if (isset($object->{$field_name}))
						{
							$txt .= $object->{$field_name};
						}
						else if (isset($object[$field_name]))
						{
							$txt .= $object[$field_name];
						}
						else
						{
							$txt .= $field_name;
						}
					}
					else
					{
						$txt .= $field_name;
					}


					/*
					if (isset($object->{$field})) {

						$txt.= $object->{$field};

					} else {
						$txt .= $field;
					}
					 */

				}
				$excel->Box($txt, 'R');
			}
			$excel->downWard();
		}

		$excel->output($format->name .'('.date('Ymd').')');
	}

	public function csv($objects, $format)
	{
		$csv = array();

		// 要素をインプットする
		foreach($objects as $object)
		{
			$r_arr = array();

			foreach($format->element as $element)
			{
				$txt = '';
				$fields = explode('}', str_replace('{', '}' , $element->txt) );
				foreach ($fields as $field_name)
				{
					// field を元に, object を txt に変換
					if (is_object($object))
					{
						// リレーションの可能性有り
						$related_str = $this->create_related_str($object, $field_name);

						if ($related_str !== false && is_string($related_str))
						{
							$txt .= $related_str;
						} // ここまでリレーションの処理

						else if (isset($object->{$field_name}))
						{
							$txt .= $object->{$field_name};
						}
						else if (isset($object[$field_name]))
						{
							$txt .= $object[$field_name];
						}
						else
						{
							$txt .= $field_name;
						}
					}
					else
					{
						$txt .= $field_name;
					}


					/*
					if (isset($object->{$field})) {

						$txt.= $object->{$field};

					} else {
						$txt .= $field;
					}
					 */

				}
				$key_name = mb_convert_encoding($element->name, 'SJIS', 'UTF-8');
				$r_arr[$key_name] = $txt;
			}
			$csv[] = $r_arr;
		}


		mb_convert_variables('SJIS-win','UTF-8',$csv);

		$formated = \Format::forge($csv)->to_csv();
		$res = \Response::forge($formated, 200);
		$res->set_header('Content-Type', 'application/csv');
		$res->set_header('Content-Disposition', 'attachment; filename="'.$format->name.'.csv"');

		$res->send(true);
		die();
	}

	protected function create_related_str($object, $field_name)
	{
		if (strpos($field_name, '.') !== false)
		{
			$related_name = substr($field_name, 0, strpos($field_name, '.'));
			$related_field = substr($field_name, strpos($field_name, '.') +1);
			if (isset($object->{$related_name}))
			{
				if (is_array($object->{$related_name}))
				{
					$related_str = '';
					foreach ($object->{$related_name} as $v)
					{
						isset($v->{$related_field}) &&
						$related_str .= $v->{$related_field} . ', ';
					}
					$related_str = rtrim(rtrim($related_str), ',');
				}
				else
				{
					isset($object->{$related_name}->{$related_field}) &&
					$related_str = $object->{$related_name}->{$related_field};
				}
			}
			return $related_str;
		}
		else
		{
			return false;
		}
	}

}
