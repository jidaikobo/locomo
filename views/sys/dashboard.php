<h1 class="skip">ダッシュボード</h1>
<?php foreach ($actions as $action): ?>
<div class="widget size<?php echo $action['size'] ?> <?php echo $action['blockname'] ?> lcm_focus" title="<?php echo $action['title'] ?>" >
	<h1 class="widget_title lcmbar_top lcmbar_top_title"><?php echo $action['title'] ?></h1>
	<div class="widget_content">
		<?php echo $action['content']; ?>
	</div><!-- /.widget_content -->
</div>
<?php endforeach; ?>
