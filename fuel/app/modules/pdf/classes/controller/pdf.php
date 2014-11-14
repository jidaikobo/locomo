<?php
namespace Pdf;
class Controller_Pdf extends \Controller
{
	//trait
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;
	protected $test_datas = array(
		'name'    => 'text',
		'kana' => 'text',
		'user_type'     => 'text:test',
		'volunteer_insurance_type'        => 'text:test',
		'dm_address'        => 'text:test',
		'dm_issue_type'        => 'text:test',
		'is_death'        => 'bool',
		'status'       => 'text:public',
		'creator_id'   => 'int',
		'modifier_id'  => 'int',
	);




	public function before() {
		\Package::load('pdf');
		$this->pdf = Pdf::factory('fpdi')->init();
		// define('PDF_TEMP_PATH', PKGPATH.'pdf/templates/');
		// define('PDF_TEMP_PATH', PKGPATH . '');
	}

	public function action_index() {

		// if (!\Input::post() or !\Security::check_token()) throw new \HttpNotFoundException;

		// fpdi tcpdf を使い分ける場合は、action 毎に
		$pdf = Pdf::factory('fpdi')->init('P', 'mm', 'A4', 'UTF-8', false);

		//PDF付加情報
		// $pdf->setSourceFile(PKGPATH.'assets/pdftemplate/A4grid.pdf');

		$pdf->SetCreator('制作');
		$pdf->SetAuthor('作者');
		$pdf->SetTitle('タイトル');
		$pdf->SetSubject('タイトル2');

		//ヘッダーフッター情報
//		$pdf->setHeaderFont(array('kozminproregular', '', 14));
//		$pdf->setFooterFont(array('kozminproregular', '', 9));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);



		$pdf->setFont('kozminproregular');

$customer = array(
	'name' => '匿名 係長',
	'zip' => '999-7483',
	'address' => '東京新宿歌舞伎町220-69',
);

		//$pdf->AddPage('P', array(300, 100));
		$pdf->AddPage();
		// $pdf->SetMargins(15,20,15);
		$pdf->setFontSize(9);
		
		$pdf->Text(15,20, '〒' . $customer['zip'], false, false, true, 0, 2);
		$pdf->Text(15,25,  $customer['address'], false, false, true, 0, 2);
		$pdf->Text(15,55, $customer['name'] . ' 様', false, false, true, 0, 2);
		// $page = $pdf->importPage(1);
		// $pdf->useTemplate($page);
$test_text = "
ご寄贈くださいまして誠にありがとうございました。厚くお礼申し上げます。\n
障害者福祉の為に、ご期待に添いますように使わせていただきます。\n
ご厚情を深謝申し上げますとともに、今後とも、何とぞ宜しくご支援の程お願い申し上げます。\n
";

		$pdf->setFontSize(14);

		$pdf->setXY(60, 90, true);
		$pdf->Cell(90, 7, 'お礼状', 'B', 2, 'C');
		$pdf->setFontSize(13);
		$pdf->Cell(90, 30, '金 ' . number_format(35805) . ' 円', 0, 2, 'C');
		$pdf->setFontSize(10);
		$pdf->setLastH(0);
		$pdf->MultiCell(180, 5, $test_text, 0, 'L', false, 2, 15);



		$pdf->setFontSize(9);
		$pdf->Text(200,250, date('Y年m月d日', strtotime('2014-08-09')), false, false, true, 0, 2, 'R');
		$pdf->Text(200,255, '社会福祉法人 京都ライトハウス', false, false, true, 0, 2, 'R');
		$pdf->Text(200,260, '理事長  神谷 俊昭', false, false, true, 0, 2, 'R');

		// $pdf->text_vertical(100, 100, '縦書きのテスト1902');


		/* PDF を出力します */
		$pdf->Output("output.pdf", "I");
	}



