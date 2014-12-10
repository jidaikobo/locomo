<style type="text/css">
.widget
{
	border: 1px #aaa solid;
	float: left;
	height: 210px;
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

</style>

<?php foreach ($actions as $action): ?>
<div class="widget size<?php echo $action['size'] ?>">
<?php echo $action['content']; ?>
</div>
<?php endforeach; ?>
