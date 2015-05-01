<h1><?php echo $title ?></h1>
<?php echo @$breadcrumbs; ?>

<ul>
	<li>ディレクトリを完全に削除します。</li>
	<li>ディレクトリを削除すると、そのディレクトリの中に含まれるものもすべて削除されます。</li>
	<li>この削除は取り消しができません。注意してください。</li>
</ul>

<?php
echo \Form::open(array('class' => 'lcm_form form_group'));
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>
