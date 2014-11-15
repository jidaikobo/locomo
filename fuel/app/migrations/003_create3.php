<?php
/*
 * create posts 部署マスタ
 */
namespace Fuel\Migrations;
class Create3
{
	public function up()
	{
		$files_dir = DOCROOT . 'fuel/lightstaff_csv/';
		echo "新規作成した posts.csv をインポートします\n差分は目視でチェックして下さい\n";
		try {
			\DB::start_transaction();

			\DBUtil::create_table('posts', array(
				'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
				'name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'is_customer' => array('type' => 'bool', 'default' => 0),
				'bk_id' => array('constraint' => 11, 'type' => 'int'),
				'gid_bk_merged' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'sys_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'director_position' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'director_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'zip' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'address' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'tel' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'fax' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'mail' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'url' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'office_no' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'office_details_no' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'office_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'office_kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'employer_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'employer_kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'dept_position' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'dept_position_kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'dept_name' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'depo_kana' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'jigyo_type' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'facility_type' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'area_type' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'reduction_type' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'reduction_max_price' => array('constraint' => 11, 'type' => 'int', 'default' => 0),
				'bank_id' => array('constraint' => 11, 'type' => 'int', 'default' => 0),
				'banking_account_number' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'memo' => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
				'is_status' => array('type' => 'bool', 'default' => 0),
				'allowance' => array('type' => 'float', 'default' => 0),
			), array('id'));

			\DB::query('LOAD DATA INFILE "' . $files_dir . 'posts.csv" INTO TABLE posts CHARACTER SET "utf8" FIELDS TERMINATED BY "," ENCLOSED BY \'"\' IGNORE 1 LINES;')->execute();

			\DB::query('ALTER TABLE `posts` ADD `created_at` DATETIME NULL DEFAULT NULL ,ADD `expired_at` DATETIME NULL DEFAULT NULL , ADD `updated_at` DATETIME NULL DEFAULT NULL , ADD `deleted_at` DATETIME NULL DEFAULT NULL ;')->execute();

			\DB::query('INSERT INTO `banks_posts` (bank_id, post_id) SELECT b.id, p.id FROM `banks` AS b, posts AS p WHERE p.banking_account_number = b.account_number')->execute();
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
		\DBUtil::drop_table('posts');
	}
}



