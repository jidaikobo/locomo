<?php foreach ($actions as $action): ?>
<section class="widget size<?php echo $action['size'] ?> lcm_focus">
	<h1 class="widget_title lcmbar_top lcmbar_top_title"><?php echo $action['title'] ?></h1>
	<div class="widget_content">
		<?php echo $action['content']; ?>
	</div><!-- /.widget_content -->
</section>
<?php endforeach; ?>
