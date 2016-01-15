<?php
namespace Fuel\Migrations;
class Modify_Scdl_Add_Public
{
	public function up()
	{
		try {
			\DB::start_transaction();

			// lcm_scdlにカラムを追加
			echo "lcm_scdlにカラムを追加\n";
			\DB::query('ALTER TABLE `lcm_scdls` ADD `public_start_time` time DEFAULT null;')->execute();
			\DB::query('ALTER TABLE `lcm_scdls` ADD `public_end_time` time DEFAULT null;')->execute();
			\DB::query('ALTER TABLE `lcm_scdls` ADD `public_display` bool DEFAULT null;')->execute();

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
		\DB::query('ALTER TABLE `lcm_scdls` DROP `public_start_time`;')->execute();
		\DB::query('ALTER TABLE `lcm_scdls` DROP `public_end_time`;')->execute();
		\DB::query('ALTER TABLE `lcm_scdls` DROP `public_display`;')->execute();
	}
}
