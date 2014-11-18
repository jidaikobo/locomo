<?php
namespace Locomo;
class Request extends \Fuel\Core\Request
{
	public $id = false;

	public function __construct($uri, $route = true, $method = null)
	{
		parent::__construct($uri, $route, $method);

		// id suspect
		if(isset($this->method_params[0]) && is_numeric($this->method_params[0]))
		{
			$this->id = $this->method_params[0];
		}
	}
}
