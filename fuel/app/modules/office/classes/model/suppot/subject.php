<?php
namespace Office;
class Model_Supporter_Subject extends \Supportcontribute\Model_Supportcontributesubject
{

	public static $_conditions = array(
		'where' => array(
			array('is_contributer' , 1),
		),
	);


}


