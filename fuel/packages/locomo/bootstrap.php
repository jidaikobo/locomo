<?php
/**
 * Locomo
 *
 * @package    Locomo
 * @version    0.5
 * @author     info@jidaikobo.com
 * @license    MIT License
 * @copyright  2014 jidaikobo Inc., 2014 tsukitsume., 2014 Hinodeya Inc.
 * @link       http://www.jidaikobo.com, http://www.hinodeya-ecolife.com
 */

require_once(__DIR__.'/core/bootstrap/bootstrap.php');

//custom bootstrap
$path = PKGPROJPATH.'/bootstrap/bootstrap.php';
if(file_exists($path)):
	require_once($path);
endif;
