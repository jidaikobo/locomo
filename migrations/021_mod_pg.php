<?php
namespace Fuel\Migrations;
class Mod_Pg
{
	public function up()
	{
		try {
			\DB::start_transaction();
			echo "ununique pg.path\n";
			\DBUtil::drop_index('lcm_pgs', 'path');

			echo "add pg.lang\n";
			\DB::query("ALTER TABLE `lcm_pgs` ADD `lang` varchar(2) NOT NULL DEFAULT '';")->execute();
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
		try {
			\DB::start_transaction();
			echo "unique pg.path\n";
			\DB::query("ALTER TABLE `lcm_pgs` CHANGE `path` `path` varchar(255) NOT NULL DEFAULT '';")->execute();
			\DBUtil::create_index('lcm_pgs', 'path', 'path');
			echo "drop pg.lang\n";
			\DB::query("ALTER TABLE `lcm_pgs` DROP COLUMN `lang`;")->execute();
		}
		catch (\Database_Exception $e)
		{
			\DB::rollback_transaction();
			echo "error\n";
			throw $e;
			return;
		}
	}
}
