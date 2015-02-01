<h1><?php echo $title ?></h1>
<?php
// multipart/form-data なので、消しちゃ駄目。
echo @$breadcrumbs;
echo \Form::open(array('enctype' => 'multipart/form-data'));
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>
