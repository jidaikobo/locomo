<?php
/*
 * create table `customers` 福祉：顧客マスタ.csv
 */
namespace Fuel\Migrations;
class Create_customer
{
	public function up()
	{
		$files_dir = DOCROOT . 'fuel/lightstaff_csv/';
		try {
				\DB::start_transaction();

				\DBUtil::create_table('customers', array(
					'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
					'name' => array('constraint' => 50, 'type' => 'varchar'),
					'kana' => array('constraint' => 50, 'type' => 'varchar'),
					'user_type' => array('constraint' => 25, 'type' => 'varchar'),
					'sex' => array('constraint' => 10, 'type' => 'varchar', 'null' => true),
					'birthday_at' => array('type' => 'date', 'null' => true),
					'representative' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'person_in_charge' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'area_type' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'zip' => array('constraint' => 8, 'type' => 'varchar', 'null' => true),
					'address' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
					'tel' => array('constraint' => 16, 'type' => 'varchar', 'null' => true),
					'fax' => array('constraint' => 16, 'type' => 'varchar', 'null' => true),
					'mobile_phone' => array('constraint' => 16, 'type' => 'varchar', 'null' => true),
					'email' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'company_name' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
					'company_zip' => array('constraint' => 8, 'type' => 'varchar', 'null' => true),
					'company_address' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
					'company_tel' => array('constraint' => 16, 'type' => 'varchar', 'null' => true),
					'company_fax' => array('constraint' => 16, 'type' => 'varchar', 'null' => true),
					'company_email' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'volunteer_insurance_type' => array('constraint' => 15, 'type' => 'varchar'),
					'dm_issue_type' => array('constraint' => 10, 'type' => 'varchar'),
					'dm_zip' => array('constraint' => 8, 'type' => 'varchar', 'null' => true),
					'dm_address' => array('constraint' => 255, 'type' => 'varchar', 'varchar' => '50'),
					'dm_name_1' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'dm_name_2' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'dm_tel' => array('constraint' => 16, 'type' => 'varchar', 'null' => true),
					'memo' => array('type' => 'text', 'null' => true),
					'status' => array('constraint' => 20, 'type' => 'varchar'),
					'is_death' => array('type' => 'bool'),
					'sys_date_at' => array('type' => 'datetime', 'null' => true),
					'sys_name' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'sys_position' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'sys_sub_name' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
					'sys_wf_status' => array('constraint' => 20, 'type' => 'varchar', 'null' => true),

				), array('id'));

				\DBUtil::truncate_table('customers');
				\DB::query("ALTER TABLE `customers` auto_increment = 1")->execute();

				\DB::query('LOAD DATA INFILE "' . $files_dir . '福祉：顧客マスタ.csv" INTO TABLE customers CHARACTER SET "utf8" FIELDS TERMINATED BY "," ENCLOSED BY \'"\' IGNORE 1 LINES;')->execute();

				\DB::query('ALTER TABLE `customers` ADD `created_at` DATETIME NULL DEFAULT NULL ,ADD `expired_at` DATETIME NULL DEFAULT NULL , ADD `updated_at` DATETIME NULL DEFAULT NULL , ADD `deleted_at` DATETIME NULL DEFAULT NULL ;')->execute();
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
		\DBUtil::drop_table('customers');
	}
}
