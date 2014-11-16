<?php
/*
 * alter table `customers_posts` 関係テーブル化
 */

namespace Fuel\Migrations;
class Create_dmrelation
{
	public function up()
	{
		$files_dir = DOCROOT . 'fuel/lightstaff_csv/';

				\DBUtil::create_table('customers_items_dm', array(
					'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
					'customer_id' => array('constraint' => 11, 'type' => 'int'),
					'name_id' => array('constraint' => 11, 'type' => 'int'),
					'name' => array('constraint' => 50, 'type' => 'varchar'),
					'medium_id' => array('constraint' => 11, 'type' => 'int'),
					'medium_type' => array('constraint' => 50, 'type' => 'varchar'),
					'issue_id' => array('constraint' => 11, 'type' => 'int'),
					'issue_type' => array('constraint' => 50, 'type' => 'varchar'),
					'memo' => array('constraint' => 50, 'type' => 'varchar'),
					'memo_2' => array('constraint' => 50, 'type' => 'varchar'),
					'is_status' => array('constraint' => 11, 'type' => 'int'),

				), array('id'));

				\DB::query('LOAD DATA INFILE "' . $files_dir . '福祉：顧客DM設定.csv" INTO TABLE customers_items_dm CHARACTER SET "utf8" FIELDS TERMINATED BY "," ENCLOSED BY \'"\' IGNORE 1 LINES;')->execute();

	}

	public function down()
	{
		\DBUtil::drop_table('customers_items_dm');
	}
}


