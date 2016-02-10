Subject: 【自動返信】ユーザ登録申請
From: <?php echo \Config::get('site_title').' <'.LOCOMO_ADMIN_MAIL.">\n" ?>

<?php echo $item->display_name ?>さま

このメールは<?php echo \Config::get('site_title') ?>にユーザ登録申請された方への自動返信です。
お心当たりのない場合は、このメールを破棄してください。

登録内容は以下の通りです。

お名前：<?php echo $item->display_name ?>
ユーザ名：<?php echo $item->username ?>
ユーザ名：登録時に設定なさったもの

ユーザ登録申請はサイト管理者に送信されました。
サイト管理者の承認を経て、ログインいただけるようになります。
しばらくお待ちください。

当サイトを今後ともよろしくお願いいたします。

--
<?php echo \Config::get('site_title') ?>
<?php echo \Config::get('slogan') ?>
