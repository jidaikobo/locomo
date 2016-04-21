Subject: 【自動返信】ユーザ登録完了
From: <?php echo \Config::get('site_title').' <'.LOCOMO_ADMIN_MAIL.">\n" ?>
return-to: <?php echo LOCOMO_ADMIN_MAIL."\n" ?>

<?php echo $item->display_name ?>さま

このメールは<?php echo \Config::get('site_title') ?>にユーザ登録申請された方への自動返信です。
お心当たりのない場合は、このメールを破棄してください。

ユーザ登録が完了しました。

当サイトを今後ともよろしくお願いいたします。

<?php echo LOCOMO_MAIL_SIGNATURE ?>
<?php echo \Config::get('site_title')."\n" ?>
<?php echo \Config::get('slogan') ?>
