<?php
return array(
	'nicename'      => 'xxx',         //モジュール名称
	'adminindex'    => 'index_admin', //モジュールの管理者向けインデクス
	'is_admin_only' => true,          //trueだと、aclの候補にならず、かつ管理者向けメニューにか表示されなくなります
	'order_in_menu' => 100,           //ログイン後のメニューの表示順。小さいほど上
);
