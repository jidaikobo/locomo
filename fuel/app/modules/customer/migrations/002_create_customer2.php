<?php
/*
 * create table `customers_posts` 顧客関係部署設定
 */
namespace Fuel\Migrations;
class Create_customer2
{
	public function up()
	{
		$files_dir = DOCROOT . 'fuel/lightstaff_csv/';
		try {
				\DB::start_transaction();

				\DBUtil::create_table('customers_posts', array(
					'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
					'customer_id' => array('constraint' => 11, 'type' => 'int'),
					'position_name' => array('constraint' => 50, 'type' => 'varchar'),
					'position_gid' => array('constraint' => 50, 'type' => 'varchar'),
					'position_type' => array('constraint' => 50, 'type' => 'varchar'),
					'order' => array('constraint' => 11, 'type' => 'int'),
					'is_status' => array('constraint' => 11, 'type' => 'int'),

				), array('id'));


				\DBUtil::truncate_table('customers_posts');
				\DB::query("ALTER TABLE `customers_posts` auto_increment = 1")->execute();

				\DB::query('LOAD DATA INFILE "' . $files_dir . '福祉：顧客関係部署設定.csv" INTO TABLE customers_posts CHARACTER SET "utf8" FIELDS TERMINATED BY "," ENCLOSED BY \'"\' IGNORE 1 LINES;')->execute();

				\DB::query('ALTER TABLE `customers_posts` ADD `created_at` DATETIME NULL DEFAULT NULL ,ADD `expired_at` DATETIME NULL DEFAULT NULL , ADD `updated_at` DATETIME NULL DEFAULT NULL , ADD `deleted_at` DATETIME NULL DEFAULT NULL ;')->execute();
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
		\DBUtil::drop_table('customers_posts');
	}
}

