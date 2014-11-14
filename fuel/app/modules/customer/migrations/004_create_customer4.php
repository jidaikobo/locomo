<?php
/*
 * alter table `customers_posts` 関係テーブル化
 */

namespace Fuel\Migrations;
class Create_customer4
{
	public function up()
	{
		$files_dir = DOCROOT . 'fuel/lightstaff_csv/';
		try {
				\DB::start_transaction();

				\DB::query("ALTER TABLE `customers_posts` ADD `post_id` INT NOT NULL AFTER `customer_id`;")->execute();

				\DB::query("UPDATE customers_posts AS c SET c.post_id = (SELECT DISTINCT p.id FROM posts AS p WHERE c.position_gid = p.gid_bk_merged);")->execute();

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
		\DB::query('ALTER TABLE `customers_posts` DROP `post_id`;')->execute();
	}
}


