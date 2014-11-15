<?php
/*
 * create banks 金融機関
 * create banks_posts 金融機関使用
 */
namespace Fuel\Migrations;
class Create2
{
	public function up()
	{
		$files_dir = DOCROOT . 'fuel/lightstaff_csv/';
		try {
			\DB::start_transaction();

			\DBUtil::create_table('banks', array(
				'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
				'type' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'bank_number' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'branch_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'branch_kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'btanch_number' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'deposit_type' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'sign' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'sign_bottom' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'account_number' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'holder_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'holder_kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'is_status' => array('type' => 'bool', 'default' => 0),
			), array('id'));


		\DB::query('LOAD DATA INFILE "' . $files_dir . '福祉：金融機関.csv" INTO TABLE banks CHARACTER SET "utf8" FIELDS TERMINATED BY "," ENCLOSED BY \'"\' IGNORE 1 LINES;')->execute();

		\DB::query('ALTER TABLE `banks` ADD `created_at` DATETIME NULL DEFAULT NULL ,ADD `expired_at` DATETIME NULL DEFAULT NULL , ADD `updated_at` DATETIME NULL DEFAULT NULL , ADD `deleted_at` DATETIME NULL DEFAULT NULL ;')->execute();

			\DBUtil::create_table('banks_posts', array(
				'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
				'bank_id' => array('constraint' => 11, 'type' => 'int'),
				'post_id' => array('constraint' => 11, 'type' => 'int'),
				'use_type' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'use_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			), array('id'));

		\DB::query('LOAD DATA INFILE "' . $files_dir . '福祉：金融機関利用.csv" INTO TABLE banks_posts CHARACTER SET "utf8" FIELDS TERMINATED BY "," ENCLOSED BY \'"\' IGNORE 1 LINES;')->execute();


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
		\DBUtil::drop_table('banks');
		\DBUtil::drop_table('banks_posts');
	}
}




