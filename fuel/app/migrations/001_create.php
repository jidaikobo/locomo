<?php
/*
 * create items 項目マスタ
 */
namespace Fuel\Migrations;
class Create
{
	public function up()
	{
		$files_dir = DOCROOT . 'fuel/lightstaff_csv/';
		try {
			\DB::start_transaction();

			\DBUtil::create_table('items', array(
				'id' => array('' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
				'category' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'sub_category' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'data' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'seq' => array('constraint' => 11, 'type' => 'int'),
				'is_memo' => array('type' => 'bool', 'default' => 0),
				'is_status' => array('type' => 'bool', 'default' => 0),
			), array('id'));

			\DB::query('LOAD DATA INFILE "' . $files_dir . '福祉：項目マスタ.csv" INTO TABLE items CHARACTER SET "utf8" FIELDS TERMINATED BY "," ENCLOSED BY \'"\' IGNORE 1 LINES;')->execute();

			\DB::query('ALTER TABLE `items` ADD `created_at` DATETIME NULL DEFAULT NULL ,ADD `expired_at` DATETIME NULL DEFAULT NULL , ADD `updated_at` DATETIME NULL DEFAULT NULL , ADD `deleted_at` DATETIME NULL DEFAULT NULL ;')->execute();
		}
		catch (Exception $e) {
			DB::rollback_transaction();
			throw $e;
			return;
		}

		\DB::commit_transaction();
	}


	public function down()
	{
		\DBUtil::drop_table('items');
	}
}




