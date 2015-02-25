<?php
namespace Locomo;



class Controller_Pdf extends \Locomo\Controller_Base
// class Controller_Pdf extends \Controller
{
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 10,
		'is_for_admin' => false,
		'main_action' => 'action_index_admin',
		'nicename' => 'PDF',
	);



	public function before() {
		parent::before();
		\Package::load('pdf');
		$this->pdf = \Pdf::factory('fpdi')->init();
		// define('PDF_TEMP_PATH', PKGPATH.'pdf/templates/');
		define('PDF_TEMP_PATH', APPPATH.'/locomo/assets/pdftemplate/');

		\Fuel\Core\Fuel::$profiling = false;
	}

	public function after($response)
	{
		// response object を返すと template が返るので pdf がレンダリングされない
		exit();
	}

/* ==============================
 * 宛名印刷 単体 A4
============================== */
	public function addressee(
		$title = '宛名',
		$customers,
		$field_format_default = array(
			'name' => 'name',
			'zip' => 'zip',
			'address' => 'address'
		),
		$width = 150,
		$margin = array(0, 0),
		$cr="",
		$name_align = 'L',
		$change_format = false, // function change format
		$rotate = 0,
		$rotate_x = 115,
		$rotate_y = 0
	){

		if (!$customers) {return;} // throw error;

		$margin_x = $margin[0];
		$margin_y = $margin[1];

		$pdf = $this->pdf;
		$pdf->SetMargins(0, 0, 0);
		if (\Input::get('test')) {
			$pdf->setSourceFile(APPPATH.'locomo/assets/pdftemplate/output/' . \Input::get('test'));
			$t_page = $pdf->importPage(1);
		}

		$pdf->SetTitle($title);
		// pdf init
		$pdf->setFont('kozminproregular');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetAutoPageBreak(false);

		if (\Input::get('test')) $pdf->SetTextColor(0,0,125);

		// 配列に変換
		if (!is_array($customers)) $customers = array($customers);

		foreach ($customers as $customer) {
			$pdf->AddPage('P', 'A4');
			if (\Input::get('test')) $pdf->useTemplate(1);

			// フィールドを条件によって変えたいときにコールバック呼び出す
			$field_format = $field_format_default;

			if (is_callable($change_format)) {
				$ff = $change_format($customer);
				if (is_array($ff)) $field_format = $ff;
			}

			// field zip
			$zip = '';
			if (is_array($field_format['zip']) and count($field_format['zip']) == 2) {
				$zip = '〒' . $customer->{$field_format['zip'][0]} . '-' .  $customer->{$field_format['zip'][1]};
			} elseif ( (is_array($field_format['zip']) and count($field_format['zip']) == 1) OR is_int($field_format['zip']) ) {
				$zip = '〒' . $customer->{$field_format['zip'][0]};
			} elseif ( is_string($field_format['zip']) ) {
				$zip = '〒' . $customer->{$field_format['zip']};
			}

			// field address
			$address = '';
			$field_format_address = $field_format['address'];
			if (!is_array($field_format_address)) $field_format_address = array($field_format_address);
			foreach ($field_format_address as $key => $val) {
				if (isset($customer->{$field_format_address[$key]})) {
					$address .= $customer->{$field_format_address[$key]} . $cr;
				} else {
					$address .=$field_format_address[$key];
				}
			}

			// field name
			$name = '';
			$field_format_name = $field_format['name'];
			if (!is_array($field_format_name)) $field_format_name = array($field_format_name);
			foreach ($field_format_name as $key => $val) {
				if (isset($customer->{$field_format_name[$key]})) {
					$name .= $customer->{$field_format_name[$key]};
				} else {
					$name .=$field_format_name[$key];
				}
			}

			// $name .= (array_key_exists('title',$customer::properties()) and $customer->title) ? "　" . $customer->title : "　様";


			if ($rotate) {
				$pdf->StartTransform();
				$pdf->Rotate($rotate, $rotate_x , $rotate_y);
			}



			// output customer id
			$fs = min($width / 24 / MM_PER_POINT, 11);
			$pdf->SetFontSize($fs/1.7);
			$pdf->SetXY($margin_x, $margin_y);
			$pdf->MultiCell($width, $fs*MM_PER_POINT, $customer->id, 0, 'R', 0, 2);

			// output zip & address
			$fs = min($width / 24 / MM_PER_POINT, 11);
			$pdf->SetFontSize($fs);
			$pdf->SetXY($margin_x, $margin_y + $fs*MM_PER_POINT);
			$pdf->MultiCell($width, $fs*2*MM_PER_POINT, $zip . "\n" . $address . '', 0, 'L', 0, 2);

			// output name
			$fs = min($width / 20 / MM_PER_POINT, 13);
			$pdf->SetFontSize($fs);
			$y = $pdf->GetY();
			$y = (is_float($y) or is_int($y)) ? $y : 0;
			$pdf->SetXY($margin_x+($width*0.05), $y + $fs*MM_PER_POINT);
			$pdf->MultiCell($width*0.95, $fs*2*MM_PER_POINT, $name, 0, $name_align);


			if ($rotate) $pdf->StopTransform();



		} //endforeach;

		$pdf->Output($title . ".pdf", "I");

	}



