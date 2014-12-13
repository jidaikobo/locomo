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
			'updater_id'     => array('constraint' => 5, 'type' => 'int'),
		), array('id'));

//		\DBUtil::create_index('users', array('username'), 'users_username', 'UNIQUE');
		\DBUtil::create_index('users', array('password'), 'users_password');
		\DBUtil::create_index('users', array('email'), 'users_email', 'UNIQUE');
		\DBUtil::create_index('users', array('login_hash'), 'users_login_hash');
		\DBUtil::create_index('users', array('created_at'), 'users_created_at');
		\DBUtil::create_index('users', array('expired_at'), 'users_expired_at');
		\DBUtil::create_index('users', array('deleted_at'), 'users_deleted_at');
		\DBUtil::create_index('users', array('is_visible'), 'users_is_visible');

		// table user_admins
		\DBUtil::create_table('user_admins', array(
			'id'            => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true), // to use ORM
			'user_id'       => array('type' => 'int', 'constraint' => 11),
			'username'      => array('type' => 'varchar', 'constraint' => 50),
			'last_login_at' => array('type' => 'datetime'),
			'login_hash'    => array('type' => 'varchar', 'constraint' => 255),
			'deleted_at'    => array('type' => 'datetime', 'null' => true), // to use ORM
		), array('id'));
		\DBUtil::create_index('user_admins', array('user_id'), 'user_admins_user_id');
		\DBUtil::create_index('user_admins', array('username'), 'user_admins_username');

		// user_logs
		\DBUtil::create_table('user_logs', array(
			'loginlog_id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'login_id'    => array('constraint' => 255, 'type' => 'varchar'),
			'login_pass'  => array('constraint' => 255, 'type' => 'varchar'),
			'status'      => array('constraint' => 11, 'type' => 'int'),
			'ipaddress'   => array('constraint' => 255, 'type' => 'varchar'),
			'add_at'      => array('type' => 'datetime', 'null' => true),
			'count'       => array('constraint' => 11, 'type' => 'int'),
		), array('loginlog_id'));

		// usergroups
		\DBUtil::create_table('usergroups', array(
			'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'         => array('constraint' => 50, 'type' => 'varchar'),
			'description'  => array('constraint' => 255, 'type' => 'varchar'),
			'seq'          => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'is_available' => array('constraint' => 1, 'type' => 'tinyint'),
			'deleted_at'   => array('type' => 'datetime', 'null' => true),
		), array('id'));
		\DBUtil::create_index('usergroups', array('seq'), 'usergroups_seq');
		\DBUtil::create_index('usergroups', array('is_available'), 'usergroups_is_available');
		\DBUtil::create_index('usergroups', array('deleted_at'), 'usergroups_deleted_at');

		// user_usergroups
		\DBUtil::create_table('user_usergroups', array(
			'user_id'   => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'group_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
		), array('user_id','group_id'));

		//default acl
		$arr = array(
			array('\\Help\\Controller_Help',   'index_admin', '\\Help\\Controller_Help/index_admin'),
			array('\\Admin\\Controller_Admin', 'home',        '\\Admin\\Controller_Admin/home'),
			array('\\Admin\\Controller_Admin', 'home',        '\\Admin\\Controller_Admin/dashboard'),
			array('\\Admin\\Controller_Admin', 'edit',        '\\Admin\\Controller_Admin/edit'),
			array('\\Admin\\Controller_Admin', 'clock',       '\\Admin\\Controller_Admin/clock'),
			array('\\Admin\\Controller_Admin', 'calendar',    '\\Schedules\\Controller_Schedules/calendar'),
		);
		foreach($arr as $v):
			$slug = serialize(\Locomo\Auth_Acl_Locomoacl::_parse_conditions($v[2]));
			$query = \DB::insert('acls');
			$query->columns(array(
				'controller',
				'action',
				'slug',
				'usergroup_id',
			));
			$query->values(array($v[0], $v[1], $slug, -10));//'-10' means all logged in users
			$query->execute();
		endforeach;
	}

	public function down()
	{
		\DBUtil::drop_table('users');
		\DBUtil::drop_table('user_admins');
		\DBUtil::drop_table('user_logs');
		\DBUtil::drop_table('usergroups');
		\DBUtil::drop_table('user_usergroups');
		if (\DBUtil::table_exists('acls')) \DBUtil::truncate_table('acls');
	}
}
