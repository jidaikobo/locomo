<h1><?php echo $title ?></h1>

<?php
echo \Form::open(array('enctype' => 'multipart/form-data', 'class' => 'lcm_form form_group'));
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>


<style>
img
{
	max-width: 100px;
}
</style>