/* ==============================
 * 宛名 タックシール
============================== */
	public function tack_seal(
		$title = '',
		$customers = null,
		$field_format_default = array(
			'name' => 'name',
			'zip' => 'zip',
			'address' => 'address'
		),
		$cell = array(2, 5),
		$margin = array(21, 19, 21, 19),
		$cr = "",
		$change_format = false
	) {

		if (!$customers) {return;} // throw error;

		if (is_int($margin)) {
			$margin = array($margin, $margin, $margin, $margin);
		} elseif (is_array($margin)) {
			if (!isset($margin[1])) $margin[1] = $margin[0];
			if (!isset($margin[2])) $margin[2] = $margin[0];
			if (!isset($margin[3])) $margin[3] = $margin[1];
		}

		if (!$cr) $cr = "" ;

		// pdf init
		$pdf = $this->pdf;
		$pdf->setFont('kozminproregular');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetAutoPageBreak(false);


		// 初期値をセット
		$cnt = count($customers);
		$page = 0;
		$cols = 0;
		$rows = 0;
		$refer_point = array('x' => 0, 'y' => 0); //左上の基準点
		$width = (210 - ($margin[1] + $margin[3])) / $cell[0];
		$height = (297 - ($margin[0] + $margin[2])) / $cell[1];
		$margin_x = $margin[3];
		$margin_y = $margin[0];

		$post_per_page = $cell[0] * $cell[1];

		$num_of_page = $cnt/$post_per_page;

		$i = 0; // 何件目?

		foreach ($customers as $customer) {
			if ($i%$post_per_page == 0) { // 1件目
				$pdf->add_page();
				$refer_point = array('x' => 0, 'y' => 0); //初期化
			} else {
				if ($i%$cell[0] == 0) { // 列判定 改行
					$refer_point['y'] += $height;
					$refer_point['x'] = 0;
				} else {
					$refer_point['x'] += $width;
				}
			}
			if (\Input::get('test')) $pdf->Rect($refer_point['x']+$margin_x, $refer_point['y']+$margin_y, $width, $height);

			// フィールドを条件によって変えたいときにコールバック呼び出す
			$field_format = $field_format_default;

			if (is_callable($change_format)) {
				$ff = $change_format($customer);
				if (is_array($ff)) $field_format = $ff;
			}

			// field zip
			$zip = '';
			if (is_array($field_format['zip']) and count($field_format['zip']) == 2) {
				$zip = '〒' . $customer->{$field_format['zip'][0]} . '-' .  $customer->{$field_format['zip'][1]};
			} elseif ( (is_array($field_format['zip']) and count($field_format['zip']) == 1) OR is_int($field_format['zip']) ) {
				$zip = '〒' . $customer->{$field_format['zip'][0]};
			} elseif ( is_string($field_format['zip']) ) {
				$zip = '〒' . $customer->{$field_format['zip']};
			}

			// field address
			$address = '';
			if (is_array($field_format['address'])) {
				foreach ($field_format['address'] as $key => $val) {
					if (isset($customer->{$field_format['address'][$key]})) {
						$address .= $customer->{$field_format['address'][$key]} . $cr;
					} else {
						$address .= $val;
					}

				}
			} elseif ( is_string($field_format['address']) ) {
				$address = $customer->{$field_format['address']};
			}

			// field name
			$name = '';
			if (is_array($field_format['name'])) {
				$sp_flg = false;
				foreach ($field_format['name'] as $key => $val) {
					if ($sp_flg) $name .= '　';
					if (isset($customer->{$field_format['name'][$key]})) {
						$name .= $customer->{$field_format['name'][$key]};
					} else {
						$name .= $val;
					}

					if (!$sp_flg) $cr_flg = true;
				}
			} elseif ( is_string($field_format['name']) ) {
				$name = $customer->{$field_format['name']};
			}

			// $name .= (array_key_exists('title',$customer::properties()) and $customer->title) ? "　" . $customer->title : "　様";


			// output zip & address
			$fs = min($width / 24 / MM_PER_POINT, 11);
			$pdf->SetFontSize($fs);
			$pdf->SetXY($margin_x + $refer_point['x'] + $width*0.05, $margin_y + $refer_point['y'] + $height*0.05);
			$pdf->MultiCell($width*0.9, $height*0.45, $zip . "\n" . $address . '', 0, 'L');

			// output name
			$fs = min($width / 20 / MM_PER_POINT, 13);
			$pdf->SetFontSize($fs);
			$pdf->SetXY($margin_x + $refer_point['x'] + $width*0.15, $margin_y + $refer_point['y'] + $height*0.55);
			$pdf->MultiCell($width*0.8, $height*0.45, $name . '' , 0, 'L');

			$i++;

		}

		if (!$title) $title = 'tackseal(' . $cell[0] . 'x' . $cell[1] . ')';
		$pdf->output($title . '.pdf', 'I');
	}



