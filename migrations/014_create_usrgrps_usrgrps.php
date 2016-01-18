<?php
namespace Fuel\Migrations;
class Create_Usrgrps_Usrgrps
{
	public function up()
	{
		try {
			\DB::start_transaction();

			// lcm_usrs_usrgrps
			echo "create lcm_usrgrps_usrgrps table.\n";
			\DBUtil::create_table('lcm_usrgrps_usrgrps', array(
				'group_id_from'  => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
				'group_id_to' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			), array('group_id_from','group_id_to'));

			\DB::commit_transaction();
		}
		catch (\Database_Exception $e)
		{
			\DB::rollback_transaction();
			echo "error\n";
			throw $e;
			return;
		}

	}

	public function down()
	{
		\DBUtil::drop_table('lcm_usrgrps_usrgrps');
	}
}
