<?php
namespace Fuel\Tasks;
class Backup
{
	public function dump()
	{

		$conf = \Config::load('db');

		/*
		# backup production env.
		$dbname=$conf['connection']['username'];
		$dbpass=$conf['connection']['password'];
		$username=$conf['connection']['username']

		mysqldump -u $username --password=$dbpass --hex-blob --extended-insert --add-locks --add-drop-table --lock-tables --disable-keys --quote-names --default-character-set=utf8 $dbname > /var/www/kyoto-lighthouse.org/backups/$dbname-`date +%Y%m%d`.dump

		# backup staging env.
		dbname="lightstaff_dev"

		mysqldump -u $username --password=$dbpass --hex-blob --extended-insert --add-locks --add-drop-table --lock-tables --disable-keys --quote-names --default-character-set=utf8 $dbname > /var/www/www.kyoto-lighthouse.org/backups/$dbname-`date +%Y%m%d`.dump
		echo 'hoge';
		 */
	}
}
