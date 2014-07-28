<?php
/**
 * Kontiki
 *
 * @package    Kontiki
 * @version    1
 * @author     shibata@jidaikobo.com
 * @license    MIT License
 * @copyright  2014 jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

require_once(__DIR__.'/core/bootstrap/bootstrap.php');

//custom bootstrap
$path = __DIR__.'/bootstrap/bootstrap.php';
if(file_exists($path)):
	require_once($path);
endif;
