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
		$ext = strtolower(substr($file, strrpos($file, '.') + 1));

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

	/**
	 * get_attached_files()
	 * 自動生成画像の拡張子など、将来的に汎用化する
	 */
	public static function get_attached_files($dir, $id = false, $types = array(), $exclude_types = array())
	{
		$retvals = array();
		$id = $id ?: \Request::main()->id;
		$dir = \Inflector::add_tailing_slash($dir);
		$upload_path = LOCOMOUPLOADPATH.DS.$dir.$id;
		$save_path = 'uploads/'.$dir.$id.DS;
		$files = is_dir($upload_path) ? \File::read_dir($upload_path, 1) : array();
		if ($files)
		{
			foreach ($files as $file)
			{
				if($types && ! in_array(static::get_file_genre($file), $types)) continue;
				if($exclude_types && in_array(static::get_file_genre($file), $exclude_types)) continue;
				if(in_array(substr($file, -7, 7), array('_lg.jpg','_sm.jpg','_tn.jpg'))) continue;
				$retvals[$save_path.$file] = rawurldecode($file);
			}
		}
		return $retvals;
	}

	/**
	 * attach()
	 * simple uploader
	 * TODO: 汎用化を進めるためにconfigを参照するようにする
	 */
	public static function attach($dir, $obj)
	{
		if ( ! $obj instanceof \Orm\Model) return null;
		if ( ! \Input::post() || ! $obj || ! \Input::file()) return null;

		// vals
		$errors = array();
		$id = $obj->id;
		$dir = \Inflector::add_tailing_slash($dir);
		$upload_path = LOCOMOUPLOADPATH.DS.$dir.$id;
		$save_path = 'uploads'.DS.$dir.$id.DS;

		if ( ! is_dir($upload_path))
		{
			mkdir($upload_path, 0777);
		}
		$config = array(
			'path' => $upload_path,
			'auto_rename' => false,
			'overwrite' => true,
		);
		\Upload::process($config);
		\Upload::register('before', function (&$file){$file['filename'] = urlencode($file['filename']);});

		// upload
		$files = \Upload::get_files();
		\Upload::save($upload_path, array_keys($files));

		// retouch
		$files = is_dir($upload_path) ? \File::read_dir($upload_path, 1) : array();
		if ($files)
		{
			foreach ($files as $file)
			{
				if( ! in_array(substr($file, -4, 4), array('.jpg','jpeg','.gif','.png'))) continue;
				if(in_array(substr($file, -7, 7), array('_lg.jpg','_sm.jpg','_tn.jpg'))) continue;

				$sizes = \Image::sizes($upload_path.DS.$file);

				// large image
				if ($sizes->width <= 1600)
				{
					\Image::load($upload_path.DS.$file)
																	 ->save_pa('', '_lg', 'jpg');
				}
				else
				{
					\Image::load($upload_path.DS.$file)
																	 ->resize(1600)
																	 ->config('bgcolor', '#ffffff')
																	 ->save_pa('', '_lg', 'jpg');
				}

				// small image
				\Image::load($upload_path.DS.$file)
																 ->resize(400)
																 ->config('bgcolor', '#ffffff')
																 ->save_pa('', '_sm', 'jpg');

				// thumbnail
				\Image::load($upload_path.DS.$file)
																 ->crop_resize(400, 400)
																 ->config('bgcolor', '#ffffff')
																 ->save_pa('', '_tn', 'jpg');
			}
		}
	}

	/**
	 * unlink()
	 * こちらも将来的に汎用化を進める
	 */
	public static function unlink($obj)
	{
		if ( ! $obj instanceof \Orm\Model) return null;

		$unlinks = array();

		// unlink
		if (\Input::post('unlink') && $obj)
		{
			foreach (\Input::post('unlink') as $path)
			{
				\File::delete($path);
				// 自動生成される画像の削除
				foreach(array('_lg.jpg','_sm.jpg','_tn.jpg') as $suffix)
				{
					$ext = substr($path, strrpos($path, '.'));
					$pathtemp = str_replace(substr($path, strrpos($path, '.')), $suffix, $path);
					if (file_exists($pathtemp)) \File::delete($pathtemp);
					// $unlinks['failed'][] = $pathtemp; // 削除に失敗したものを保存予定
					$unlinks['deleted'][] = $pathtemp;
				}
			}
			return $unlinks;
		}
	}
}
