<?php if ($activated): ?>
<h1>ユーザ登録完了</h1>
<p><?php echo $activated->display_name ?>さまのユーザ登録を完了しました。</p>
<p>今後とも当サイトをよろしくお願い致します。</p>
<p><a href="<?php echo \Uri::create('auth/login/') ?>" class="button">ログインする</a></p>

<?php elseif ($activated): ?>
<h1>すでにユーザ登録が完了しています</h1>
<p><?php echo $activated->display_name ?>さまのユーザ登録はすでに完了しています。</p>
<p><a href="<?php echo \Uri::create('auth/login/') ?>" class="button">ログインする</a></p>

<?php elseif ($deleted): ?>
<h1>ユーザ登録承認の有効期限を過ぎています</h1>
<p><?php echo $deleted->display_name ?>さまのユーザ登録は期限内に完了されなかったため、現在は承認できません。以下リンクから、再度登録をお願い致します。</p>
<p><a href="<?php echo \Uri::create('auth/registration/?email='.e($deleted->display_name).'&amp;activation_key='.e($deleted->email)) ?>" class="button">再度ユーザ登録をする</a></p>

<?php else: ?>
<h1>ユーザ登録できませんでした</h1>
<p>なんらかの理由でユーザ登録に失敗しました。</p>
<p>届いたメールのリンクが改行して不完全なものになっている場合、改行を取り除いて、再度おためしください。</p>

<?php endif; ?>
