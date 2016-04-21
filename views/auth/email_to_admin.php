Subject: 【自動送信】ユーザ新規登録
From: <?php echo \Config::get('site_title').' <'.LOCOMO_ADMIN_MAIL.">\n" ?>

<?php echo \Config::get('site_title') ?>へのユーザの新規登録がありました。
現在のユーザ数は <?php echo Model_Usr::count(array('where' => array(array('is_visible' => 1)))) ?> です。

登録内容は以下の通りです。

お名前：<?php echo $item->display_name."\n" ?>
ユーザ名：<?php echo $item->username."\n" ?>

<?php echo LOCOMO_MAIL_SIGNATURE ?>
<?php echo \Config::get('site_title')."\n" ?>
<?php echo \Config::get('slogan') ?>
