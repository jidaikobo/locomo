<?php
namespace Fuel\Migrations;
class Create_Usr
{
	public function up()
	{
		// lcm_usr_users
		echo "create lcm_usr_users table.\n";
		\DBUtil::create_table('lcm_usrs', array(
			'id'                => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'username'          => array('type' => 'varchar', 'constraint' => 50),
			'password'          => array('type' => 'varchar', 'constraint' => 255),
			'email'             => array('type' => 'varchar', 'constraint' => 255),
			'display_name'      => array('type' => 'varchar', 'constraint' => 255),
			'last_login_at'     => array('type' => 'datetime'),
			'login_hash'        => array('type' => 'varchar', 'constraint' => 255),
			'activation_key'    => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
			'profile_fields'    => array('type' => 'text'),
			'is_visible'        => array('constraint' => 1, 'type' => 'int'),
			'main_usergroup_id' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'deleted_at'        => array('type' => 'datetime', 'null' => true),
			'created_at'        => array('type' => 'datetime', 'null' => true),
			'expired_at'        => array('type' => 'datetime', 'null' => true),
			'updated_at'        => array('type' => 'datetime', 'null' => true),
			'creator_id'        => array('constraint' => 5, 'type' => 'int'),
			'updater_id'        => array('constraint' => 5, 'type' => 'int'),
		), array('id'));

//		\DBUtil::create_index('lcm_usrs', array('username'), 'users_username', 'UNIQUE');
		\DBUtil::create_index('lcm_usrs', array('password'), 'users_password');
		\DBUtil::create_index('lcm_usrs', array('email'), 'users_email', 'UNIQUE');
		\DBUtil::create_index('lcm_usrs', array('login_hash'), 'users_login_hash');
		\DBUtil::create_index('lcm_usrs', array('created_at'), 'users_created_at');
		\DBUtil::create_index('lcm_usrs', array('expired_at'), 'users_expired_at');
		\DBUtil::create_index('lcm_usrs', array('deleted_at'), 'users_deleted_at');
		\DBUtil::create_index('lcm_usrs', array('is_visible'), 'users_is_visible');

		// lcm_usr_admins
		echo "create lcm_usr_admins table.\n";
		\DBUtil::create_table('lcm_usr_admins', array(
			'id'            => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true), // to use ORM
			'user_id'       => array('type' => 'int', 'constraint' => 11),
			'username'      => array('type' => 'varchar', 'constraint' => 50),
			'last_login_at' => array('type' => 'datetime'),
			'login_hash'    => array('type' => 'varchar', 'constraint' => 255),
			'deleted_at'    => array('type' => 'datetime', 'null' => true), // to use ORM
		), array('id'));
		\DBUtil::create_index('lcm_usr_admins', array('user_id'), 'user_admins_user_id');
		\DBUtil::create_index('lcm_usr_admins', array('username'), 'user_admins_username');

		// lcm_usr_logs
		echo "create lcm_usr_logs table.\n";
		\DBUtil::create_table('lcm_usr_logs', array(
			'loginlog_id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'login_id'    => array('constraint' => 255, 'type' => 'varchar'),
			'login_pass'  => array('constraint' => 255, 'type' => 'varchar'),
			'status'      => array('constraint' => 11, 'type' => 'int'),
			'ipaddress'   => array('constraint' => 255, 'type' => 'varchar'),
			'add_at'      => array('type' => 'datetime', 'null' => true),
			'count'       => array('constraint' => 11, 'type' => 'int'),
		), array('loginlog_id'));

		// lcm_usrgrps
		echo "create lcm_usrgrps table.\n";
		\DBUtil::create_table('lcm_usrgrps', array(
			'id'              => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'            => array('constraint' => 50, 'type' => 'varchar'),
			'description'     => array('constraint' => 255, 'type' => 'varchar'),
			'seq'             => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'is_available'    => array('constraint' => 1, 'type' => 'tinyint'),
			'is_for_acl'      => array('constraint' => 1, 'type' => 'tinyint'),
			'customgroup_uid' => array('constraint' => 11, 'type' => 'tinyint', 'null' => true),
		), array('id'));
		\DBUtil::create_index('lcm_usrgrps', array('seq'), 'usergroups_seq');
		\DBUtil::create_index('lcm_usrgrps', array('is_available'), 'usergroups_is_available');
		\DBUtil::create_index('lcm_usrgrps', array('customgroup_uid'), 'usergroups_customgroup_uid');

		// lcm_usrs_usrgrps
		echo "create lcm_usrs_usrgrps table.\n";
		\DBUtil::create_table('lcm_usrs_usrgrps', array(
			'user_id'  => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'group_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
		), array('user_id','group_id'));
	}

	public function down()
	{
		echo "drop usr related tables table.\n";
		\DBUtil::drop_table('lcm_usrs');
		\DBUtil::drop_table('lcm_usr_admins');
		\DBUtil::drop_table('lcm_usr_logs');
		\DBUtil::drop_table('lcm_usrgrps');
		\DBUtil::drop_table('lcm_usrs_usrgrps');
		if (\DBUtil::table_exists('lcm_acl_acls')) \DBUtil::truncate_table('lcm_acl_acls');
	}
}
