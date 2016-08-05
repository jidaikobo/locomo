<h1><?php echo $title ?></h1>
<?php
echo \Form::open(array('action' => \Uri::create(\Uri::current(), array(''), \Input::get()), 'class'=>'lcm_form form_group'));
echo $form;
echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token());
echo \Form::close();
?>

<h2>既読ユーザー</h2>
<?php if ($users = \Arr::assoc_to_keyval($item->opened, 'id', 'display_name')): ?>
	<?php echo implode(', ', $users); ?>
<?php endif; ?>

<?php
echo \Presenter_Msgbrd_View::parents($item->parent);
?>
