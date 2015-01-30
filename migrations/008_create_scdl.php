<?php
namespace Fuel\Migrations;
class Create_scdl
{
	public function up()
	{
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
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'is_visible' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
		$query = \DB::delete("lcm_scdls")->execute();
		$query = \DB::query("
INSERT INTO `lcm_scdls` (`id`, `repeat_kb`, `target_month`, `target_day`, `start_date`, `end_date`, `start_time`, `end_time`, `week_kb`, `title_text`, `title_importance_kb`, `title_kb`, `private_kb`, `message`, `group_kb`, `group_detail`, `purpose_kb`, `purpose_text`, `user_num`, `user_id`, `created_at`, `updated_at`, `deleted_at`, `is_visible`) VALUES
(1, 0, 0, 0, '2014-11-27', '2014-11-27', '09:00:00', '12:00:00',  '', '一日だけ', '↑高', '社内', '', '一日だけ', 0, '', '賃室', '', 0, 1, '1970-01-01 09:00:00', '2014-11-27 11:41:56', NULL, 0),
(2, 1, 0, 0, '2014-11-18', '2014-11-28', '13:00:00', '14:00:00',  '', '毎日の予定', '↑高', '社内', '', '毎日の予定', 0, '', '賃室', '', 0, 1, '1970-01-01 09:00:00', '2014-11-27 11:43:05', NULL, 0),
(3, 2, 0, 0, '2014-11-06', '2014-11-20', '15:00:00', '16:00:00',  '', '土日除きます', '↑高', '社内', '', '土日除きます', 0, '', '賃室', '', 0, 1, '1970-01-01 09:00:00', '2014-11-27 11:44:09', NULL, 0),
(4, 3, 0, 0, '2014-11-06', '2014-11-20', '09:00:00', '12:00:00',  '4', '毎週の予定', '↑高', '社内', '', '毎週の予定', 0, '', '賃室', '', 0, 1, '1970-01-01 09:00:00', '2014-11-27 11:46:25', NULL, 0),
(5, 4, 0, 3, '2014-09-01', '2015-03-01', '09:00:00', '14:00:00',  '', '毎月の予定', '↑高', '社内', '', '毎月の予定', 0, '', '賃室', '', 0, 1, '1970-01-01 09:00:00', '2014-11-27 11:47:19', NULL, 0),
(6, 5, 12, 24, '2013-11-01', '2018-11-01', '15:00:00', '16:00:00',  '', '毎年のクリスマスイベント', '↑高', '社内', '', '毎年のクリスマスイベント', 0, '', '賃室', '', 0, 1, '1970-01-01 09:00:00', '2014-11-27 11:50:14', NULL, 0);
		", \DB::INSERT);
		$query->execute();

		\DBUtil::create_table('lcm_scdls_buildings', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'schedule_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'building_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));

		\DB::query("
		INSERT INTO items (
			`id` ,
			`category` ,
			`sub_category` ,
			`name` ,
			`data` ,
			`seq` ,
			`is_memo` ,
			`is_status`
			)
			VALUES (
			NULL ,  'schedule_building',  '',  '施設1',  '',  '1',  '0',  '0'
			), (
			NULL ,  'schedule_building',  '',  '施設2',  '',  '2',  '0',  '0'
		);
		")->execute();

		\DBUtil::create_table('lcm_scdls_members', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'schedule_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'user_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));
		\DBUtil::create_table('lcm_scdls_items', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'item_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'item_name' => array('type' => 'text'),
			'item_group' => array('type' => 'text'),
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
				'item_name' => '毎年',
				'item_group' => 'repeat_kb',
				'item_sort' => 6,
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
				'item_id' => 1,
				'item_name' => '施設1',
				'item_group' => 'building',
				'item_sort' => 1,
				'created_at' => \DB::expr("NOW()"),
				'updated_at' => \DB::expr("NOW()")
			))->execute();
		\DB::insert("lcm_scdls_items")->set(array(
				'item_id' => 2,
				'item_name' => '施設2',
				'item_group' => 'building',
				'item_sort' => 2,
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

		\DBUtil::create_table('lcm_scdls_attends', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'schedule_id' => array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'attend_kb'	=> array('constraint' => 11, 'type' => 'int', 'default' => '0'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'updated_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('lcm_scdls');
		\DBUtil::drop_table('lcm_scdls_buildings');
		\DB::delete("items")->where("category", "schedule_building")->execute();
		\DBUtil::drop_table('lcm_scdls_members');
		\DBUtil::drop_table('lcm_scdls_items');
		\DBUtil::drop_table('lcm_scdls_attends');

	}
}
