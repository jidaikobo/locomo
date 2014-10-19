<?php
namespace Fuel\Migrations;
class Create_sample
{
	public function up()
	{
		\DBUtil::create_table('samples', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
			'belongsto_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));


		\DBUtil::create_table('belongsto', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));

		\DBUtil::create_table('hasone', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
			'sample_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));

		\DBUtil::create_table('hasmany', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
			'sample_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));

		\DBUtil::create_table('manymany', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 50, 'type' => 'varchar'),
			'created_at' => array('type' => 'datetime', 'null' => true),
			'expired_at' => array('type' => 'datetime', 'null' => true),
			'deleted_at' => array('type' => 'datetime', 'null' => true),

		), array('id'));


		$query = \DB::insert('manymany');
		$query->columns(array(
			'name',
			'created_at',
			'expired_at',
			'deleted_at',
		));

		$query->values(array(
			'MM_1',
			'2014-10-01 00:00:00',
			'2080-10-01 00:00:00',
			null,
		));
		$query->values(array(
			'MM_2',
			'2014-10-01 00:00:00',
			'2080-10-01 00:00:00',
			null,
		));
		$query->values(array(
			'MM_3',
			'2014-10-01 00:00:00',
			'2080-10-01 00:00:00',
			null,
		));
		$query->values(array(
			'MM_4',
			'2014-10-01 00:00:00',
			'2080-10-01 00:00:00',
			null,
		));

		$query->execute();

		\DBUtil::create_table('samples_manymany', array(
			'sample_id' => array('constraint' => 11, 'type' => 'int'),
			'manymany_id' => array('constraint' => 11, 'type' => 'int'),

		));


	}

	public function down()
	{
		\DBUtil::drop_table('samples');
		\DBUtil::drop_table('belongsto');
		\DBUtil::drop_table('hasone');
		\DBUtil::drop_table('hasmany');
		\DBUtil::drop_table('manymany');
		\DBUtil::drop_table('samples_manymany');
	}
}
