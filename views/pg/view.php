<table class="tbl">

<?php if ($item->title): ?>
<tr>
	<th><?php echo __('title') ?></th>
	<td><?php echo $item->title; ?></td>
</tr>
<?php endif; ?>

<?php if ($item->path): ?>
<tr>
	<th>ファイル名</th>
	<td><?php echo $item->path; ?></td>
</tr>
<?php endif; ?>

<?php if ($item->lang): ?>
<tr>
	<th>言語</th>
	<td><?php echo $item->lang; ?></td>
</tr>
<?php endif; ?>

<?php if ($item->url): ?>
<tr>
	<th>リダイレクト先</th>
	<td><?php echo $item->url; ?></td>
</tr>
<?php endif; ?>

<?php if ($item->summary): ?>
<tr>
	<th>要旨</th>
	<td><?php echo $item->summary; ?></td>
</tr>
<?php endif; ?>

<?php if ($item->content): ?>
<tr>
	<th>本文</th>
	<td><?php echo $item->content; ?></td>
</tr>
<?php endif; ?>

<?php if ($item->created_at): ?>
<tr>
	<th>作成日</th>
	<td><?php echo $item->created_at; ?></td>
</tr>
<?php endif; ?>

</table>
