<?php
namespace Fuel\Migrations;
/*
 * main_usergroup_id の削除
 * lightstaff では流さない (コメントアウトする)
 */
class Fix_Drop_Main_Usergroup_Id
{

	public function up()
	{
		\DBUtil::drop_fields('lcm_usrs', array(
			'main_usergroup_id',
		));

	}

	public function down()
	{
		\DBUtil::add_fields('lcm_usrs', array(
			'main_usergroup_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
		));

	}

	
}
