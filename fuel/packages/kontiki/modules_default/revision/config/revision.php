<?php
return array(
	'nicename'      => 'リビジョン',   //モジュール名称
	'adminindex'    => '',            //モジュールの管理者向けインデクス
	'is_admin_only' => true,          //trueだと、aclの候補にならず、かつ管理者向けメニューにか表示されなくなります
	'order_in_menu' => 100,           //ログイン後のメニューの表示順。小さいほど上

	'revision_interval' => 300, //リビジョンの間隔（秒）
);