<?php
namespace Locomo;
class File extends \Fuel\Core\File
{
	/**
	 * get_file_genre()
	 * mainly to use by Controller_Flr. detect file genre by ext.
	 * ['file', 'txt', 'image', 'audio', 'movie', 'braille', 'doc', 'xls', 'ppt', 'pdf']
	 */
	public static function get_file_genre($file)
	{
		$exts = array(
			'image'   => array('gif','jpg','jpeg','png','bmp','tif','tiff','ai','psd',),
			'audio'   => array('mp3','m4a','asf','asx','au','aif','dig','iff','mid','midi','wav','wma','oga',),
			'movie'   => array('mp4','avi','ogv','mpeg','mpg',),
			'braille' => array('bes', 'bet', 'ble', 'bls', 'bmt', 'brl', 'bs', 'bse', 'ebk', 'mbd', 'mse', 'nab', 'hebk', ),
			'doc'     => array('doc', 'docx',),
			'xls'     => array('xls', 'xlsx',),
			'ppt'     => array('ppt', 'pptx',),
			'pdf'     => array('pdf',),
			'compressed' => array('zip', 'lzh',),
		);
		$ext = substr($file, strrpos($file, '.') + 1);

		// search genre
		$retval = 'file';
		foreach ($exts as $genre => $each_exts)
		{
			if(in_array($ext, $each_exts))
			{
				$retval = $genre;
				break;
			}
		}
		return $retval;
	}
}
