<?php
echo \Form::open(array('action' => \Uri::create(\Uri::current(), array(''), \Input::get()),'class'=>'lcm_form form_group', 'enctype' => 'multipart/form-data'));
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>
