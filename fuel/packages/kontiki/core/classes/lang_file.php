<?php
namespace Kontiki_Core;
abstract class Lang_File extends \Fuel\Core\Lang_File
{
	//override find_file
	protected function find_file()
	{
		$paths = array();
		foreach ($this->languages as $lang)
		{
			$paths = array_merge($paths, \Finder::search('lang'.DS.$lang, $this->file, $this->ext, true));
		}

		//jidaikobo - start
		$pkgpath = PKGCOREPATH.'lang'.DS.$lang.DS.$this->file.$this->ext;
		if(file_exists($pkgpath)):
			array_push($paths, $pkgpath);
		endif;

		$pkgpath = PKGPROJPATH.'lang'.DS.$lang.DS.$this->file.$this->ext;
		if(file_exists($pkgpath)):
			array_push($paths, $pkgpath);
		endif;
		//jidaikobo - end

		if (empty($paths))
		{
			throw new \LangException(sprintf('File "%s" does not exist.', $this->file));
		}

		return $paths;
	}


}
