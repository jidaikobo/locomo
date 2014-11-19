<?php
namespace Locomo;
class Asset_Instance extends \Fuel\Core\Asset_Instance
{
	/*
	 * override render() to use locomo default assets
	 */
	public function render($group = null, $raw = false)
	{
		$retval = parent::render($group, $raw);

		if(strpos($retval, APPPATH) !== false)
		{
			$search = APPPATH.'locomo/assets';
			$replace = 'content/fetch_view';
		}
		else
		{

/*
echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">' ;
var_dump( $retval ) ;
var_dump( LOCOMOPATH ) ;
var_dump( APPPATH ) ;
echo '</textarea>' ;
*/
			$search = LOCOMOPATH.'assets';
			$replace = 'content/fetch_view';
		}
		return str_replace($search, $replace, $retval);
	}
}
