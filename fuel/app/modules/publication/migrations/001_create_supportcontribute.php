<?php
namespace Fuel\Migrations;
class Create_support
{
	public function up()
	{
		\DBUtil::create_table('supportcontributes', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'payment_id' => array('constraint' => 11, 'type' => 'int'),
			'receipt_at' => array('type' => 'datetime'),
			'customer_id' => array('constraint' => 11, 'type' => 'int'),
			'support_type' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'subject_id' => array('constraint' => 11, 'type' => 'int'),
			'support_money' => array('constraint' => 11, 'type' => 'int'),
			'fee' => array('constraint' => 11, 'type' => 'int'),
			'support_article' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'article_delivery_gid' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'consignee_type' => array('constraint' => 255, 'type' => 'varchar'),
			'support_aim' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'memo' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'is_letter_of_thanks' => array('type' => 'bool', 'null' => true),
			'send_letter_of_thanks_at' => array('type' => 'datetime', 'null' => true),
			'classification' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'entry_at' => array('type' => 'datetime', 'null' => true),
			'entry_user' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'entry_uid' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'update_at' => array('type' => 'datetime', 'null' => true),
			'update_user' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'update_uid' => array('constraint' => 255, 'type' => 'varchar', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),
			'is_contribuer' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('supports');
	}
}
