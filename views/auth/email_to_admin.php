Subject: 【自動送信】ユーザ新規登録
From: <?php echo \Config::get('site_title').' <'.LOCOMO_ADMIN_MAIL.">\n" ?>

<?php echo \Config::get('site_title') ?>へのユーザの新規登録がありました。
現在のユーザ数は <?php echo Model_Usr::count('all') ?> です。

登録内容は以下の通りです。

お名前：<?php echo $item->dislay_name ?>
ユーザ名：<?php echo $item->username ?>

--
<?php echo \Config::get('site_title') ?>
<?php echo \Config::get('slogan') ?>