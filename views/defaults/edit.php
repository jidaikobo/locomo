<h1><?php echo $title ?></h1>
<?php
echo \Form::open(array('action' => \Uri::create(\Uri::current(), array(), \Input::get())));
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>
