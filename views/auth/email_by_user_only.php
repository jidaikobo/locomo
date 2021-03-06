Subject: 【自動返信】ユーザ登録申請
From: <?php echo \Config::get('site_title').' <'.LOCOMO_ADMIN_MAIL.">\n" ?>

<?php echo $item->display_name ?>さま

このメールは<?php echo \Config::get('site_title') ?>にユーザ登録申請された方への自動返信です。
お心当たりのない場合は、このメールを破棄してください。

下記リンクで、登録を承認いただけます。
承認いただくまでは、ログインはできません。
下記リンクは<?php echo intval(\Config::get('user_registration_limit_days')) ?>日間程度有効です。

<?php echo \Uri::create('/auth/activation/?key='.urlencode($item->activation_key).'&email='.urlencode($item->email))."\n" ?>

登録内容は以下の通りです。

お名前：<?php echo $item->display_name."\n" ?>
ユーザ名：<?php echo $item->username."\n" ?>
パスワード：登録時に設定なさったもの

当サイトを今後ともよろしくお願いいたします。

<?php echo LOCOMO_MAIL_SIGNATURE ?>
<?php echo \Config::get('site_title')."\n" ?>
<?php echo \Config::get('slogan') ?>
