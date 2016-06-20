<?php
namespace Fuel\Migrations;
class Fix_Format_Table_Add_Merge
{
	public function up()
	{
		// frmt の has_many のテーブル
		echo "create lcm_frmt_tables table.\n";
		\DBUtil::add_fields('lcm_frmt_table_elements', array(
			'is_merge' => array('type' => 'bool', 'default' => 1,),
		));

	}

	public function down()
	{
		\DBUtil::drop_fields('lcm_frmt_table_elements', array(
			'is_merge',
		));
	}
}
