<?php

namespace Fuel\Migrations;

class Create_users
{
	public function up()
	{
		\DBUtil::create_table('users', array(
			'id'             => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_name'      => array('constraint' => 50, 'type' => 'varchar'),
			'password'       => array('constraint' => 50, 'type' => 'varchar'),
			'email'          => array('constraint' => 255, 'type' => 'varchar'),
			'activation_key' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
			'status'         => array('constraint' => 20, 'type' => 'varchar'),
			'last_login_at'  => array('type' => 'datetime', 'null' => true),
			'deleted_at'     => array('type' => 'datetime', 'null' => true),
			'created_at'     => array('type' => 'datetime', 'null' => true),
			'expired_at'     => array('type' => 'datetime', 'null' => true),
			'updated_at'     => array('type' => 'datetime', 'null' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('users');
	}
}