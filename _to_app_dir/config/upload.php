<?php
return array(
	// list of file mime-types that a user is allowed to upload
	'mime_whitelist' => array(
		'aif'  => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'art'  => 'image/x-jg',
		'au'   => 'audio/basic',
		'avi'  => 'video/x-msvideo',
		'bmp'  => 'image/bmp',
		'css'  => 'text/css',
		'csv'  => 'text/comma-separated-values',
		'doc'  => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'gif'  => 'image/gif',
		'gz'   => 'application/x-gzip',
		'htm'  => 'text/html',
		'html' => 'text/html',
		'jaw'  => 'application/jxw',
		'jbw'  => 'application/jxw',
		'jpeg' => 'image/jpeg',
		'jpg'  => 'image/jpeg',
		'js'   => 'text/javascript',
		'jsw'  => 'application/jxw',
		'jtw'  => 'application/jxw',
		'juw'  => 'application/jxw',
		'jxw'  => 'application/jxw',
		'lzh'  => 'application/lha',
		'moov' => 'video/quicktime',
		'mov'  => 'video/quicktime',
		'mp2v' => 'video/x-mpeg2',
		'mpe'  => 'video/mpeg',
		'mpeg'  => 'video/mpeg',
		'mpegv' => 'video/mpeg',
		'mpg'  => 'video/mpeg',
		'mpv'  => 'video/mpeg',
		'mpv2' => 'video/x-mpeg2',
		'pdf'  => 'application/pdf',
		'png'  => 'image/png',
		'ppa'  => 'application/vnd.ms-powerpoint',
		'pps'  => 'application/vnd.ms-powerpoint',
		'ppt'  => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'qt'   => 'video/quicktime',
		'ra'   => 'audio/x-pn-realaudio',
		'ram'  => 'audio/x-pn-realaudio',
		'rtf'  => 'application/msword',
		'snd'  => 'audio/basic',
		'tif'  => 'image/tiff',
		'tgz'  => 'application/x-gzip',
		'txt'  => 'text/plain',
		'vbs'  => 'video/mpeg',
		'wav'  => 'audio/x-wav',
		'xbm'  => 'image/x-xbitmap',
		'xdw'  => 'application/vnd.fujixerox.docuworks',
		'xls'  => 'application/vnd.ms-excel',
		'xls1' => 'application/msexcel',
		'xls2' => 'application/excel',
		'xls3' => 'application/x-excel',
		'xls4' => 'application/x-msexcel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'zip'  => 'application/zip',
		'zip1'  => 'application/x-zip-compressed',
		'zip2'  => 'application/x-compress',

// files for blind person
//		'bes' => '',
//		'bet' => '',
//		'ble' => '',
//		'bls' => '',
//		'bmt' => '',
//		'brl' => '',
//		'bs' => '',
//		'bse' => '',
//		'ebk' => '',
//		'mbd' => '',
//		'mse' => '',
//		'nab' => '',
	),

	/**
	 * uploades dir. default_permission
	 *
	 * 1:reab+download 2:upload (rename file, purge file) 3:create dir 4:rename dir + move dir 5:purge dir
	 * 'int(usergroup_id|user_id)/right',
	 */
	'default_permission' => array(
		'usergroup' => array(
//			'0/1', // donwload files allowed to guest
			'-10/4', // create and delete dir are allowed to logged in user 
		),
		'user' => array(
		),
	),
);


