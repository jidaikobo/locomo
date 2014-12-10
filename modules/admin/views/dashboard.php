<style type="text/css">
.widget
{
	border: 1px #aaa solid;
	float: left;
	height: 220px;
	overflow: auto;
	margin-right: 10px;
}

.size1
{
	width: 30%;
}

.size2
{
	width: 60%;
}

.size3
{
	width: 100%;
}

.widget_title
{
	background-color: #eee;
	margin-bottom: 10px;
}

</style>

<?php foreach ($actions as $action): ?>
<div class="widget size<?php echo $action['size'] ?>">
<div class="widget_title"><?php echo $action['title'] ?></div>
<?php echo $action['content']; ?>
</div>
<?php endforeach; ?>
