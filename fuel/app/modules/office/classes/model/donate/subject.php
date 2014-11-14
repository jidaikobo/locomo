<?php
namespace Office;
class Model_Donate_Subject extends \Supportcontribute\Model_Supportcontributesubject
{

	public static $_conditions = array(
		'where' => array(
			array('is_contributer' , 0),
		),
	);

}

