<?php
namespace Fuel\Migrations;
class Create_Scdl
{
	public function up()
	{
		// lcm_scdls
		echo "create lcm_scdls table.\n";
		\DBUtil::create_table('lcm_scdls', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'repeat_kb' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'target_month' => array('type' => 'int', 'null' => true),
			'target_day' => array('type' => 'int', 'null' => true),
			'start_date' => array('type' => 'date', 'null' => true),
			'end_date' => array('type' => 'date', 'null' => true),
			'start_time' => array('type' => 'time', 'null' => true),
			'end_time' => array('type' => 'time', 'null' => true),
			'delete_day' => array('type' => 'text', 'null' => true),
			'week_kb' => array('type' => 'text', 'default' => '', 'null' => true),
			'week_index' => array('type' => 'int', 'null' => true),
			'title_text' => array('type' => 'text', 'default' => ''),
			'title_importance_kb' => array('type' => 'text'),
			'title_kb' => array('type' => 'text', 'null' => true),
			'provisional_kb' => array('type' => 'text', 'default' => '', 'null' => true),
			'unspecified_kb' => array('type' => 'text', 'default' => '', 'null' => true),
			'allday_kb' => array('type' => 'text', 'default' => '', 'null' => true),
			'private_kb' => array('type' => 'text', 'default' => '', 'null' => true),
			'overlap_kb' => array('type' => 'text', 'default' => '', 'null' => true),
			'message' => array('type' => 'text', 'default' => ''),
			'group_kb' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'group_detail' => array('type' => 'text', 'default' => '', 'null' => true),
			'purpose_kb' => array('type' => 'text'),
			'purpose_text' => array('type' => 'text', 'default' => ''),
			'user_num' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'user_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'attend_flg' => array('constraint' => 11, 'type' => 'int', 'default' => '0', 'null' => true),
			'kind_flg'	=> array('constraint' => 11, 'type' => 'int', 'default' => '1'),
			'creator_id' => array('type' => 'int', 'null' => true, 'default' => -1),
			'updater_id' => array('type' => 'int', 'null' => true, 'default' => -1),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'is_visible' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
		$query = \DB::delete("lcm_scdls")->execute();



		echo "スケジューラにparent_idカラムを追加します。\n";
		if (\DBUtil::field_exists('lcm_scdls', array('parent_id')))
		{
			\DB::query('ALTER TABLE `lcm_scdls` DROP COLUMN `parent_id`;')->execute();
		}
		\DB::query('ALTER TABLE `lcm_scdls` ADD `parent_id` int(10) DEFAULT NULL;')->execute();
		echo "parent_idフィールドを追加しました。\n";



		echo "create lcm_scdls_buildings table.\n";
		\DBUtil::create_table('lcm_scdls_buildings', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'schedule_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'building_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));




		echo "create lcm_scdls_members table.\n";
		\DBUtil::create_table('lcm_scdls_members', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'schedule_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'user_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));
		echo "create lcm_scdls_items table.\n";
		\DBUtil::create_table('lcm_scdls_items', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'item_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'item_name' => array('type' => 'text'),
			'item_group' => array('type' => 'varchar', 'constraint' => 255),
			'item_group2' => array('type' => 'text', 'null' => true),
			'item_sort' => array('type' => 'int'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 0,
				'item_name' => 'なし',
				'item_group' => 'repeat_kb',
				'item_sort' => 1,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 1,
				'item_name' => '毎日',
				'item_group' => 'repeat_kb',
				'item_sort' => 2,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 2,
				'item_name' => '毎日(土日除)',
				'item_group' => 'repeat_kb',
				'item_sort' => 3,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 3,
				'item_name' => '毎週',
				'item_group' => 'repeat_kb',
				'item_sort' => 4,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 4,
				'item_name' => '毎月',
				'item_group' => 'repeat_kb',
				'item_sort' => 5,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 5,
				'item_name' => '毎月(曜日)',
				'item_group' => 'repeat_kb',
				'item_sort' => 6,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 6,
				'item_name' => '毎年',
				'item_group' => 'repeat_kb',
				'item_sort' => 7,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 0,
				'item_name' => '標準',
				'item_group' => 'title_kb',
				'item_sort' => 1,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 1,
				'item_name' => '社内',
				'item_group' => 'title_kb',
				'item_sort' => 2,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 2,
				'item_name' => '社外',
				'item_group' => 'title_kb',
				'item_sort' => 3,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 3,
				'item_name' => '外出',
				'item_group' => 'title_kb',
				'item_sort' => 4,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 4,
				'item_name' => '来社',
				'item_group' => 'title_kb',
				'item_sort' => 5,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 5,
				'item_name' => '個人',
				'item_group' => 'title_kb',
				'item_sort' => 6,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 1,
				'item_name' => '仮予定',
				'item_group' => 'detail_kb',
				'item_sort' => 1,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 2,
				'item_name' => '時間指定なし',
				'item_group' => 'detail_kb',
				'item_sort' => 2,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 3,
				'item_name' => '終日',
				'item_group' => 'detail_kb',
				'item_sort' => 3,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 4,
				'item_name' => '非公開設定',
				'item_group' => 'detail_kb',
				'item_sort' => 4,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 5,
				'item_name' => '時間の重複チェック',
				'item_group' => 'detail_kb',
				'item_sort' => 5,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();


		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 0,
				'item_name' => '日',
				'item_group' => 'week_kb',
				'item_sort' => 1,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 1,
				'item_name' => '月',
				'item_group' => 'week_kb',
				'item_sort' => 2,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 2,
				'item_name' => '火',
				'item_group' => 'week_kb',
				'item_sort' => 3,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 3,
				'item_name' => '水',
				'item_group' => 'week_kb',
				'item_sort' => 4,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 4,
				'item_name' => '木',
				'item_group' => 'week_kb',
				'item_sort' => 5,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 5,
				'item_name' => '金',
				'item_group' => 'week_kb',
				'item_sort' => 6,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 6,
				'item_name' => '土',
				'item_group' => 'week_kb',
				'item_sort' => 7,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 1,
				'item_name' => '↑高',
				'item_group' => 'importance_kb',
				'item_sort' => 1,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 2,
				'item_name' => '→中',
				'item_group' => 'importance_kb',
				'item_sort' => 2,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 3,
				'item_name' => '↓低',
				'item_group' => 'importance_kb',
				'item_sort' => 3,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 1,
				'item_name' => '全グループ',
				'item_group' => 'group_kb',
				'item_sort' => 1,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 2,
				'item_name' => 'グループ指定',
				'item_group' => 'group_kb',
				'item_sort' => 2,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 1,
				'item_name' => '賃室',
				'item_group' => 'purpose_kb',
				'item_sort' => 2,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();

		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 0,
				'item_name' => 'リセット',
				'item_group' => 'attend_kb',
				'item_sort' => 4,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 1,
				'item_name' => '参加',
				'item_group' => 'attend_kb',
				'item_sort' => 1,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 2,
				'item_name' => '仮参加',
				'item_group' => 'attend_kb',
				'item_sort' => 2,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 3,
				'item_name' => '辞退',
				'item_group' => 'attend_kb',
				'item_sort' => 3,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();

		echo "create lcm_scdls_attends table.\n";
		\DBUtil::create_table('lcm_scdls_attends', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'schedule_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'attend_kb'	=> array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));





		echo "create scdls index.\n";
		$index = array('ALTER TABLE `lcm_scdls` ADD INDEX `start_date_index` ( `start_date` )'
					, 'ALTER TABLE `lcm_scdls` ADD INDEX `end_date_index` ( `end_date` )'
					, 'ALTER TABLE `lcm_scdls` ADD INDEX `start_end_date_index` ( `start_date` , `end_date` )'
					, 'ALTER TABLE `lcm_scdls_attends` ADD INDEX `schedule_id_index` ( `schedule_id` )'
					, 'ALTER TABLE `lcm_scdls_attends` ADD INDEX `user_id_index` ( `user_id` )'
					, 'ALTER TABLE `lcm_scdls_buildings` ADD INDEX `schedule_id_index` ( `schedule_id` )'
					, 'ALTER TABLE `lcm_scdls_buildings` ADD INDEX `building_id_index` ( `building_id` )'
					, 'ALTER TABLE `lcm_scdls_items` ADD INDEX `item_id_index` ( `item_id` )'
					, 'ALTER TABLE `lcm_scdls_items` ADD INDEX `item_group_index` ( `item_group` )'
					, 'ALTER TABLE `lcm_scdls_members` ADD INDEX `schedule_id_index` ( `schedule_id` )'
					, 'ALTER TABLE `lcm_scdls_members` ADD INDEX `user_id_index` ( `user_id` )');
		foreach ($index as $v) {
			\DB::query($v)->execute();
		}


	}

	public function down()
	{
		// 追加フィールドを消す
		\DB::query('ALTER TABLE `lcm_scdls` DROP COLUMN `parent_id`;')->execute();
		echo "スケジューラのparent_idを削除しました\n";


		echo "drop scdl related tables.\n";
		\DBUtil::drop_table('lcm_scdls');
		\DBUtil::drop_table('lcm_scdls_buildings');
//		\DB::delete("items")->where("category", "schedule_building")->execute();
		\DBUtil::drop_table('lcm_scdls_members');
		\DBUtil::drop_table('lcm_scdls_items');
		\DBUtil::drop_table('lcm_scdls_attends');

	}
}
