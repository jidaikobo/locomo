<?php
namespace Locomo

define('MY_ENCODING', 'UTF-8');
define('MM_PER_POINT', (25.4 / 72.0));

class Pdf
{
	require_once(PKGPATH.'locomo/vendor/tcpdf/config/lang/jpn.php');
	require_once(PKGPATH.'locomo/vendor/tcpdf/tcpdf.php');
	require_once(PKGPATH.'locomo/vendor/fpdi/fpdi.php');

	/*
	 * 表組用
	 */
	public function Table()
	{
	}


	/*
	 * @Wrapper of Text
	 */
	public function Txt($options)
	{
		static $options = static::boxWordBuffer($options);
		$defaults = array(
			'x' => 0,
			'y' => 0,
			'txt' => '',
			'fstroke' => false,
			'fclip' => false,
			'ffill' => true,
			'border' => 0,
			'ln' => 0,
			'align' => '',
			'fill' => false,
			'link' => '',
			'stretch' => 0,
			'ignore_min_height' => false,
			'calign' => 'T',
			'valign' => 'M',
			'rtloff' => false
		);

		$options = array_merge($defaults, $options);

		return $this->Txt(
			$options['x'],
			$options['y'],
			$options['txt'],
			$options['fstroke'],
			$options['fclip'],
			$options['ffill'],
			$options['border'],
			$options['ln'],
			$options['align'],
			$options['fill'],
			$options['link'],
			$options['stretch'],
			$options['ignore_min_height'],
			$options['calign'],
			$options['valign']
		);
	}

	/*
	 * @Wrapper of Cell
	 */
	public function Box($options)
	{
		static $options = static::boxWordBuffer($options);

		if (isset($options['x']) and $options['x']) {
			$this->SetX(intval($options['x']));
		}
		if (isset($options['y']) and $options['y']) {
			$this->SetY(intval($options['y']));
		}

		$defaults = array(
			'w',
			'h' => 0,
			'txt' => '',
			'border' => 0,
			'ln' => 0,
			'align' => '',
			'fill' => false,
			'link' => '',
			'stretch' => 0,
			'ignore_min_height' => false,
			'calign' => 'T',
			'valign' => 'M'
		);

		$options = array_merge($defaults, $options);

		return $this->Cell(
			$options['w'],
			$options['h'],
			$options['txt'],
			$options['border'],
			$options['ln'],
			$options['align'],
			$options['fill'],
			$options['link'],
			$options['stretch'],
			$options['ignore_min_height'],
			$options['calign'],
			$options['valign']
		);
	}

	/*
	 * @Wrapper of MultiCell
	 */
	public function multiBox($options)
	{

		static $options = static::boxWordBuffer($options);

		$defaults(
			'w',
			'h',
			'txt',
			'border' => 0,
			'align' => 'J',
			'fill' => false,
			'ln' => 1,
			'x' => '',
			'y' => '',
			'reseth' => true,
			'stretch' => 0,
			'ishtml' => false,
			'autopadding' => true,
			'maxh' => 0,
			'valign' => 'T',
			'fitcell' => false,
		);

		$options = array_merge($defaults, $options);

		return $this->MultiCell(
			$options['w'],
			$options['h'],
			$options['txt'],
			$options['border'],
			$options['align'],
			$options['fill'],
			$options['ln'],
			$options['x'],
			$options['y'],
			$options['reseth'],
			$options['stretch'],
			$options['ishtml'],
			$options['autopadding'],
			$options['maxh'],
			$options['valign'],
			$options['fitcell']
		);
	}

	/*
	 * Box options の key の揺れを吸収
	 */
	protected static function boxWordBuffer($options)
	{
		// key to val
		$buffers = array(
			'width' => 'w',
			'height' => 'h',
			'text' => 'txt',
			'max_height' => 'maxh',
			'maxheight' => 'maxh',
			'cell_align' => 'calign',
			'vertical_align' => 'valign',
			'fit_cell' => 'fitcell',
		);

		foreach ($buffers as $key => $val) {
			if (isset($options[$key]) and !isset($options[$val])) {
				$options[$key] = $options[$val];
			}
		}

		return $options;
	}




	/**
	 * Utilities
	 */

	// 封筒印刷
	// 用紙サイズ某と宛名
	public function envelope(
		$title = null,
		$obj,
		$field_format_default = array(
			'name' => 'name',
			'zip' => 'zip',
			'address' => 'address',
		),
		$width = 150,
		$margins = array(0, 0),
		$cr="",
		$name_align = 'L',
		$change_format = false, // function change format
		$rotate = 0,
		$rotate_x = 115,
		$rotate_y = 0,
		$size = false,
		$orientation = 'P'
	)
	{
	}


	/*
	 * タックシール
	 */
	public function tack_seal(
		$title = '',
		$customers = null,
		$field_format_default = array(
			'name' => 'name',
			'zip' => 'zip',
			'address' => 'address'
		),
		$cell = array(2, 5),
		$margin = array(21, 19, 21, 19), // mergin T, R, B, L, horizontal, vertical
		$cr = "",
		$change_format = false
	) {
	}

	/*
	 * 郵便はがき
	 */

	/*
	$name = array(
		'name1',
		"\n",
		'name2',
	);

	$name = array(
		'name1',
		"\n",
		'name2',
	);

	$zip = array(
		'fields' => array('zip3', "-", 'zip4'),
		'position' => array(array()),
		'position' => array(),
	);

	$values = array(
		array(
			'font-size' => 10,
			'position' => array(20, 40),
			'fields' => array(
				'address1',
				'address2',
				'address3',
			),
			'width' => 100,
		),
		array(
			'font-size' => 20,
			'fields' => array(
				'name'
			),
			'width' => 100,
		),

	);
	 */
	public function Bulk($objects)
	{

		foreach ($values as $key => $val) {
			if ( !isset($val['fields']) ) continue;

			$defaults(
				'w',
				'h',
				'txt',
				'border' => 0,
				'align' => 'J',
				'fill' => false,
				'ln' => 1,
				'x' => '',
				'y' => '',
				'reseth' => true,
				'stretch' => 0,
				'ishtml' => false,
				'autopadding' => true,
				'maxh' => 0,
				'valign' => 'T',
				'fitcell' => false,
			);

			$options = array('ln' => 2);

			if (isset($val['font-size'])) {
				$pdf->setFontSize($val['font-size']);
			}

			if (isset($val['position'])) {
				$x = $y = 0;
				if (isset($val['posiiton'][0])) {
					$options['x'] = $val['posiiton'][0];
				} elseif (isset($val['posiiton']['x'])) {
					$options['x'] = $val['posiiton']['x'];
				}
				if (isset($val['posiiton'][1])) {
					$options['y'] = $val['posiiton'][1];
				} elseif (isset($val['posiiton']['y'])) {
					$options['y'] = $val['posiiton']['y'];
				}
				// $pdf->setXY($x, $y);
			}

			$options['txt'] = '';
			foreach($val['fields'] as $field_name) {
				if (isset($objects->{$field_name})) {
					$options['txt'] .= $objects->{$field_name};
				} else {
					$options['txt'] .= $field_name;
				}
			}

			$this->multiBox($optinos);
		}

	}
}
