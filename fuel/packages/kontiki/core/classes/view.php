<?php
namespace Kontiki_Core;
class View extends \Fuel\Core\View
{
	public function set_filename($file)
	{
		//$obj = parent::set_filename($file);
		
		// set find_file's one-time-only search paths
		\Finder::instance()->flash($this->request_paths);

		//jidaikobo - start
		//PROJPATHのviewsを優先検索対象にする
		$request = \Request::active();
		\Finder::instance()->flash(PKGPROJPATH.'views'.DS.$request->module);
		//jidaikobo - end

		// locate the view file
		if (($path = \Finder::search('views', $file, '.'.$this->extension, false, false)) === false)
		{
			throw new \FuelException('The requested view could not be found: '.\Fuel::clean_path($file));
		}

		// Store the file path locally
		$this->file_name = $path;

		return $this;
	}

}
