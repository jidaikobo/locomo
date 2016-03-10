Subject: 【ユーザ登録】ユーザ登録申請
From: <?php echo \Config::get('site_title').' <'.LOCOMO_ADMIN_MAIL.">\n" ?>

<?php echo \Config::get('site_title') ?>へのユーザ登録申請がありました。

登録内容を確認の上、以下リンクをクリックして、登録を完了してください。
ユーザには、このリンクは送付されませんので、ご注意ください。
<?php echo \Uri::create('/auth/actibate/'.$item->activation_key.DS.$item->email) ?>

登録内容は以下の通りです。

お名前：<?php echo $item->display_name."\n" ?>
ユーザ名：<?php echo $item->username."\n" ?>

-- 
<?php echo \Config::get('site_title')."\n" ?>
<?php echo \Config::get('slogan') ?>