	public function action_letter() {

		if (!\Input::post()) throw new \HttpNotFoundException;
		//if (!\Auth::is_user()) throw new \HttpNotFoundException;

		// fpdi tcpdf を使い分ける場合は、action 毎に
		$pdf = Pdf::factory('fpdi')->init('P', 'mm', 'A4', 'UTF-8', false);

		/*
		 * datas
		 */
		$date = \Input::post('letter_date') ?: date('Y-m-d');

		//PDF付加情報
		// $pdf->setSourceFile(PKGPATH.'assets/pdftemplate/A4grid.pdf');

		$pdf->SetCreator('制作');
		$pdf->SetAuthor('作者');
		$pdf->SetTitle('礼状印刷' . date('Y年 n月d日' , strtotime($date)));
		//$pdf->SetSubject('タイトル2');

		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		$pdf->setFont('kozminproregular');


		\Module::load('supportcontribute');
		$items = \Supportcontribute\Model_Supportcontribute::find('all', array(
			'where' => array(
				array('id', 'IN', \Input::post('ids')),
			),
		));
		foreach ($items as $item) {

		//$pdf->AddPage('P', array(300, 100));
		$pdf->AddPage();
		// $pdf->SetMargins(15,20,15);
		$pdf->setFontSize(9);
		
		$pdf->Text(15,20, '〒' . $item->customer['zip'], false, false, true, 0, 2);
		$pdf->Text(15,25,  $item->customer['address'], false, false, true, 0, 2);
		$pdf->Text(15,55, $item->customer['name'] . ' 様', false, false, true, 0, 2);
		// $page = $pdf->importPage(1);
		// $pdf->useTemplate($page);
// todo 消す
$test_text = "
ご寄贈くださいまして誠にありがとうございました。厚くお礼申し上げます。\n
障害者福祉の為に、ご期待に添いますように使わせていただきます。\n
ご厚情を深謝申し上げますとともに、今後とも、何とぞ宜しくご支援の程お願い申し上げます。\n
";

		$pdf->setFontSize(14);

		$pdf->setXY(60, 90, true);
		$pdf->Cell(90, 7, 'お礼状', 'B', 2, 'C');
		$pdf->setFontSize(13);
		$pdf->Cell(90, 30, '金 ' . number_format($item->support_money) . ' 円', 0, 2, 'C');
		$pdf->setFontSize(10);
		$pdf->setLastH(0);
		$pdf->MultiCell(180, 5, $test_text, 0, 'L', false, 2, 15);



		$pdf->setFontSize(9);
		$pdf->Text(200,250, date('Y年m月d日', strtotime($date)), false, false, true, 0, 2, 'R');
		$pdf->Text(200,255, '社会福祉法人 京都ライトハウス', false, false, true, 0, 2, 'R');
		$pdf->Text(200,260, '理事長  神谷 俊昭', false, false, true, 0, 2, 'R');

		// $pdf->text_vertical(100, 100, '縦書きのテスト1902');

		} // endforeach

		/* PDF を出力します */
		$pdf->Output("letter(" . date('Y/m/d' , strtotime($date)) . ").pdf", "I");
	}


	public function action_test () {

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


	public function action_summary($type = null) {

		$req = str_replace(\Uri::base(), '', substr(\Input::referrer(), 0, strpos(\Input::referrer(), '?')));
		// var_dump($req); die();
		$table = \Request::forge($req)->execute();//?year=' . \input::post('year'))->execute();

		$table = (string)$table . (string)$table . $table;
		//
		//

		$html =
			'<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>'.
			'<style>body{font-family:"ipagp";}</style>'.
			$table .
			'</body></html>';

		//var_dump($html); die();

		$dompdf = \Pdf::factory('dompdf')->init();

		$dompdf = new \DOMPDF();
		$dompdf->load_html($html);
		$dompdf->render();
		$dompdf->stream("sample.pdf", array("Attachment" => 0));

		/*
		switch ($type) {
			case 'support':
				$this->support_summary(\input::post());
				break;
			default :
				echo $type;
				break;
		}
		 */
	}

	protected function support_summary($input_post) {
		$year = \Input::post('year');
	}

}
