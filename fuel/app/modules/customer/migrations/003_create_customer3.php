<?php
/*
 * create table `customers_items` ユーザー区分設定
 */
namespace Fuel\Migrations;
class Create_customer3
{
	public function up()
	{
		$files_dir = DOCROOT . 'fuel/lightstaff_csv/';
		try {
				\DB::start_transaction();

				// ユーザー区分設定
				\DBUtil::create_table('customers_items', array(
					'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
					'category' => array('constraint' => 11, 'type' => 'varchar'),
					'object_id' => array('constraint' => 11, 'type' => 'int'),
					'devision_id' => array('constraint' => 11, 'type' => 'int'),
					'devision_name' => array('constraint' => 50, 'type' => 'varchar'),
					'devision_data' => array('constraint' => 50, 'type' => 'varchar'),
					'seq' => array('constraint' => 11, 'type' => 'int'),
					'is_status' => array('constraint' => 11, 'type' => 'int'),

				), array('id'));


				\DBUtil::truncate_table('customers_items');
				\DB::query("ALTER TABLE `customers_items` auto_increment = 1")->execute();

				\DB::query('LOAD DATA INFILE "' . $files_dir . '福祉：ユーザー区分設定.csv" INTO TABLE customers_items CHARACTER SET "utf8" FIELDS TERMINATED BY "," ENCLOSED BY \'"\' IGNORE 1 LINES;')->execute();

				\DB::query('ALTER TABLE `customers_items` ADD `created_at` DATETIME NULL DEFAULT NULL ,ADD `expired_at` DATETIME NULL DEFAULT NULL , ADD `updated_at` DATETIME NULL DEFAULT NULL , ADD `deleted_at` DATETIME NULL DEFAULT NULL ;')->execute();

				\DB::query('ALTER TABLE customers_items ADD INDEX idx_customers_items_cl(category);')->execute();
				\DB::query('ALTER TABLE customers_items ADD INDEX idx_customers_items_ob(object_id);')->execute();

				\DB::query("ALTER TABLE `customers` ADD `support` VARCHAR(255) NULL ;")->execute();
				\DB::query("update customers as c set support = (select u.devision_name from customers_items as u where c.id = u.object_id and u.category = '後援会' );")->execute();

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
		\DBUtil::drop_table('customers_items');
		\DB::query('ALTER TABLE `customers` DROP `support`;')->execute();
	}
}

