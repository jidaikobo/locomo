<?php
namespace Fuel\Migrations;
class Create_users
{
	public function up()
	{
		// table users
		\DBUtil::create_table('users', array(
			'id'             => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'username'       => array('type' => 'varchar', 'constraint' => 50),
			'password'       => array('type' => 'varchar', 'constraint' => 255),
			'email'          => array('type' => 'varchar', 'constraint' => 255),
			'display_name'   => array('type' => 'varchar', 'constraint' => 255),
			'last_login_at'  => array('type' => 'datetime'),
			'login_hash'     => array('type' => 'varchar', 'constraint' => 255),
			'activation_key' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
			'profile_fields' => array('type' => 'text'),
			'is_visible'     => array('constraint' => 1, 'type' => 'int'),
			'deleted_at'     => array('type' => 'datetime', 'null' => true),
			'created_at'     => array('type' => 'datetime', 'null' => true),
			'expired_at'     => array('type' => 'datetime', 'null' => true),
			'updated_at'     => array('type' => 'datetime', 'null' => true),
			'creator_id'     => array('constraint' => 5, 'type' => 'int'),
			'modifier_id'    => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
		\DBUtil::create_index('users', array('username', 'email'), 'username', 'UNIQUE');

		// table users
		\DBUtil::create_table('users_admins', array(
			'username'      => array('type' => 'varchar', 'constraint' => 50),
			'last_login_at' => array('type' => 'datetime'),
			'login_hash'    => array('type' => 'varchar', 'constraint' => 255),
		), array('username'));

		//users_logs
		\DBUtil::create_table('users_logs', array(
			'loginlog_id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'login_id'    => array('constraint' => 255, 'type' => 'varchar'),
			'login_pass'  => array('constraint' => 255, 'type' => 'varchar'),
			'status'      => array('constraint' => 11, 'type' => 'int'),
			'ipaddress'   => array('constraint' => 255, 'type' => 'varchar'),
			'add_at'      => array('type' => 'datetime', 'null' => true),
			'count'       => array('constraint' => 11, 'type' => 'int'),
		), array('loginlog_id'));

		//usergroups
		\DBUtil::create_table('usergroups', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'         => array('constraint' => 50, 'type' => 'varchar'),
			'description'  => array('constraint' => 255, 'type' => 'varchar'),
			'order'        => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'is_available' => array('constraint' => 1, 'type' => 'tinyint'),
			'deleted_at'   => array('type' => 'datetime', 'null' => true),
		), array('id'));

		//usergroups_r
		\DBUtil::create_table('usergroups_r', array(
			'user_id'   => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'group_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
		), array('user_id','group_id'));

		//default acl
/*
		$query = \DB::insert('acls');
		$query->columns(array(
			'controller',
			'action',
			'owner_auth',
		));
		$query->values(array('user','view',1,));
		$query->values(array('user','edit',1,));
		$query->execute();
*/
	}

	public function down()
	{
		\DBUtil::drop_table('users');
		\DBUtil::drop_table('users_admins');
		\DBUtil::drop_table('users_logs');
		\DBUtil::drop_table('usergroups');
		\DBUtil::drop_table('usergroups_r');
		if(\DBUtil::table_exists('acls')) \DBUtil::truncate_table('acls');
	}
}