/* ==============================
 * 宛名
 * 郵便はがき
 * 名前を連名にするには $delimiter をいれる
 * company, address 列の列を増やしたい時は2,3にフィールド名を設定する
============================== */
	protected function letter(
		$title = '郵便はがき',
		$customers = null,
		$field_format_default = array(
			'name'=>'name',
			'company' => 'company',
			// 'company2' => 'company2',
			// 'company3' => 'company3',
			'post' => 'post',
			'zip'=>'zip',
			'address'=>'address',
			// 'address2'=>'address2',
			// 'address3'=>'address3',
		),
		$margin = array(0, 0),
		$delimiter = '・'
	) {

		$margin_x = $margin[0];
		$margin_y = $margin[1];

		$pdf = $this->pdf;

		// pdf init
		$pdf->setSourceFile(APPPATH.'locomo/assets/pdftemplate/letter_test.pdf');
		$page = $pdf->importPage(1);
		$pdf->SetMargins(0, 0, 0);
		$pdf->setFont('kozminproregular');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetAutoPageBreak(false);
		$pdf->SetFontSize(12);

		foreach ($customers as $customer) {

			$pdf->addPage('P', array(148, 100));
			if (\Input::get('test')) $pdf->useTemplate(1);

			// フィールドを条件によって変えたいときにコールバック呼び出す
			$field_format = $field_format_default;

			if (is_callable($change_format)) {
				$ff = $change_format($customer);
				if (is_array($ff)) $field_format = $ff;
			}


			// field zip
			$zip = '';
			if (is_array($field_format['zip']) and count($field_format['zip']) == 2) {
				$zip3 = $customer->{$field_format['zip'][0]};
				$zip4 = $customer->{$field_format['zip'][1]};
			} elseif ( (is_array($field_format['zip']) and count($field_format['zip']) == 1) OR is_int($field_format['zip']) ) {
				$zip = explode('-', $customer->{$field_format['zip'][0]});
				$zip3 = $zip[0];
				$zip4 = $zip[1];
			} 
			if ( is_string($field_format['zip']) ) {
				$zip = explode('-', $customer->{$field_format['zip']});
				$zip3 = $zip[0];
				$zip4 = $zip[1];
			}

			// field address
			$address = '';
			if (is_array($field_format['address'])) {
				foreach ($field_format['address'] as $key => $val) {
					$address .= $customer->{$field_format['address'][$key]};
				}
			} elseif ( is_string($field_format['address']) ) {
				$address = $customer->{$field_format['address']};
			}
			// field address2
			$address2 = '';
			if (isset($field_format['address2'])) {
				if (is_array($field_format['address2'])) {
					foreach ($field_format['address2'] as $key => $val) {
						$address2 .= $customer->{$field_format['address2'][$key]};
					}
				} elseif ( is_string($field_format['address2']) ) {
					$address2 = $customer->{$field_format['address2']};
				}
			}
			// field address2
			$address3 = '';
			if (isset($field_format['address3'])) {
				if (is_array($field_format['address3'])) {
					foreach ($field_format['address3'] as $key => $val) {
						$address3 .= $customer->{$field_format['address3'][$key]};
					}
				} elseif ( is_string($field_format['address3']) ) {
					$address3 = $customer->{$field_format['address3']};
				}
			}

			// field post
			$post = '';
			if (isset($field_format['post'])) {
				if (is_array($field_format['post'])) {
					foreach ($field_format['post'] as $key => $val) {
						$post .= $customer->{$field_format['post'][$key]};
					}
				} elseif ( is_string($field_format['post']) ) {
					$post = $customer->{$field_format['post']};
				}
			}

			// field company
			$company = '';
			if (is_array($field_format['company'])) {
				foreach ($field_format['company'] as $key => $val) {
					$company .= $customer->{$field_format['company'][$key]};
				}
			} elseif ( is_string($field_format['company']) ) {
				$company = $customer->{$field_format['company']};
			}
			// field company2
			$company2 = '';
			if (isset($field_format['company2'])) {
				if (is_array($field_format['company2'])) {
					foreach ($field_format['company2'] as $key => $val) {
						$company2 .= $customer->{$field_format['company2'][$key]};
					}
				} elseif ( is_string($field_format['company2']) ) {
					$company2 = $customer->{$field_format['company2']};
				}
			}
			// field company3
			$company3 = '';
			if (isset($field_format['company3'])) {
				if (is_array($field_format['company3'])) {
					foreach ($field_format['company3'] as $key => $val) {
						$company3 .= $customer->{$field_format['company3'][$key]};
					}
				} elseif ( is_string($field_format['company3']) ) {
					$company3 = $customer->{$field_format['company3']};
				}
			}

			// field name
			$name = '';
			if (is_array($field_format['name'])) {
				$sp_flg = false;
				foreach ($field_format['name'] as $key => $val) {
					if ($sp_flg) $name .= '　';
					$name .= $customer->{$field_format['name'][$key]};
					$sp_flg = true;
				}
			} elseif ( is_string($field_format['name']) ) {
				$name = $customer->{$field_format['name']};
			}


			// output zip
			$pdf->text_horizontal(44.5+$margin_x, 13+$margin_y, $zip3, 58, 'left', 18, 3);
			$pdf->text_horizontal(66+$margin_x, 13+$margin_y, $zip4, 58, 'left', 18, 2.9);

			// output address
			$pdf->text_vertical(90+$margin_x, 30+$margin_y, $address, 115, 'top', 14, 1, true, 1);
			$pdf->text_vertical(84+$margin_x, 33+$margin_y, $address2, 112, 'top',14, 1, true, 1);
			$pdf->text_vertical(78+$margin_x, 36+$margin_y, $address3, 109, 'top',14, 1, true, 1);

			// output company
			$pdf->text_vertical(70+$margin_x, 32+$margin_y, (string)$company, 113, 'top', 12, 1);
			$pdf->text_vertical(65+$margin_x, 34+$margin_y, (string)$company2, 111, 'top', 11, 1);
			$pdf->text_vertical(60+$margin_x, 36+$margin_y, (string)$company3, 109, 'top', 11, 1);

			// output post
			$post_length = mb_strlen($post, 'UTF-8');
			if ($post_length <= 6) {
				$pdf->text_vertical(50.5+$margin_x, 23+$margin_y, (string)$post, 9*6*MM_PER_POINT, 'top', 9, 0);
			} elseif ($post_length <= 12) { // 2 列
				$pdf->text_vertical(52.5+$margin_x, 23+$margin_y, (string)mb_substr($post, 0 , 6, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(48.5+$margin_x, 23+$margin_y, (string)mb_substr($post, 6, null, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
			} elseif ($post_length <= 18) { // 3 列
				$pdf->text_vertical(54.5+$margin_x, 23+$margin_y, (string)mb_substr($post, 0 , 6, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(50.5+$margin_x, 23+$margin_y, (string)mb_substr($post, 6 , 6, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(46.5+$margin_x, 23+$margin_y, (string)mb_substr($post, 12, null, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
			} else { // それ以上
				$len = intval($post_length/3);
				$len_ex = $post_length%3;
				if ($len_ex) {
					$len++;
					$len_ex = $len - ($post_length - $len*2);
					for ($n=0;$n<$len_ex;$n++) { $post .= '  '; }
				}
				$pdf->text_vertical(54.5+$margin_x, 23+$margin_y, (string)mb_substr($post, 0 , $len, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(50.5+$margin_x, 23+$margin_y, (string)mb_substr($post, $len , $len, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(46.5+$margin_x, 23+$margin_y, (string)mb_substr($post, $len*2, null, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
			}


			// output name
			// 連名の処理
			if (mb_substr_count($name, $delimiter, 'UTF-8')) {
				$arr = explode(' ', str_replace('　', ' ', $name));
				if (count($arr) == 2) {
					$last_name = $arr[0];
					$first_name = $arr[1];
					$first_name_arr = explode($delimiter, $arr[1]);
					$pdf->text_vertical(52.8+$margin_x, 44+$margin_y, $last_name , 30, "center",22, 0, true, 2);

					$last_name_length = mb_strlen($last_name, 'UTF-8');
					$name_length = 0;
					foreach ($first_name_arr as $val) {
						$name_length = max($name_length, mb_strlen($val, 'UTF-8'));
					}
					for ($u=0;$u<count($first_name_arr);$u++) {
						$pdf->text_vertical(52.8 - 7.6*$u+$margin_x, 44 + 7.76 + 7.76*$last_name_length+$margin_y, $first_name_arr[$u] , 60, "center",22, 1, true, 2);
						$pdf->text_vertical(52.8 - 7.6*$u+$margin_x, 44 + 7.76 + 7.76 + (7.76*($name_length + $last_name_length))+$margin_y, '様' , 12, "top", 22, 1);
					}
				} else {
					$pdf->text_vertical(52+$margin_x, 44, $name+$margin_y, 100, 'top', 22, 1);
				}
			} else {
				$name .= isset($customer->title) ? "　" . $customer->title : "　様";
				$pdf->text_vertical(52.8+$margin_x, 44+$margin_y, $name, 150 - 44, 'top', 22, 1, true, 2);
			}
		}
		if (!$title) $title =' 郵便はがき';
		$pdf->Output($title . ".pdf", "I");
	}



/* ==============================
 * 宛名 封筒 90x205
============================== */
	protected function envelope(
		$title = '封筒',
		$customers = null,
		$field_format_default = array(
			'name'=>'name',
			'company' => 'company',
			// 'company2' => 'company2',
			'post' => 'post',
			'zip'=>'zip',
			'address'=>'address'
			// 'address2'=>'address2'
		),
		$margin = array(0, 0),
		$delimiter = '・',
		$change_format
	) {

		$margin_x = $margin[0];
		$margin_y = $margin[1];

		$pdf = $this->pdf;

		$pdf->SetMargins(0, 0, 0);
		$pdf->setSourceFile(APPPATH.'locomo/assets/pdftemplate/env90x205_test.pdf');
		$pdf->setFont('kozminproregular');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetAutoPageBreak(false);
		$pdf->SetFontSize(12);

		$page = $pdf->importPage(1);


		foreach ($customers as $customer) {

			// $pdf->add_page(GOTHIC, 'env90x205.pdf');

			$pdf->addPage('P', array(90, 205));
			if (\Input::get('test')) $pdf->useTemplate(1);


			// フィールドを条件によって変えたいときにコールバック呼び出す
			$field_format = $field_format_default;

			if (is_callable($change_format)) {
				$ff = $change_format($customer);
				if (is_array($ff)) $field_format = $ff;
			}


			// field zip
			$zip = '';
			if (is_array($field_format['zip']) and count($field_format['zip']) == 2) {
				$zip3 = $customer->{$field_format['zip'][0]};
				$zip4 = $customer->{$field_format['zip'][1]};
			} elseif ( (is_array($field_format['zip']) and count($field_format['zip']) == 1) OR is_int($field_format['zip']) ) {
				$zip = explode('-', $customer->{$field_format['zip'][0]});
				$zip3 = $zip[0];
				$zip4 = $zip[1];
			} 
			if ( is_string($field_format['zip']) ) {
				$zip = explode('-', $customer->{$field_format['zip']});
				$zip3 = $zip[0];
				$zip4 = $zip[1];
			}

			// field address
			$address = '';
			if (is_array($field_format['address'])) {
				foreach ($field_format['address'] as $key => $val) {
					$address .= $customer->{$field_format['address'][$key]};
				}
			} elseif ( is_string($field_format['address']) ) {
				$address = $customer->{$field_format['address']};
			}
			// field address2
			$address2 = '';
			if (isset($field_format['address2'])) {
				if (is_array($field_format['address2'])) {
					foreach ($field_format['address2'] as $key => $val) {
						$address2 .= $customer->{$field_format['address2'][$key]};
					}
				} elseif ( is_string($field_format['address2']) ) {
					$address2 = $customer->{$field_format['address2']};
				}
			}

			// field post
			$post = '';
			if (isset($field_format['post'])) {
				if (is_array($field_format['post'])) {
					foreach ($field_format['post'] as $key => $val) {
						$post .= $customer->{$field_format['post'][$key]};
					}
				} elseif ( is_string($field_format['post']) ) {
					$post = $customer->{$field_format['post']};
				}
			}

			// field company
			$company = '';
			if (is_array($field_format['company'])) {
				foreach ($field_format['company'] as $key => $val) {
					$company .= $customer->{$field_format['company'][$key]};
				}
			} elseif ( is_string($field_format['company']) ) {
				$company = $customer->{$field_format['company']};
			}
			// field company2
			$company2 = '';
			if (isset($field_format['company2'])) {
				if (is_array($field_format['company2'])) {
					foreach ($field_format['company2'] as $key => $val) {
						$company2 .= $customer->{$field_format['company2'][$key]};
					}
				} elseif ( is_string($field_format['company2']) ) {
					$company2 = $customer->{$field_format['company2']};
				}
			}

			// field name
			$name = '';
			if (is_array($field_format['name'])) {
				$sp_flg = false;
				foreach ($field_format['name'] as $key => $val) {
					if ($sp_flg) $name .= '　';
					$name .= $customer->{$field_format['name'][$key]};
					$sp_flg = true;
				}
			} elseif ( is_string($field_format['name']) ) {
				$name = $customer->{$field_format['name']};
			}



			// output zip
			$pdf->setFontSize(18);
			$pdf->text_horizontal(34+$margin_x,   12.5+$margin_y, substr($zip3, 0, 1), 0, 'left', 18, 0);
			$pdf->text_horizontal(41+$margin_x,   12.5+$margin_y, substr($zip3, 1, 1), 0, 'left', 18, 0);
			$pdf->text_horizontal(48+$margin_x,   12.5+$margin_y, substr($zip3, 2, 1), 0, 'left', 18, 0);
			$pdf->text_horizontal(55.5+$margin_x, 12.5+$margin_y, substr($zip4, 0, 1), 0, 'left', 18, 0);
			$pdf->text_horizontal(62+$margin_x,   12.5+$margin_y, substr($zip4, 1, 1), 0, 'left', 18, 0);
			$pdf->text_horizontal(69+$margin_x,   12.5+$margin_y, substr($zip4, 2, 1), 0, 'left', 18, 0);
			$pdf->text_horizontal(76+$margin_x,   12.5+$margin_y, substr($zip4, 3, 1), 0, 'left', 18, 0);


			// output address
			$pdf->text_vertical(80+$margin_x, 40+$margin_y, $address, 115, 'top', 14, 1, true, 1);
			$pdf->text_vertical(74+$margin_x, 43+$margin_y, $address2, 112, 'top',14, 1, true, 1);

			// output company
			$pdf->text_vertical(65+$margin_x, 42+$margin_y, (string)$company, 113, 'top', 11, 1);
			$pdf->text_vertical(60+$margin_x, 44+$margin_y, (string)$company2, 111, 'top', 11, 1);

			// output post
			$post_length = mb_strlen($post, 'UTF-8');
			if ($post_length <= 8) {
				$pdf->text_vertical(47.5+$margin_x, 28+$margin_y, (string)$post, 9*8*MM_PER_POINT, 'top', 9, 0);
			} elseif ($post_length <= 16) { // 2 列
				$pdf->text_vertical(49.5+$margin_x, 28+$margin_y, (string)mb_substr($post, 0 , 6, 'UTF-8'), 9*8*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(45.5+$margin_x, 28+$margin_y, (string)mb_substr($post, 6, null, 'UTF-8'), 9*8*MM_PER_POINT, 'top', 9, 0);
			} elseif ($post_length <= 24) { // 3 列
				$pdf->text_vertical(51.5+$margin_x, 28+$margin_y, (string)mb_substr($post, 0 , 6, 'UTF-8'), 9*8*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(47.5+$margin_x, 28+$margin_y, (string)mb_substr($post, 6 , 6, 'UTF-8'), 9*8*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(43.5+$margin_x, 28+$margin_y, (string)mb_substr($post, 12, null, 'UTF-8'), 9*8*MM_PER_POINT, 'top', 9, 0);
			} else { // それ以上
				$len = intval($post_length/3);
				$len_ex = $post_length%3;
				if ($len_ex) {
					$len++;
					$len_ex = $len - ($post_length - $len*2);
					for ($n=0;$n<$len_ex;$n++) { $post .= '  '; }
				}
				// var_dump($len); die();
				$pdf->text_vertical(51.5+$margin_x, 28+$margin_y, (string)mb_substr($post, 0 , $len, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(47.5+$margin_x, 28+$margin_y, (string)mb_substr($post, $len , $len, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
				$pdf->text_vertical(43.5+$margin_x, 28+$margin_y, (string)mb_substr($post, $len*2, null, 'UTF-8'), 9*6*MM_PER_POINT, 'top', 9, 0);
			}


			// output name
			// 連名の処理
			if (mb_substr_count($name, $delimiter, 'UTF-8')) {
				$arr = explode(' ', str_replace('　', ' ', $name));
				if (count($arr) == 2) {
					$last_name = $arr[0];
					$first_name = $arr[1];
					$first_name_arr = explode($delimiter, $arr[1]);
					$pdf->text_vertical(50+$margin_x, 49+$margin_y, $last_name , 30, "center",22, 0, true, 2);

					$last_name_length = mb_strlen($last_name, 'UTF-8');
					$name_length = 0;
					foreach ($first_name_arr as $val) {
						$name_length = max($name_length, mb_strlen($val, 'UTF-8'));
					}
					for ($u=0;$u<count($first_name_arr);$u++) {
						$pdf->text_vertical(50 - 7.6*$u+$margin_x, 49 + 7.76 + 7.76*$last_name_length+$margin_y, $first_name_arr[$u] , 60, "center",22, 1, true, 2);
						$pdf->text_vertical(50 - 7.6*$u+$margin_x, 49 + 7.76 + 7.76 + (7.76*($name_length + $last_name_length))+$margin_y, '様' , 12, "top", 22, 1);
					}
				} else {
					$pdf->text_vertical(50+$margin_x, 49+$margin_y, $name, 150 - 44, 'top', 22, 1, true, 2);
				}
			} else {
				//var_dump(44); die();
				$name .= isset($customer->title) ? "　" . $customer->title : "　様";
				$pdf->text_vertical(50+$margin_x, 49+$margin_y, $name, 150 - 44, 'top', 22, 1, true, 2);
			}

		}

		if (!$title) $title = '封筒';
		$pdf->Output($title . ".pdf", "I");
	}



	/*
	 * todo delete
	 * dompdf test
	 */
	public function action_dom_test () {

		$dompdf = \Pdf::factory('dompdf')->init();

		$html =
			'<html><body>'.
			'<style>body{font-family:"ipagp";}</style>'.
			''.
		  '<p>日本語 どないでっしゃろ '.
		  'templating system.</p>'.
		  '</body></html>';

		$dompdf = new \DOMPDF();
		$dompdf->load_html($html);
		$dompdf->render();
		$dompdf->stream("sample.pdf", array("Attachment" => 0));

	}


}

