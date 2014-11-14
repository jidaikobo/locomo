<?php
namespace Help;
class Actionset_Base_Help extends \Actionset_Base
{
//	use \Revision\Traits_Actionset_Base_Revision;
//	use \Workflow\Traits_Actionset_Base_Workflow;

	/**
	 * actionset_sample_action()
	 * to use remove first underscore at the function name
	 */
	public static function _actionset_sample_action($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array("help/sample_action/".$id, '閲覧'));
			$urls = static::generate_anchors('help', 'sample_action', $actions, $obj, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'sample_action',
			'explanation'  => 'explanation of sample_action',
			'order'        => 10,
			'dependencies' => array(
				'sample_action',
			)
		);
		return $retvals;
	}
}
