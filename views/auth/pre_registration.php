<?php if ($lang == 'en'): ?>
	<?php if ($type == 'by_user_only'): ?>
	<h1>Sent an email for approval</h1>
	<p>To e-mail address you have entered at the time of registration, we send you an e-mail for approval.</p>
	<p>Please complete the procedure.</p>
	<?php else: ?>
	<h1>Received a user registration application</h1>
	<p>To e-mail address you have entered at the time of registration, we send you an e-mail for confirmation.</p>
	<p>After approval by the site administrator, you will be able to log in. Please wait a moment.</p>
	<?php endif; ?>
<?php else: ?>
	<?php if ($type == 'by_user_only'): ?>
	<h1>承認用のメールをお送りしました</h1>
	<p>登録時に記入したメールアドレス宛に、承認用のメールをお送りしました。</p>
	<p>内容をご確認の上、引き続き、手続きを進めてください。</p>
	<?php else: ?>
	<h1>ユーザ登録申請を承りました</h1>
	<p>登録時に記入したメールアドレス宛に、確認用のメールをお送りしました。</p>
	<p>サイト管理者による承認後、ログインいただけるようになります。少々お待ちください。</p>
	<?php endif; ?>
<?php endif; ?>
