<?php
// multipart/form-data なので、消しちゃ駄目。
echo \Form::open(array('enctype' => 'multipart/form-data'));
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>
